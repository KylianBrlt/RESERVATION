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

$message = '';

// Gestion des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        $result = cancelAppointment($_SESSION['email'], $_POST['appointment_id']);
    } elseif (isset($_POST['schedule'])) {
        $result = createAppointment($_SESSION['email'], $_POST['date'], $_POST['time']);
    }
    $message = $result['message'];
}

// Récupération des rendez-vous
$appointments = getUserAppointments($_SESSION['email']);
$allBookedAppointments = fetchAllBookedAppointments();

function fetchAllBookedAppointments() {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT appointment_date, appointment_time FROM appointments WHERE status = 'scheduled'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<body>
    <main>
        <h2>Gérer les rendez-vous</h2>
        <?php if ($message): ?>
            <div class="<?php echo $result['success'] ? 'success' : 'error' ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Calendrier FullCalendar -->
        <section>
            <h3>Sélectionnez une date pour voir les disponibilités</h3>
            <div id='calendar'></div>
        </section>

        <!-- Section des créneaux disponibles -->
        <section id="available-slots">
            <h3>Créneaux</h3>
            <div class="selected-date" id="selected-date"></div>
            <div id="slots-container"></div>
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
                            <form method="POST" action="appointments.php"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
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

    <!-- Formulaire caché pour la soumission des réservations -->
    <form id="booking-form" method="POST" action="appointments.php" style="display: none;">
        <input type="hidden" id="date-input" name="date">
        <input type="hidden" id="time-input" name="time">
        <input type="hidden" name="schedule" value="1">
    </form>

    <script>
        $(document).ready(function () {
            const allBookedSlots = {};

            <?php foreach ($allBookedAppointments as $appointment): ?>
                if (!allBookedSlots['<?php echo $appointment['appointment_date']; ?>']) {
                    allBookedSlots['<?php echo $appointment['appointment_date']; ?>'] = [];
                }
                allBookedSlots['<?php echo $appointment['appointment_date']; ?>'].push('<?php echo $appointment['appointment_time']; ?>');
            <?php endforeach; ?>

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek'
                },
                defaultView: 'agendaWeek',
                navLinks: true,
                editable: false,
                eventLimit: true,
                selectable: true,
                minTime: '09:00:00',
                maxTime: '17:00:00',
                businessHours: {
                    dow: [1, 2, 3, 4, 5],
                    start: '09:00',
                    end: '17:00',
                    rendering: 'background'
                },
                events: [
                    <?php
                    $userAppointmentTimes = [];
                    foreach ($appointments as $appointment) {
                        if ($appointment['status'] === 'scheduled') {
                            $key = $appointment['appointment_date'] . 'T' . $appointment['appointment_time'];
                            $userAppointmentTimes[$key] = true;
                        }
                    }
                    ?>

                    <?php foreach ($appointments as $appointment): ?>
                        <?php if ($appointment['status'] === 'scheduled'): ?>
                            {
                                title: 'Votre RDV',
                                start: '<?php echo $appointment['appointment_date']; ?>T<?php echo $appointment['appointment_time']; ?>',
                                end: '<?php echo $appointment['appointment_date']; ?>T<?php echo date('H:i', strtotime($appointment['appointment_time'] . ' +60 minutes')); ?>',
                                color: '#4CAF50'
                            },
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php foreach ($allBookedAppointments as $appointment): ?>
                        <?php
                        $key = $appointment['appointment_date'] . 'T' . $appointment['appointment_time'];
                        if (!isset($userAppointmentTimes[$key])):
                            ?>
                            {
                                title: 'Réservé',
                                start: '<?php echo $appointment['appointment_date']; ?>T<?php echo $appointment['appointment_time']; ?>',
                                end: '<?php echo $appointment['appointment_date']; ?>T<?php echo date('H:i', strtotime($appointment['appointment_time'] . ' +60 minutes')); ?>',
                                color: '#f44336'
                            },
                        <?php endif; ?>
                    <?php endforeach; ?>
                ],
                dayClick: function (date, jsEvent, view) {
                    var formattedDate = date.format('YYYY-MM-DD');
                    var displayDate = date.format('dddd D MMMM YYYY');

                    $('#selected-date').text(displayDate);
                    getAvailableSlots(formattedDate);
                    $('#available-slots').show();
                }
            });

            function getAvailableSlots(date) {
                const morningSlots = ['09:00', '10:00', '11:00'];
                const afternoonSlots = ['13:00', '14:00', '15:00', '16:00', '17:00'];
                const allSlots = [...morningSlots, ...afternoonSlots];
                const bookedSlots = allBookedSlots[date] || [];
                const dayOfWeek = moment(date).day();
                const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                const isPastDate = moment(date).isBefore(moment().startOf('day'));
                const slotsContainer = $('#slots-container');
                slotsContainer.empty();

                if (isPastDate) {
                    slotsContainer.append('<p>Vous ne pouvez pas réserver de rendez-vous dans le passé.</p>');
                    return;
                }

                if (isWeekend) {
                    slotsContainer.append('<p>Les rendez-vous ne sont pas disponibles le week-end.</p>');
                    return;
                }

                const bookedSlotsSet = new Set(bookedSlots);
                const availableSlots = allSlots.filter(slot => {
                    const slotTime = moment(date + ' ' + slot);
                    return !bookedSlotsSet.has(slot) && slotTime.isAfter(moment());
                });

                if (availableSlots.length === 0) {
                    slotsContainer.append('<p><strong>Tous les créneaux sont réservés pour cette date.</strong></p>');
                    return;
                }

                availableSlots.forEach(slot => {
                    const slotElement = $(`
                        <div class="time-slot">
                            <span>${slot}</span>
                            <button type="button" onclick="bookAppointment('${date}', '${slot}')">Réserver</button>
                        </div>
                    `);
                    slotsContainer.append(slotElement);
                });
            }
        });

        function bookAppointment(date, time) {
            if (confirm('Voulez-vous réserver un rendez-vous le ' + moment(date).format('DD/MM/YYYY') + ' à ' + time + ' ?')) {
                document.getElementById('date-input').value = date;
                document.getElementById('time-input').value = time;
                document.getElementById('booking-form').submit();
            }
        }
    </script>

    <?php require 'footer.php'; ?>
</body>

</html>
