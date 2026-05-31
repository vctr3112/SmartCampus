<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST["email"])) {

$email = isset($_POST["email"]) ? $_POST["email"] : "";
$mot_de_passe = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : "";
$confirmer = isset($_POST["confirmer_mot_de_passe"]) ? $_POST["confirmer_mot_de_passe"] : "";

if (empty($email)) {
$error .= "L'email est requis.<br>";
}
if (empty($mot_de_passe)) {
$error .= "Le mot de passe est requis.<br>";
}
if ($mot_de_passe !== $confirmer) {
$error .= "Les mots de passe ne correspondent pas.<br>";
}

if ($error == "") {
//on vérifie que l'email existe dans la BDD
$query_check = "SELECT id_utilisateur FROM Utilisateurs WHERE email = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "s", $email);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) == 0) {
$error = "Aucun compte associé à cet email.";
} else {
$query_update = "UPDATE Utilisateurs SET mot_de_passe = ? WHERE email = ?";
$stmt_update = mysqli_prepare($conn, $query_update);
mysqli_stmt_bind_param($stmt_update, "ss", $mot_de_passe, $email);
mysqli_stmt_execute($stmt_update);


mysqli_close($conn);
header("Location: connexion.html");
exit();
}
}
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Réinitialisation du mot de passe</title>
</head>
<body>
<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>
</body>
</html>
