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

if (isset($_POST["ajouter_etudiant"])) {
$nom = isset($_POST["nom"]) ? $_POST["nom"] : "";
$prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
$niveau = isset($_POST["niveau"]) ? $_POST["niveau"] : "";
$filiere = isset($_POST["filiere"]) ? $_POST["filiere"] : "";
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
if (empty($niveau)) {
$error .= "Le niveau est requis.<br>";
}
if (empty($filiere)) {
$error .= "La filière est requise.<br>";
}

if ($error == "") {
//insertion dans la table utilisateurs
$mot_de_passe_hash = password_hash("SmartCampus2026!", PASSWORD_DEFAULT);
$query_utilisateur = "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'etudiant')";
$stmt = mysqli_prepare($conn, $query_utilisateur);
mysqli_stmt_bind_param($stmt, "ssss", $nom, $prenom, $email, $mot_de_passe_hash);
mysqli_stmt_execute($stmt);

//récupération de l'id créé
$id_utilisateur = mysqli_insert_id($conn);

//insertion dans la table etudiant
$annee_inscription = date("Y");
$query_etudiant = "INSERT INTO Etudiant (id_utilisateur, numero_etudiant, filiere, niveau, annee_inscription) VALUES (?, ?, ?, ?, ?)";
$stmt2 = mysqli_prepare($conn, $query_etudiant);
$numero_etudiant = rand(10000, 99999);
mysqli_stmt_bind_param($stmt2, "iissi", $id_utilisateur, $numero_etudiant, $filiere, $niveau, $annee_inscription);
mysqli_stmt_execute($stmt2);

mysqli_close($conn);

header("Location: gestion_etudiant.html");
exit();
}
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Ajouter un étudiant</title>
</head>
<body>

<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>

</body>
</html>
