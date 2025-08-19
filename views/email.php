<?php

require __DIR__ . '/vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
$mail = new PHPMailer(true);

$mail->isSMTP();

   $mail->Host       = 'sandbox.smtp.mailtrap.io';                     
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'bd5ed557564c7b';                     
    $mail->Password   = '496353a167a69c';                               
    $mail->Port       = 2525;
    
    $mail->setFrom('hameemtrooper@gmail.com', 'Mailer');
    $mail->addAddress('cursedtenno458@gmail.com', 'Joe User');

    $mail->isHTML(true);

    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is test mail <b>HIIIIIII</b>';

    try {
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }