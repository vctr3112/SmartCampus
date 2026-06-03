<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";

session_start();

if (!isset($_SESSION["id_utilisateur"]) || $_SESSION["role"] !== "enseignant") {
header("Location: connexion.html");
exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
die("Echec de la connexion: " . mysqli_connect_error());
}

//on récupère l'id_enseignant depuis la session
$id_utilisateur = $_SESSION["id_utilisateur"];
$result_ens = mysqli_query($conn, "SELECT id_enseignant FROM Enseignant WHERE id_utilisateur = $id_utilisateur");
$enseignant = mysqli_fetch_assoc($result_ens);
$id_enseignant = $enseignant["id_enseignant"];

//si GET : on renvoie les données en JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {

//on récupère les cours de l'enseignant
if (isset($_GET["cours"])) {
$result_cours = mysqli_query($conn, "SELECT id_cours, nom_cours FROM Cours WHERE id_enseignant = $id_enseignant");
$cours = [];
while ($row = mysqli_fetch_assoc($result_cours)) {
$cours[] = $row;
}
header("Content-Type: application/json");
echo json_encode($cours);
exit();
}

//on récupère les classes (filiere + niveau)
if (isset($_GET["classes"]) && isset($_GET["id_cours"])) {
$id_cours = (int)$_GET["id_cours"];
$result_classes = mysqli_query($conn, "SELECT DISTINCT e.filiere, e.niveau 
FROM Etudiant e JOIN Inscription i ON e.id_etudiant = i.id_etudiant 
WHERE i.id_cours = $id_cours");
$classes = [];
while ($row = mysqli_fetch_assoc($result_classes)) {
$classes[] = $row;
}
header("Content-Type: application/json");
echo json_encode($classes);
exit();
}

//on récupère les étudiants selon le cours et la classe
if (isset($_GET["etudiants"]) && isset($_GET["id_cours"]) && isset($_GET["filiere"]) && isset($_GET["niveau"])) {
$id_cours = (int)$_GET["id_cours"];
$filiere = $_GET["filiere"];
$niveau = $_GET["niveau"];

$query = "SELECT e.id_etudiant, u.nom, u.prenom 
FROM Etudiant e JOIN Utilisateurs u ON e.id_utilisateur = u.id_utilisateur
JOIN Inscription i ON e.id_etudiant = i.id_etudiant WHERE i.id_cours = ? AND e.filiere = ? AND e.niveau = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iss", $id_cours, $filiere, $niveau);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$etudiants = [];
while ($row = mysqli_fetch_assoc($result)) {
$etudiants[] = $row;
}
header("Content-Type: application/json");
echo json_encode($etudiants);
exit();
}
}

//si POST : on enregistre les notes
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
$id_cours = isset($_POST["id_cours"]) ? (int)$_POST["id_cours"] : 0;
$date_examen = isset($_POST["date_examen"]) ? $_POST["date_examen"] : "";
$notes = isset($_POST["notes"]) ? $_POST["notes"] : [];

if (empty($id_cours)) {
$error .= "Le cours est requis.<br>";
}
if (empty($date_examen)) {
$error .= "La date d'examen est requise.<br>";
}
if (empty($notes)) {
$error .= "Aucune note à enregistrer.<br>";
}

if ($error == "") {
foreach ($notes as $id_etudiant => $valeur) {
if ($valeur !== "") {
$id_etudiant = (int)$id_etudiant;
$valeur = (float)$valeur;

//on vérifie si une note existe déjà
$query_check = "SELECT id_note FROM Note WHERE id_etudiant = ? AND id_cours = ?";
$stmt_check = mysqli_prepare($conn, $query_check);
mysqli_stmt_bind_param($stmt_check, "ii", $id_etudiant, $id_cours);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) > 0) {
//mise à jour de la note existante
$query_update = "UPDATE Note SET valeur = ?, date_saisie = ? WHERE id_etudiant = ? AND id_cours = ?";
$stmt_update = mysqli_prepare($conn, $query_update);
mysqli_stmt_bind_param($stmt_update, "dsii", $valeur, $date_examen, $id_etudiant, $id_cours);
mysqli_stmt_execute($stmt_update);
} else {
//on insert une nouvelle note
$query_insert = "INSERT INTO Note (id_etudiant, id_cours, valeur, coefficient, date_saisie) VALUES (?, ?, ?, 1, ?)";
$stmt_insert = mysqli_prepare($conn, $query_insert);
mysqli_stmt_bind_param($stmt_insert, "iids", $id_etudiant, $id_cours, $valeur, $date_examen);
mysqli_stmt_execute($stmt_insert);
}
}
}

mysqli_close($conn);
header("Location: saisie_notes.html");
exit();
}
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>SmartCampus - Saisie des notes</title>
</head>
<body>
<?php
if ($error != "") {
echo "<p style='color:red;'>" . $error . "</p>";
}
?>
</body>
</html>
