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

//si c'est une requête GET, on renvoie la liste des enseignants
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["enseignants"])) {
$result = mysqli_query($conn, "SELECT id_enseignant, u.nom, u.prenom 
FROM Enseignant e JOIN Utilisateurs u ON e.id_utilisateur = u.id_utilisateur");
$enseignants = [];
while ($row = mysqli_fetch_assoc($result)) {
$enseignants[] = $row;
}
header("Content-Type: application/json");
echo json_encode($enseignants);
exit();
}

//on récupère la liste des cours
$query = "SELECT c.id_cours, c.nom_cours, c.code_cours, c.credits, c.capacite_max, u.nom, u.prenom FROM Cours c
JOIN Enseignant e ON c.id_enseignant = e.id_enseignant JOIN Utilisateurs u ON e.id_utilisateur = u.id_utilisateur";
$result = mysqli_query($conn, $query);

$cours = [];
while ($row = mysqli_fetch_assoc($result)) {
$cours[] = $row;
}

mysqli_close($conn);

header("Content-Type: application/json");
echo json_encode($cours);
?>
