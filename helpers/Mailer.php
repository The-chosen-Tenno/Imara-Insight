<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';



function sendLeaveRequestEmail($email, $user_name, $leave_details)
{
    $subject = 'Leave Request';
    $body = '
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Leave Request</title>
            <style>
                body { font-family: Arial,sans-serif; background:#f4f4f4; margin:0; padding:0; }
                .container { max-width:600px; margin:30px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
                h2 { color:#333; }
                p { color:#555; }
                .details td { padding:8px 10px; }
                .footer { margin-top:30px; font-size:12px; color:#999; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Leave Request</h2>
                <p>Dear HR / Team,</p>
                <p>I would like to request leave with the following details:</p>
                
                <table class="details" border="0" cellpadding="0" cellspacing="0">';

    if (!empty($leave_details['leave_duration'])) {
        $body .= '<tr>
                <td><strong>Leave Duration:</strong></td>
                <td>' . htmlspecialchars($leave_details['leave_duration']) . '</td>
            </tr>';
    }

    if (!empty($leave_details['date_off'])) {
        $body .= '<tr>
                <td><strong>Date Off:</strong></td>
                <td>' . htmlspecialchars($leave_details['date_off']) . '</td>
            </tr>';
    }

    if (!empty($leave_details['start_date'])) {
        $body .= '<tr>
                <td><strong>Start Date:</strong></td>
                <td>' . htmlspecialchars($leave_details['start_date']) . '</td>
            </tr>';
    }

    if (!empty($leave_details['end_date'])) {
        $body .= '<tr>
                <td><strong>End Date:</strong></td>
                <td>' . htmlspecialchars($leave_details['end_date']) . '</td>
            </tr>';
    }

    if (!empty($leave_details['reason_type'])) {
        $body .= '<tr>
                <td><strong>Reason:</strong></td>
                <td>' . htmlspecialchars($leave_details['reason_type']) . '</td>
            </tr>';
    }

    $body .= '</table>
                <p>I kindly request you to approve my leave.</p>
                <p>Thank you,<br>' . htmlspecialchars($user_name) . '</p>

                <div class="footer">
                    This is an automated email.
                </div>
            </div>
        </body>
        </html>';


    $to_email = 'leaves@imarasoft.net';
    $to_name = 'admin';
    sendMail($to_email, $to_name, $email, $user_name, $subject, $body);
}

function sendApproveLeaveEmail($email, $user_name, $leave_details)
{
    $subject = 'Your Leave Has been Approved';
    $body = '
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Leave Approved</title>
            <style>
                body { font-family: Arial,sans-serif; background:#f4f4f4; margin:0; padding:0; }
                .container { max-width:600px; margin:30px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
                h2 { color:#333; }
                p { color:#555; }
                .details td { padding:8px 10px; }
                .footer { margin-top:30px; font-size:12px; color:#999; }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>Leave Approved</h2>
                <p>Hi ' . htmlspecialchars($user_name) . ',</p>
                <p>Your leave request has been approved. Here are the details:</p>
                <table class="details" border="0" cellpadding="0" cellspacing="0">';

    if (!empty($leave_details['leave_duration'])) {
        $body .= '<tr>
                    <td><strong>Leave Duration:</strong></td>
                    <td>' . htmlspecialchars($leave_details['leave_duration']) . '</td>
                  </tr>';
    }

    if (!empty($leave_details['date_off'])) {
        $body .= '<tr>
                    <td><strong>Date Off:</strong></td>
                    <td>' . htmlspecialchars($leave_details['date_off']) . '</td>
                  </tr>';
    }

    if (!empty($leave_details['start_date'])) {
        $body .= '<tr>
                    <td><strong>Start Date:</strong></td>
                    <td>' . htmlspecialchars($leave_details['start_date']) . '</td>
                  </tr>';
    }

    if (!empty($leave_details['end_date'])) {
        $body .= '<tr>
                    <td><strong>End Date:</strong></td>
                    <td>' . htmlspecialchars($leave_details['end_date']) . '</td>
                  </tr>';
    }

    if (!empty($leave_details['reason_type'])) {
        $body .= '<tr>
                    <td><strong>Reason:</strong></td>
                    <td>' . htmlspecialchars($leave_details['reason_type']) . '</td>
                  </tr>';
    }

    $body .= '</table>
                <p>Thank you,<br>Admin</p>
                <div class="footer">This is an automated email.</div>
            </div>
        </body>
        </html>';

    $from_email = 'leaves@imarasoft.net';
    $from_name = 'admin';
    sendMail($email, $user_name, $from_email, $from_name, $subject, $body);
}

function sendMail($ToEmail, $ToName, $fromEmail, $fromName, $subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'e8be8f5dddf8d1';
        $mail->Password   = '49b9535e75f3e6';
        $mail->Port       = 2525;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($ToEmail, $ToName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
