<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut le fichier de connexion à la base de données
require_once 'getPDO.php';

function createUser($first_name, $last_name, $birth_date, $address, $phone, $email, $password, $token) {
    // Obtient une instance de PDO
    $pdo = getPDO();
    // Hash le mot de passe avant de le stocker dans la base de données
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    // Prépare la requête SQL pour insérer un nouvel utilisateur
    $sql = "INSERT INTO users (first_name, last_name, birth_date, address, phone, email, password, token, is_verified) 
            VALUES (:first_name, :last_name, :birth_date, :address, :phone, :email, :password, :token, 0)";
    $stmt = $pdo->prepare($sql);
    // Exécute la requête avec les paramètres fournis
    return $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'birth_date' => $birth_date,
        'address' => $address,
        'phone' => $phone,
        'email' => $email,
        'password' => $hashedPassword,
        'token' => $token
    ]);
}
?>