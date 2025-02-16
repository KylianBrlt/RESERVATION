<?php
// Inclut l'en-tête de la page
require 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Inclut les fonctions nécessaires
require_once 'functions/getPDO.php';
require_once 'functions/createUser.php';

// Gestion de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère et sécurise les données du formulaire
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $birth_date = htmlspecialchars(trim($_POST['birth_date']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    // Vérifie si l'email est déjà utilisé
    $pdo = getPDO();
    $sql = "SELECT email FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    // Vérifie si le numéro de téléphone est déjà utilisé
    $sql = "SELECT phone FROM users WHERE phone = :phone";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['phone' => $phone]);
    $existingPhone = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // Si l'email existe déjà, affiche un message d'erreur
        echo "<p>email already exists. Please choose a different email.</p>";
    } else if ($existingPhone) {
        // Si le numéro de téléphone existe déjà, affiche un message d'erreur
        echo "<p>Phone number already exists. Please choose a different phone number.</p>";
    } else {
        // Crée un nouvel utilisateur
        if (createUser($first_name, $last_name, $birth_date, $address, $phone, $email, $password)) {
            // Si l'inscription est réussie, redirige vers la page de connexion avec un message de succès
            $_SESSION['success_message'] = "Registration successful! Please login.";
            header('Location: login.php');
            exit;
        } else {
            // Si l'inscription échoue, affiche un message d'erreur
            echo "<p>Error during account creation.</p>";
        }
    }
}
?>
<main>
    <h2>Register</h2>
    <form method="POST" action="register.php">
        <input type="text" name="first_name" placeholder="Prénom" required>
        <input type="text" name="last_name" placeholder="Nom" required>
        <input type="date" name="birth_date" required>
        <input type="text" name="address" placeholder="Adresse postale" required>
        <input type="text" name="phone" placeholder="Numéro de téléphone" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">S'inscrire</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</main>

<?php
// Inclut le pied de page de la page
require 'footer.php';
?>