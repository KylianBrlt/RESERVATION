<?php
require_once 'getPDO.php';

function deleteAccount($email) {
    $pdo = getPDO();
    
    try {
        // Start transaction to ensure all related data is deleted
        $pdo->beginTransaction();
        
        // Delete user account
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(['email' => $email]);
        
        if ($result) {
            $pdo->commit();
            return ['success' => true, 'message' => 'Account deleted successfully'];
        }
        
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Error deleting account'];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}
?>