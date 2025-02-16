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
require_once 'functions/updateUserInfo.php';
require_once 'functions/deleteAccount.php';

// Récupère les informations de l'utilisateur depuis la base de données
$user = getUserInfo($_SESSION['email']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'delete_account') {
        $result = deleteAccount($_SESSION['email']);
        if ($result['success']) {
            session_destroy();
            header('Location: login.php');
            exit;
        } else {
            $message = '<div class="error">' . $result['message'] . '</div>';
        }
    } else {
        $result = updateUserInfo(
            $_SESSION['email'],
            htmlspecialchars(trim($_POST['first_name'])),
            htmlspecialchars(trim($_POST['last_name'])),
            htmlspecialchars(trim($_POST['birth_date'])),
            htmlspecialchars(trim($_POST['address'])),
            htmlspecialchars(trim($_POST['phone'])),
            filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL)
        );
        
        if ($result['success']) {
            $_SESSION['email'] = $_POST['email'];
            $user = getUserInfo($_SESSION['email']);
            $message = '<div class="success">' . $result['message'] . '</div>';
        } else {
            $message = '<div class="error">' . $result['message'] . '</div>';
        }
    }
}

if ($user) {
    ?>
    <main>
        <h2>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>
        <?php echo $message; ?>
        
        <form method="POST" action="profile.php" class="edit-profile-form">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="birth_date">Birth Date:</label>
                <input type="date" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <button type="submit">Update Profile</button>
        </form>
        
        <!-- Add Delete Account Section -->
        <div class="delete-account-section">
            <h3>Delete Account</h3>
            <p class="warning">Warning: This action cannot be undone. All your data will be permanently deleted.</p>
            <form method="POST" action="profile.php" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                <input type="hidden" name="action" value="delete_account">
                <button type="submit" class="delete-button">Delete Account</button>
            </form>
        </div>
    </main>
    <?php
} else {
    echo "<p>Error retrieving user information.</p>";
}

// Inclut le pied de page de la page
require_once 'footer.php';
?>