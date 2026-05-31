<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "etudiant") {
echo json_encode(["erreur" => "Non autorise. Veuillez vous connecter."]);
exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
echo json_encode(["erreur" => "Echec de la connexion: " . mysqli_connect_error()]);
exit();
}
mysqli_set_charset($conn, "utf8mb4");

$id_utilisateur = $_SESSION["id_utilisateur"];
$donnees_edt = [];

$sql_user = "SELECT u.nom, u.prenom, e.id_etudiant FROM Utilisateurs u
JOIN Etudiant e ON u.id_utilisateur = e.id_utilisateur WHERE u.id_utilisateur = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $id_utilisateur);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

if ($etudiant = mysqli_fetch_assoc($result_user)) {
$donnees_edt["nom"] = $etudiant["nom"];
$donnees_edt["prenom"] = $etudiant["prenom"];
$donnees_edt["id_etudiant"] = $etudiant["id_etudiant"];
}

$sql_seances = "SELECT c.nom_cours, DATE_FORMAT(s.date, '%d/%m/%Y') as date_fr,
DATE_FORMAT(s.heure_debut, '%H:%i') as heure_debut, DATE_FORMAT(s.heure_fin, '%H:%i') as heure_fin,
s.salle FROM Seance s JOIN Cours c ON s.id_cours = c.id_cours JOIN Inscription i ON c.id_cours = i.id_cours
JOIN Etudiant e ON i.id_etudiant = e.id_etudiant WHERE e.id_utilisateur = ? 
ORDER BY s.date ASC, s.heure_debut ASC LIMIT 20";

$stmt_seances = mysqli_prepare($conn, $sql_seances);
mysqli_stmt_bind_param($stmt_seances, "i", $id_utilisateur);
mysqli_stmt_execute($stmt_seances);
$result_seances = mysqli_stmt_get_result($stmt_seances);

$liste_seances = [];
while ($row = mysqli_fetch_assoc($result_seances)) {
$liste_seances[] = $row;
}

$donnees_edt["seances"] = $liste_seances;

echo json_encode($donnees_edt);
mysqli_close($conn);
?>
