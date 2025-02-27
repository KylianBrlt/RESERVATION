<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut l'en-tête de la page
require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Prepare email content
        $to = "kylian.brault@gmail.com"; // Replace with your email address
        $subject = "New Contact Form Submission";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";

        // Send email using msmtp
        $command = sprintf(
            'printf "Subject: %s\n\n%s" | msmtp -a default %s',
            escapeshellarg($subject),
            escapeshellarg($body),
            escapeshellarg($to)
        );

        // Execute the command
        $output = shell_exec($command);

        // Check if the email was sent successfully
        if ($output === null) {
            $success = "Message sent successfully!";
        } else {
            $error = "Failed to send message. Please try again later.";
        }
    }
}
?>

<main>
    <h2>Contact Us</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="POST" action="contact.php">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit">Send</button>
    </form>
</main>

<?php
// Inclut le pied de page de la page
require 'footer.php';
?>