<?php
// Inclut l'en-tête de la page
require 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut le fichier de connexion à la base de données
require_once 'functions/getPDO.php';
?>

<main class="container">
<section class="verification-section">
<h2>Email Verification</h2>

<div class="verification-result">
<?php
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
            echo "<div class='success-message'>";
            echo "<p>Email verification successful! You can now <a href='login.php'>login</a>.</p>";
            echo "</div>";
        } else {
            echo "<div class='error-message'>";
            echo "<p>Error verifying email.</p>";
            echo "</div>";
        }
    } else {
        echo "<div class='error-message'>";
        echo "<p>Invalid token.</p>";
        echo "</div>";
    }
} else {
    echo "<div class='error-message'>";
    echo "<p>No token provided.</p>";
    echo "</div>";
}
?>
</div>
</section>
</main>

<?php
// Inclut le pied de page
require 'footer.php';
?>
