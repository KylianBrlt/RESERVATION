<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'getPDO.php';

function createAppointment($user_email, $date, $time) {
    $pdo = getPDO();
    
    // Vérifier si le créneau est disponible
    $stmt = $pdo->prepare("SELECT id FROM appointments 
                          WHERE appointment_date = :date 
                          AND appointment_time = :time 
                          AND status = 'scheduled'");
    $stmt->execute(['date' => $date, 'time' => $time]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Ce créneau horaire n\'est pas disponible'];
    }
    
    // Récupérer l'ID de l'utilisateur à partir de son email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $user_email]);
    $user = $stmt->fetch();
    
    // Créer le rendez-vous
    $stmt = $pdo->prepare("INSERT INTO appointments 
                          (user_id, appointment_date, appointment_time) 
                          VALUES (:user_id, :date, :time)");
    
    $result = $stmt->execute([
        'user_id' => $user['id'],
        'date' => $date,
        'time' => $time
    ]);
    
    return ['success' => $result, 
            'message' => $result ? 'Rendez-vous programmé avec succès' : 'Échec de la programmation'];
}

function cancelAppointment($user_email, $appointment_id) {
    $pdo = getPDO();
    
    // Vérifier si le rendez-vous appartient à l'utilisateur
    $stmt = $pdo->prepare("SELECT a.id FROM appointments a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE u.email = :email AND a.id = :appointment_id 
                          AND a.status = 'scheduled'");
    $stmt->execute([
        'email' => $user_email,
        'appointment_id' => $appointment_id
    ]);
    
    if (!$stmt->fetch()) {
        return ['success' => false, 'message' => 'Rendez-vous non trouvé'];
    }
    
    // Mettre à jour le statut du rendez-vous à "annulé"
    $stmt = $pdo->prepare("UPDATE appointments 
                          SET status = 'cancelled' 
                          WHERE id = :appointment_id");
    
    $result = $stmt->execute(['appointment_id' => $appointment_id]);
    
    return [
        'success' => $result,
        'message' => $result ? 'Rendez-vous annulé avec succès' : 'Erreur lors de l\'annulation'
    ];
}

function getUserAppointments($user_email) {
    $pdo = getPDO();
    
    // Récupérer tous les rendez-vous de l'utilisateur
    $stmt = $pdo->prepare("SELECT a.* 
                          FROM appointments a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE u.email = :email 
                          ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    
    $stmt->execute(['email' => $user_email]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}