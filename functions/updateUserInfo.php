<?php
require_once 'getPDO.php';

function updateUserInfo($currentEmail, $first_name, $last_name, $birth_date, $address, $phone, $newEmail) {
    $pdo = getPDO();
    
    // Check if new email already exists (if email is being changed)
    if ($currentEmail !== $newEmail) {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
        $stmt->execute(['email' => $newEmail]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
    }
    
    // Check if new phone already exists (if phone is being changed)
    $stmt = $pdo->prepare("SELECT phone FROM users WHERE phone = :phone AND email != :email");
    $stmt->execute(['phone' => $phone, 'email' => $currentEmail]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Phone number already exists'];
    }

    // Update user information
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
        return ['success' => true, 'message' => 'Profile updated successfully'];
    }
    return ['success' => false, 'message' => 'Error updating profile'];
}
?>