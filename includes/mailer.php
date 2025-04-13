<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '..\vendor\autoload.php';

function createMailer() {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tanish183gupta@gmail.com';
        $mail->Password   = 'kwbv fdci qeld arfq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('tanish183gupta@gmail.com', 'Farmers Portal');

        return $mail;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return null;
    }
}

function sendRegistrationEmail($email, $username, $userType) {
    $mail = createMailer();
    if (!$mail) return false;

    try {
        $mail->addAddress($email);
        $mail->Subject = 'Welcome to Farmers Portal!';
        $mail->isHTML(true);
        
        $mail->Body = "
            <h2>Welcome to Farmers Portal!</h2>
            <p>Dear {$username},</p>
            <p>Thank you for registering as a {$userType} on Farmers Portal. Your account has been successfully created.</p>
            <p>You can now login and start using our platform.</p>
            <p>Best regards,<br>Farmers Portal Team</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendBidPlacedEmail($farmerEmail, $retailerName, $productName, $quantity, $bidAmount) {
    $mail = createMailer();
    if (!$mail) return false;

    try {
        $mail->addAddress($farmerEmail);
        $mail->Subject = 'New Bid Received';
        $mail->isHTML(true);
        
        $mail->Body = "
            <h2>New Bid Received</h2>
            <p>A new bid has been placed on your product:</p>
            <ul>
                <li>Product: {$productName}</li>
                <li>Quantity: {$quantity}</li>
                <li>Bid Amount: ₹{$bidAmount}</li>
                <li>Retailer: {$retailerName}</li>
            </ul>
            <p>Login to your account to review this bid.</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sendBidStatusEmail($retailerEmail, $status, $productName, $quantity, $bidAmount) {
    $mail = createMailer();
    if (!$mail) return false;

    try {
        $mail->addAddress($retailerEmail);
        $mail->Subject = "Bid {$status}";
        $mail->isHTML(true);
        
        $mail->Body = "
            <h2>Bid {$status}</h2>
            <p>Your bid has been {$status}:</p>
            <ul>
                <li>Product: {$productName}</li>
                <li>Quantity: {$quantity}</li>
                <li>Bid Amount: ₹{$bidAmount}</li>
            </ul>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: {$mail->ErrorInfo}");
        return false;
    }
}