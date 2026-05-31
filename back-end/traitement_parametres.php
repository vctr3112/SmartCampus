<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "SmartCampus";
session_start();

if (!isset($_SESSION["id_utilisateur"])) {
    header("Location: connexion.html");
    exit();
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Echec de la connexion: " . mysqli_connect_error());
}

$id_utilisateur = $_SESSION["id_utilisateur"];

// GET?infos : renvoie nom et prénom
if (isset($_GET["infos"])) {
    $result = mysqli_query($conn, "SELECT nom, prenom FROM Utilisateurs WHERE id_utilisateur = $id_utilisateur");
    $user = mysqli_fetch_assoc($result);
    header("Content-Type: application/json");
    echo json_encode([
        "nom" => $user["nom"],
        "prenom" => $user["prenom"]
    ]);
    mysqli_close($conn);
    exit();
}

$error = "";

if (isset($_POST["modifier"])) {
    $nom = isset($_POST["nom"]) ? $_POST["nom"] : "";
    $prenom = isset($_POST["prenom"]) ? $_POST["prenom"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $mot_de_passe = isset($_POST["mot_de_passe"]) ? $_POST["mot_de_passe"] : "";
    $confirmer = isset($_POST["confirmer_mot_de_passe"]) ? $_POST["confirmer_mot_de_passe"] : "";

    if (empty($nom)) { $error .= "Le nom est requis.<br>"; }
    if (empty($prenom)) { $error .= "Le prénom est requis.<br>"; }
    if (empty($email)) { $error .= "L'email est requis.<br>"; }
    if (!empty($mot_de_passe) && $mot_de_passe !== $confirmer) {
        $error .= "Les mots de passe ne correspondent pas.<br>";
    }

    if ($error == "") {
        if (empty($mot_de_passe)) {
            $query = "UPDATE Utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssi", $nom, $prenom, $email, $id_utilisateur);
        } else {
            $query = "UPDATE Utilisateurs SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? WHERE id_utilisateur = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", $nom, $prenom, $email, $mot_de_passe, $id_utilisateur);
        }
        mysqli_stmt_execute($stmt);
        mysqli_close($conn);
        header("Location: parametres.html");
        exit();
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SmartCampus - Paramètres</title>
</head>
<body>
<?php
if ($error != "") {
    echo "<p style='color:red;'>" . $error . "</p>";
}
?>
</body>
</html>
