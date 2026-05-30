<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

session_start();

if(!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "admin") {
header("Location: connexion.html");
exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}

//on récupère la liste des enseignants
$query = "SELECT u.nom, u.prenom, e.id_enseignant, e.departement, e.specialite, u.email 
FROM Enseignant e JOIN Utilisateurs u ON e.id_utilisateur = u.id_utilisateur";
$result = mysqli_query($conn, $query);

$enseignants = [];
while ($row = mysqli_fetch_assoc($result)) {
$enseignants[] = $row;
}

mysqli_close($conn);

header("Content-Type: application/json");
echo json_encode($enseignants);
?>
