<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

session_start();


header("Content-Type: application/json");


if (!isset($_SESSION["id_utilisateur"]) || !isset($_SESSION["role"]) || $_SESSION["role"] !== "etudiant") {
    echo json_encode(["erreur" => "Non autorisé. Veuillez vous connecter."]);
    exit();
}


$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    echo json_encode(["erreur" => "Echec de la connexion: " . mysqli_connect_error()]);
    exit();
}


mysqli_set_charset($conn, "utf8mb4");

$id_utilisateur = $_SESSION["id_utilisateur"];
$donnees_dashboard = [];


$sql_user = "SELECT u.nom, u.prenom, e.numero_etudiant 
             FROM Utilisateurs u 
             JOIN Etudiant e ON u.id_utilisateur = e.id_utilisateur 
             WHERE u.id_utilisateur = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $id_utilisateur);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

if ($etudiant = mysqli_fetch_assoc($result_user)) {
    $donnees_dashboard["nom"] = $etudiant["nom"];
    $donnees_dashboard["prenom"] = $etudiant["prenom"];
    $donnees_dashboard["numero_etudiant"] = $etudiant["numero_etudiant"];
}



$sql_cours = "SELECT c.nom_cours, s.heure_debut, s.salle 
              FROM Seance s 
              JOIN Cours c ON s.id_cours = c.id_cours 
              JOIN Inscription i ON c.id_cours = i.id_cours 
              JOIN Etudiant e ON i.id_etudiant = e.id_etudiant 
              WHERE e.id_utilisateur = ? AND s.date >= CURDATE() 
              ORDER BY s.date ASC, s.heure_debut ASC LIMIT 1";
$stmt_cours = mysqli_prepare($conn, $sql_cours);
mysqli_stmt_bind_param($stmt_cours, "i", $id_utilisateur);
mysqli_stmt_execute($stmt_cours);
$result_cours = mysqli_stmt_get_result($stmt_cours);

if ($cours = mysqli_fetch_assoc($result_cours)) {
    $donnees_dashboard["prochain_cours"] = $cours["nom_cours"];
    $donnees_dashboard["prochain_heure"] = substr($cours["heure_debut"], 0, 5);
    $donnees_dashboard["prochain_salle"] = "Salle " . $cours["salle"];
} else {
    $donnees_dashboard["prochain_cours"] = "Aucun cours prévu";
    $donnees_dashboard["prochain_heure"] = "";
    $donnees_dashboard["prochain_salle"] = "";
}


$sql_note = "SELECT n.valeur, c.nom_cours 
             FROM Note n 
             JOIN Cours c ON n.id_cours = c.id_cours 
             JOIN Etudiant e ON n.id_etudiant = e.id_etudiant 
             WHERE e.id_utilisateur = ? 
             ORDER BY n.date_saisie DESC LIMIT 1";
$stmt_note = mysqli_prepare($conn, $sql_note);
mysqli_stmt_bind_param($stmt_note, "i", $id_utilisateur);
mysqli_stmt_execute($stmt_note);
$result_note = mysqli_stmt_get_result($stmt_note);

if ($note = mysqli_fetch_assoc($result_note)) {
    $donnees_dashboard["note_cours"] = $note["nom_cours"];
    $donnees_dashboard["note_valeur"] = $note["valeur"] . " / 20";
} else {
    $donnees_dashboard["note_cours"] = "";
    $donnees_dashboard["note_valeur"] = "Aucune note";
}


echo json_encode($donnees_dashboard);

mysqli_close($conn);
?>