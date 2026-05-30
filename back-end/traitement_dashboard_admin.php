<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

//on démarre la session
session_start();

//on vérifie la session et le rôle
if (!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "admin") {
header("Location: connexion.html");
exit();
}

//connexion à la base de données
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}

//nombre d'étudiants
$result_etudiants = mysqli_query($conn, "SELECT COUNT() AS total FROM Etudiant");
$nb_etudiants = mysqli_fetch_assoc($result_etudiants)["total"];

//nombre d'enseignants
$result_enseignants = mysqli_query($conn, "SELECT COUNT() AS total FROM Enseignant");
$nb_enseignants = mysqli_fetch_assoc($result_enseignants)["total"];

//nombre de cours
$result_cours = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Cours");
$nb_cours = mysqli_fetch_assoc($result_cours)["total"];

header("Content-Type: application/json");
echo json_encode([
    "nb_etudiants" => $nb_etudiants,
    "nb_enseignants" => $nb_enseignants,
    "nb_cours" => $nb_cours
]);

mysqli_close($conn);
?> traitement_dashboard_admin.php