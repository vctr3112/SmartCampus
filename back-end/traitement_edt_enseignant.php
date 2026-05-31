<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "enseignant") {
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

//on récupère l'id_enseignant
$result_ens = mysqli_query($conn, "SELECT id_enseignant FROM Enseignant WHERE id_utilisateur = $id_utilisateur");
$enseignant = mysqli_fetch_assoc($result_ens);
$id_enseignant = $enseignant["id_enseignant"];

$donnees_edt = [];

//on récupère les séances de l'enseignant
$sql_seances = "SELECT c.nom_cours, DATE_FORMAT(s.date, '%d/%m/%Y') as date_fr,
DATE_FORMAT(s.heure_debut, '%H:%i') as heure_debut, DATE_FORMAT(s.heure_fin, '%H:%i') as heure_fin,
s.salle FROM Seance s JOIN Cours c ON s.id_cours = c.id_cours WHERE c.id_enseignant = ?
ORDER BY s.date ASC, s.heure_debut ASC LIMIT 20";

$stmt_seances = mysqli_prepare($conn, $sql_seances);
mysqli_stmt_bind_param($stmt_seances, "i", $id_enseignant);
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
