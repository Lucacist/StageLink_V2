<?php
$servername = "localhost";
$username = "root";  // Par défaut dans WAMP/XAMPP
$password = "";      // Laisse vide si tu n'as pas défini de mot de passe
$dbname = "StageLink"; 

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
