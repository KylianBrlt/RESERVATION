<?php
require_once 'getPDO.php';

function deleteAccount($email) {
    $pdo = getPDO();
    
    try {
        // Démarrer une transaction pour assurer la suppression de toutes les données associées
        $pdo->beginTransaction();
        
        // Supprimer le compte utilisateur
        $sql = "DELETE FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(['email' => $email]);
        
        if ($result) {
            $pdo->commit();
            return ['success' => true, 'message' => 'Compte supprimé avec succès'];
        }
        
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur lors de la suppression du compte'];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()];
    }
}
?>