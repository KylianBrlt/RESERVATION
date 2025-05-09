<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut l'en-tête de la page
require 'header.php';

// Inclut les fonctions nécessaires
require_once 'functions/getPDO.php';
require_once 'functions/login.php';

// Génère un token CSRF et le stocke dans la session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestion de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie le token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<p>Invalid CSRF token.</p>";
        exit;
    }

    // Récupère et sécurise les données du formulaire
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];

    // Vérifie les informations de connexion
    $loginResult = login($email, $password);
    if ($loginResult['success']) {
        // Si la connexion est réussie, enregistre le nom d'utilisateur en session et redirige vers le profil
        $_SESSION['email'] = $email;
        header('Location: profile.php');
        exit;
    } else {
        // Si la connexion échoue, affiche un message d'erreur
        echo "<p>{$loginResult['message']}</p>";
    }
}
?>
<main>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="email">Email :</label>
        <input type="text" id="email" name="email" required>
        <br>
        <label for="password">Password :</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
</main>

<?php
// Inclut le pied de page de la page
require 'footer.php';
?>