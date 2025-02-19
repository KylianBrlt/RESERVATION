<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclut l'en-tête de la page
require 'header.php';
?>

<main>
    <h2>Contact Us</h2>
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