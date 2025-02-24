<?php
require_once 'getPDO.php';

function updateUserInfo($currentEmail, $first_name, $last_name, $birth_date, $address, $phone, $newEmail) {
    $pdo = getPDO();
    
    // Vérifier si le nouvel email existe déjà (si l'email est modifié)
    if ($currentEmail !== $newEmail) {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->execute(['email' => $newEmail]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Cette adresse email existe déjà'];
        }
    }
    
    // Vérifier si le nouveau numéro de téléphone existe déjà (si le téléphone est modifié)
    $stmt = $pdo->prepare("SELECT phone FROM users WHERE phone = :phone AND email != :email");
    $stmt->execute(['phone' => $phone, 'email' => $currentEmail]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Ce numéro de téléphone existe déjà'];
    }

    // Mettre à jour les informations utilisateur
    $sql = "UPDATE users SET 
            first_name = :first_name,
            last_name = :last_name,
            birth_date = :birth_date,
            address = :address,
            phone = :phone,
            email = :newEmail
            WHERE email = :currentEmail";
            
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'first_name' => $first_name,
        'last_name' => $last_name,
        'birth_date' => $birth_date,
        'address' => $address,
        'phone' => $phone,
        'newEmail' => $newEmail,
        'currentEmail' => $currentEmail
    ]);
    
    if ($result) {
        return ['success' => true, 'message' => 'Profil mis à jour avec succès'];
    }
    return ['success' => false, 'message' => 'Erreur lors de la mise à jour du profil'];
}
?>