<?php
include_once 'user.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SubscriptionStatus extends Users
{
    protected function __construct()
    {
        parent::__construct();
    }


    protected function getSubscriptionStatus(?string $id): string
    {
        $sql = "SELECT id, name, file_path,  FROM subscription_status WHERE reference_number = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function sendEmail(?string $email): string
    {
        $sql = "SELECT email FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function sendExpiryNotification(?string $email, ?string $expiryDate): bool
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.example.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@example.com';
            $mail->Password = 'your_email_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            //Recipients
            $mail->setFrom('your_email@example.com', 'Mailer');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Subscription Expiry Notification';
            $mail->Body    = "Dear user,<br><br>Your subscription is set to expire on $expiryDate. Please renew your subscription to continue enjoying our services.<br><br>Thank you.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
