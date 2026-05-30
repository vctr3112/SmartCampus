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

$error = "";

if (isset($_POST["ajouter_cours"])) {
//on récupère les données du formulaire
$nom_cours = isset($_POST["nom_cours"]) ? $_POST["nom_cours"] : "";
$code_cours = isset($_POST["code_cours"]) ? $_POST["code_cours"] : "";
$credits = isset($_POST["credits"]) ? $_POST["credits"] : "";
$capacite_max = isset($_POST["capacite_max"]) ? (int)$_POST["capacite_max"] : 0;
$id_enseignant = isset($_POST["id_enseignant"]) ? (int)$_POST["id_enseignant"] : 0;

//validation des champs
if (empty($nom_cours)) {
$error .= "Le nom du cours est requis.<br>";
}
if (empty($code_cours)) {
$error .= "Le code du cours est requis.<br>";
}
if (empty($capacite_max)) {
$error .= "La capacité maximale est requise.<br>";
}
if (empty($id_enseignant)) {
$error .= "Veuillez sélectionner un enseignant.<br>";
}

if ($error == "") {
//insertion dans la table Cours
$query = "INSERT INTO Cours (id_enseignant, nom_cours, code_cours, credits, capacite_max) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "isssi", $id_enseignant, $nom_cours, $code_cours, $credits, $capacite_max);
mysqli_stmt_execute($stmt);

mysqli_close($conn);

header("Location: gestion_cours.html");
exit();
}
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Ajouter un cours</title>
</head>
<body>
<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>
</body>
</html>
