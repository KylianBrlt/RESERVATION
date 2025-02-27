<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Démarre la session pour gérer les utilisateurs connectés
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet'
        media='print' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
</head>
<body>
    <header>
        <h1>User Management System</h1>
        <nav>
            <?php if (isset($_SESSION['email'])) : ?>
                <!-- Si l'utilisateur est connecté, affiche les liens vers le profil et la déconnexion -->
                <a href="profile.php">Profile</a> |
                <a href="functions/logout.php">Logout</a> |
                <a href="appointments.php">Appointments</a> |
                <a href="contact.php">Contact</a>
            <?php else : ?>
                <!-- Si l'utilisateur n'est pas connecté, affiche les liens vers la page d'accueil, de connexion et d'inscription -->
                <a href="index.php">Home</a> |
                <a href="login.php">Login</a> |
                <a href="register.php">Register</a> |
                <a href="contact.php">Contact</a>
            <?php endif; ?>
        </nav>
    </header>