<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'header.php';
require_once 'functions/appointmentManager.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

// Gestion de l'annulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
    $result = cancelAppointment($_SESSION['email'], $_POST['appointment_id']);
    $message = $result['message'];
}

// Gestion d'une nouvelle réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule'])) {
    $date = $_POST['date'];
    $time = $_POST['time'];
    $result = createAppointment($_SESSION['email'], $date, $time);
    $message = $result['message'];
}

// Récupération des rendez-vous de l'utilisateur
$appointments = getUserAppointments($_SESSION['email']);
?>

<main>
    <h2>Gérer les rendez-vous</h2>
    <?php if (isset($message)): ?>
        <div class="<?php echo isset($result['success']) && $result['success'] ? 'success' : 'error' ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulaire de prise de rendez-vous -->
    <section class="schedule-appointment">
        <h3>Prendre un nouveau rendez-vous</h3>
        <form method="POST" action="appointments.php">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" required 
                       min="<?php echo date('Y-m-d'); ?>" 
                       max="<?php echo date('Y-m-d', strtotime('+6 months')); ?>">
            </div>
            
            <div class="form-group">
                <label for="time">Heure:</label>
                <select id="time" name="time" required>
                    <?php
                    // Créneaux du matin (9h00 - 12h00)
                    $morning_start = new DateTime('09:00');
                    $morning_end = new DateTime('11:30');
                    $interval = new DateInterval('PT30M');
                    $current = clone $morning_start;
                    
                    while ($current <= $morning_end) {
                        $timeStr = $current->format('H:i');
                        echo "<option value=\"$timeStr\">$timeStr</option>";
                        $current->add($interval);
                    }
                    
                    // Créneaux de l'après-midi (13h00 - 17h00)
                    $afternoon_start = new DateTime('13:00');
                    $afternoon_end = new DateTime('17:00');
                    $current = clone $afternoon_start;
                    
                    while ($current <= $afternoon_end) {
                        $timeStr = $current->format('H:i');
                        echo "<option value=\"$timeStr\">$timeStr</option>";
                        $current->add($interval);
                    }
                    ?>
                </select>
            </div>
            
            <input type="hidden" name="schedule" value="1">
            <button type="submit">Réserver</button>
        </form>
    </section>
    
    <!-- Liste des rendez-vous existants -->
    <section class="appointment-list">
        <h3>Vos rendez-vous</h3>
        <?php if ($appointments): ?>
            <?php foreach ($appointments as $appointment): ?>
                <div class="appointment-item">
                    <p>
                        Date: <?php echo htmlspecialchars($appointment['appointment_date']); ?><br>
                        Heure: <?php echo htmlspecialchars($appointment['appointment_time']); ?><br>
                        Statut: <?php echo htmlspecialchars($appointment['status']); ?>
                    </p>
                    <?php if ($appointment['status'] === 'scheduled'): ?>
                        <form method="POST" action="appointments.php" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                            <input type="hidden" name="cancel" value="1">
                            <button type="submit" class="cancel-button">Annuler le rendez-vous</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun rendez-vous trouvé.</p>
        <?php endif; ?>
    </section>
</main>

<?php require 'footer.php'; ?>