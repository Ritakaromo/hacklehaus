<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = htmlspecialchars($_POST['full_name']);
    $phone    = htmlspecialchars($_POST['phone']);
    $email    = htmlspecialchars($_POST['email']);
    $desc     = nl2br(htmlspecialchars($_POST['description']));

    $mail = new PHPMailer(true);

    try {
        // ==============================
        // 1. SEND MESSAGE TO YOU
        // ==============================
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hacklehausflyco@gmail.com';  // your Gmail
        $mail->Password = 'uzqz bzpi zsse arzs';        // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom("hacklehausflyco@gmail.com", "HackleHaus Website");
        $mail->addReplyTo($email, $fullname);
        $mail->addAddress("hacklehausflyco@gmail.com", "HackleHaus Support");

        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission";
        $mail->Body    = "
            <h2>New Contact Message</h2>
            <p><b>Name:</b> $fullname</p>
            <p><b>Phone:</b> $phone</p>
            <p><b>Email:</b> $email</p>
            <p><b>Message:</b><br>$desc</p>
            <hr>
            <small>Sent on ".date("d M Y, h:i A")."</small>
        ";
        $mail->send();

        // ==============================
        // 2. AUTO-REPLY TO CUSTOMER
        // ==============================
        $auto = new PHPMailer(true);
        $auto->isSMTP();
        $auto->Host = 'smtp.gmail.com';
        $auto->SMTPAuth = true;
        $auto->Username = 'hacklehausflyco@gmail.com';  
        $auto->Password = 'uzqz bzpi zsse arzs';        
        $auto->SMTPSecure = 'tls';
        $auto->Port = 587;

        $auto->setFrom("hacklehausflyco@gmail.com", "HackleHaus Support");
        $auto->addAddress($email, $fullname);

        $auto->isHTML(true);
        $auto->Subject = "We Received Your Message ✔";
        $auto->Body    = "
            <h2>Hello $fullname,</h2>
            <p>Thank you for reaching out to <b>HackleHaus Fly Co</b>. 
            We’ve received your message and one of our team members will get back to you soon.</p>

            <h3>Your Message:</h3>
            <blockquote>$desc</blockquote>

            <p>In the meantime, feel free to check our website for more details.</p>

            <br>
            <p>Best regards,<br>
            <b>HackleHaus Support Team</b></p>
        ";
        $auto->send();

        echo "<script>alert('✅ Your message has been sent!'); window.location.href='contact.html';</script>";
    } catch (Exception $e) {
        echo "<script>alert('❌ Message could not be sent. Error: {$mail->ErrorInfo}'); window.location.href='contact.html';</script>";
    }
}
?>
