<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF;  // Disable debug to prevent output interfering with redirects
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Port = 2525;
    $mail->Username = '894bf356bd68e0';
    $mail->Password = '3229787b28e045';

    // Set a valid sender email for Mailtrap
    $mail->setFrom('test@example.com', '7Auth System');
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
} catch (Exception $e) {
    error_log('Mail configuration error: ' . $e->getMessage());
}
