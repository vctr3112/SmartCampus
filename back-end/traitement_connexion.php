<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

$email = "";
$mot_de_passe = "";
$error = "";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}

if (isset($_POST["submit"])) {

$email = isset($_POST["email"]) ? $_POST["email"] : "";
$mot_de_passe = isset($_POST["password"]) ? $_POST["password"] : "";

if (empty($email)) {
$error .= "L'email est requis.<br>";
}

if (empty($mot_de_passe)) {
$error .= "Le mot de passe est requis.<br>";
}

//si pas d'erreur on cherche l'utilisateur
if ($error == "") {
$query = "SELECT * FROM Utilisateurs WHERE email = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$utilisateur = mysqli_fetch_assoc($result);

//si l'utilisateur existe pas
if (!$utilisateur) {
$error = "Email ou mot de passe incorrect.";
} else if ($mot_de_passe !== $utilisateur["mot_de_passe"]) {
$error = "Email ou mot de passe incorrect.";
} else {
session_start();
//sauvegarde des données de session
$_SESSION["id_utilisateur"] = $utilisateur["id_utilisateur"];
$_SESSION["nom"] = $utilisateur["nom"];
$_SESSION["prenom"] = $utilisateur["prenom"];
$_SESSION["role"] = $utilisateur["role"];

//redirection selon le rôle
if ($utilisateur["role"] == "etudiant") {
header("Location: dashboard_etudiant.html");
} else if ($utilisateur["role"] == "enseignant") {
header("Location: dashboard_enseignant.html");
} else if ($utilisateur["role"] == "admin") {
header("Location: dashboard_admin.html");
}
exit();
}
}
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Connexion</title>
</head>
<body>

<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>

</body>
</html>
