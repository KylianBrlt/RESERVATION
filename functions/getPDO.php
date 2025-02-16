<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function getPDO() {
    // Informations de connexion à la base de données
    $servername = "localhost";
    $username = "Create_User_Management";
    $password = "123";
    $dbname = "user_management";

    try {
        // Crée une nouvelle instance de PDO pour se connecter à la base de données
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Configure PDO pour générer des exceptions en cas d'erreur
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Display the actual error message for debugging purposes
        die("Database connection error: " . $e->getMessage());
    }
    
}
?>