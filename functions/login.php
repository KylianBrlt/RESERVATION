<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Inclut le fichier de connexion à la base de données
require_once 'getPDO.php';

function login($email, $password) {
    // Obtient une instance de PDO
    $pdo = getPDO();
    // Prépare la requête SQL pour récupérer le mot de passe hashé de l'utilisateur
    $sql = "SELECT password FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    // Exécute la requête avec le nom d'utilisateur fourni
    $stmt->execute(['email' => $email]);
    // Récupère le résultat de la requête
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si l'utilisateur existe et si le mot de passe fourni correspond au mot de passe hashé
    if ($result && password_verify($password, $result['password'])) {
        return true;
    }
    return false;
}
?>