<?php
// Inclut l'en-tête de la page
require 'header.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut les fonctions nécessaires
require_once 'functions/getPDO.php';
require_once 'functions/createUser.php';

// Génère un token CSRF et le stocke dans la session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestion de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie le token CSRF
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "<p>Invalid CSRF token.</p>";
        exit;
    }

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
        echo "<p>Email already exists. Please choose a different email.</p>";
    } else if ($existingPhone) {
        // Si le numéro de téléphone existe déjà, affiche un message d'erreur
        echo "<p>Phone number already exists. Please choose a different phone number.</p>";
    } else {
        // Génère un token unique
        $token = bin2hex(random_bytes(16));

        // Crée un nouvel utilisateur avec le token
        if (createUser($first_name, $last_name, $birth_date, $address, $phone, $email, $password, $token)) {
            // Envoie un email de vérification

            // msmtp
            $verificationLink = "https://kylianbrlt.com/reservation/verify.php?token=$token";
            $subject = "Email Verification";
            $message = "Please click the following link to verify your email: $verificationLink";
            $headers = "From: no-reply@yourdomain.com";

            if (mail($email, $subject, $message, $headers)) {
                echo "<p>Registration successful! Please check your email to verify your account.</p>";
            } else {
                echo "<p>Error sending verification email.</p>";
            }
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
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
