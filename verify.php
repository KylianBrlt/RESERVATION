<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut le fichier de connexion à la base de données
require_once 'functions/getPDO.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifie le token dans la base de données
    $pdo = getPDO();
    $sql = "SELECT * FROM users WHERE token = :token";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Active le compte de l'utilisateur
        $sql = "UPDATE users SET is_verified = 1, token = NULL WHERE token = :token";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['token' => $token])) {
            echo "<p>Email verification successful! You can now <a href='login.php'>login</a>.</p>";
        } else {
            echo "<p>Error verifying email.</p>";
        }
    } else {
        echo "<p>Invalid token.</p>";
    }
} else {
    echo "<p>No token provided.</p>";
}
?>