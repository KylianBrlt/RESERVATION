<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Démarre la session
session_start();
// Détruit la session
session_destroy();
// Redirige vers la page de connexion
header('Location: ../login.php');
exit;
?>