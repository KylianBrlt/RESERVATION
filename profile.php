<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Inclut l'en-tête de la page
require_once 'header.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
    header('Location: login.php');
    exit;
}

// Inclut les fonctions nécessaires
require_once 'functions/getUserInfo.php';

// Récupère les informations de l'utilisateur depuis la base de données
$user = getUserInfo($_SESSION['email']);
$message = '';

if ($user) {
    // Affiche les informations de l'utilisateur
    echo "<h2>Welcome, " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . " !</h2>";
    echo "<p>Here are your details :</p>";
    echo "<ul>";
    echo "<li><strong>email :</strong> " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "</li>";
    echo "</ul>";
} else {
    echo "<p>Error retrieving user information.</p>";
}

// Inclut le pied de page de la page
require_once 'footer.php';
?>