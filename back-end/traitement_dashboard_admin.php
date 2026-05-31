<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";
session_start();
if (!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "admin") {
header("Location: connexion.html");
exit();
}
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}
$id_utilisateur = $_SESSION["id_utilisateur"];
//on récupère le nom et le prénom de l'admin
$result_user = mysqli_query($conn, "SELECT nom, prenom FROM Utilisateurs WHERE id_utilisateur = $id_utilisateur");
$user = mysqli_fetch_assoc($result_user);
//nombre d'étudiants
$result_etudiants = mysqli_query($conn, "SELECT COUNT() AS total FROM Etudiant");
$nb_etudiants = mysqli_fetch_assoc($result_etudiants)["total"];
//nombre d'enseignants
$result_enseignants = mysqli_query($conn, "SELECT COUNT() AS total FROM Enseignant");
$nb_enseignants = mysqli_fetch_assoc($result_enseignants)["total"];
//nombre de cours
$result_cours = mysqli_query($conn, "SELECT COUNT(*) AS total FROM Cours");
$nb_cours = mysqli_fetch_assoc($result_cours)["total"];
mysqli_close($conn);
header("Content-Type: application/json");
echo json_encode([
"nom" => $user["nom"],
"prenom" => $user["prenom"],
"id_utilisateur" => $id_utilisateur,
"nb_etudiants" => $nb_etudiants,
"nb_enseignants" => $nb_enseignants,
"nb_cours" => $nb_cours
]);
?>
