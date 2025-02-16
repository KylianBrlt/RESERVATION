<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Inclut le fichier de connexion à la base de données
require_once 'getPDO.php';

function getUserInfo($email) {
    // Obtient une instance de PDO
    $pdo = getPDO();
    // Prépare la requête SQL pour récupérer les informations de l'utilisateur
    $sql = "SELECT email FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    // Exécute la requête avec le nom d'utilisateur fourni
    $stmt->execute(['email' => $email]);
    // Récupère et retourne les informations de l'utilisateur
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>