<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Inclut l'en-tête de la page
require 'header.php';
?>

<main>
    <h2>Welcome to the User Management System</h2>
    <p>This is a simple user management system where you can register, login, and view your profile.</p>
    <p>Please use the menu above to navigate to the login or registration page.</p>
</main>

<?php
// Inclut le pied de page de la page
require 'footer.php';
?>