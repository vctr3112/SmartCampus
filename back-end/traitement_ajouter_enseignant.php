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

if (isset($_POST["ajouter_enseignant"])) {

//on récupère les données du formulaire
$nom = isset($_POST["nom"]) ? $_POST["nom"] : "";
$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
$departement = isset($_POST["departement"]) ? $_POST["departement"] : "";
$specialite = isset($_POST["specialite"]) ? $_POST["specialite"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";

if (empty($nom)) {
$error .= "Le nom est requis.<br>";
}
if (empty($prenom)) {
$error .= "Le prénom est requis.<br>";
}
if (empty($email)) {
$error .= "L'email est requis.<br>";
}
if (empty($departement)) {
$error .= "Le département est requis.<br>";
}
if (empty($specialite)) {
$error .= "La spécialité est requise.<br>";
}

if ($error == "") {
//insertion dans la table utilisateurs
$mot_de_passe_hash = password_hash("SmartCampus2026!", PASSWORD_DEFAULT);
$query_utilisateur = "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'enseignant')";
$stmt = mysqli_prepare($conn, $query_utilisateur);
mysqli_stmt_bind_param($stmt, "ssss", $nom, $prenom, $email, $mot_de_passe_hash);
mysqli_stmt_execute($stmt);

//on récupère l'id de l'utilisateur créé
$id_utilisateur = mysqli_insert_id($conn);

//insertion dans la table Enseignant
$query_enseignant = "INSERT INTO Enseignant (id_utilisateur, departement, specialite) VALUES (?, ?, ?)";
$stmt2 = mysqli_prepare($conn, $query_enseignant);
mysqli_stmt_bind_param($stmt2, "iss", $id_utilisateur, $departement, $specialite);
mysqli_stmt_execute($stmt2);

mysqli_close($conn);

header("Location: gestion_enseignant.html");
exit();
}
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Ajouter un enseignant</title>
</head>
<body>
<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>
</body>
</html>
