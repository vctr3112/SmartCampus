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

$query = "SELECT u.nom, u.prenom, e.id_etudiant, e.niveau, e.filiere, u.email 
FROM Etudiant e JOIN Utilisateurs u ON e.id_utilisateur = u.id_utilisateur";
$result = mysqli_query($conn, $query);

$etudiants = [];
while ($row = mysqli_fetch_assoc($result)) {
$etudiants[] = $row;
}

mysqli_close($conn);

header("Content-Type: application/json");
echo json_encode($etudiants);
?>
