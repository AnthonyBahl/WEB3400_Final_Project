<?php
require 'config.php';

// Start the session
session_start();

// First we check if the email and code exists...
if (isset($_GET['email'], $_GET['code'])) {
    if($stmt = $con->prepare('SELECT * FROM `accounts` WHERE `email` = ? AND `activation_code` = ?')) {
        $stmt->bind_param('ss', $_GET['email'], $_GET['code']);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0) {
            // Store the result so we can check if the account exists in the database
            if ($stmt = $con->prepare('UPDATE `accounts` SET `activation_code` = ? WHERE `email` = ? AND `activation_code` = ?')) {
                $newcode = 'activated';
                $stmt->bind_param('sss', $newcode, $_GET['email'], $_GET['code']);
                $stmt->execute();
                echo 'Your account has been activated. Click here to <a href="login.php">login</a>.';
            }
        }
    }
} else {
    echo 'The account is already activated or does not exist';
}

?> 