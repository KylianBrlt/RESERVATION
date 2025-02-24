<?php
require_once 'getPDO.php';

function createAppointment($user_email, $date, $time) {
    $pdo = getPDO();
    
    // Check if slot is available
    $stmt = $pdo->prepare("SELECT id FROM appointments 
                          WHERE appointment_date = :date 
                          AND appointment_time = :time 
                          AND status = 'scheduled'");
    $stmt->execute(['date' => $date, 'time' => $time]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Time slot not available'];
    }
    
    // Get user ID from email
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $user_email]);
    $user = $stmt->fetch();
    
    // Create appointment
    $stmt = $pdo->prepare("INSERT INTO appointments 
                          (user_id, appointment_date, appointment_time) 
                          VALUES (:user_id, :date, :time)");
    
    $result = $stmt->execute([
        'user_id' => $user['id'],
        'date' => $date,
        'time' => $time
    ]);
    
    return ['success' => $result, 
            'message' => $result ? 'Appointment scheduled' : 'Scheduling failed'];
}

function cancelAppointment($user_email, $appointment_id) {
    $pdo = getPDO();
    
    // Verify the appointment belongs to the user
    $stmt = $pdo->prepare("SELECT a.id FROM appointments a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE u.email = :email AND a.id = :appointment_id 
                          AND a.status = 'scheduled'");
    $stmt->execute([
        'email' => $user_email,
        'appointment_id' => $appointment_id
    ]);
    
    if (!$stmt->fetch()) {
        return ['success' => false, 'message' => 'Appointment not found'];
    }
    
    // Update appointment status to cancelled
    $stmt = $pdo->prepare("UPDATE appointments 
                          SET status = 'cancelled' 
                          WHERE id = :appointment_id");
    
    $result = $stmt->execute(['appointment_id' => $appointment_id]);
    
    return [
        'success' => $result,
        'message' => $result ? 'Appointment cancelled successfully' : 'Error cancelling appointment'
    ];
}

function getUserAppointments($user_email) {
    $pdo = getPDO();
    
    $stmt = $pdo->prepare("SELECT a.* 
                          FROM appointments a 
                          JOIN users u ON a.user_id = u.id 
                          WHERE u.email = :email 
                          ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    
    $stmt->execute(['email' => $user_email]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}