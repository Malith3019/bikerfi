<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'src/Exception.php';
require_once 'src/PHPMailer.php';
require_once 'src/SMTP.php';


// passing true in constructor enables exceptions in PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //$mail->Username = 'unix.assignments@gmail.com'; // YOUR gmail email
    $mail->Username = 'systemtesting@ancedu.com'; // YOUR gmail email
    $mail->Password = 'Hari@1234'; // YOUR gmail password

   // $mail->AddAttachment('pdf_files/reservation.pdf', 'reservation.pdf');
    // Sender and recipient settings
    $mail->setFrom('systemtesting@ancedu.com', 'Sender Name');
    $mail->addAddress('malithd@ceyins.lk', 'Receiver Name');
    //$mail->addReplyTo('example@gmail.com', 'Sender Name'); // to set the reply to

    // Setting the email content
    $mail->IsHTML(true);
    $mail->Subject = "  @sdf";
    $mail->Body = ' @sdf';

    
    //$mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';
    // $mail->addAttachment("D:/Invoices/01-JAN-22/IN-22CO0100007915.pdf");
    // $mail->send();
    echo "Email message sent.";
} catch (Exception $e) {
    echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
}

?>