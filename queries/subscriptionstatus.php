<?php
include_once 'requirements.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SubscriptionStatus extends Requirements
{
    protected function __construct()
    {
        parent::__construct();
    }


    protected function getSubscriptionStatus(?string $id): string
    {

        $sql = "SELECT frequency_of_compliance, date_submiited, expiration,
        renewa, type_of_compliance, 
        status FROM requirements WHERE reference_number = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }


    protected function sendExpiryNotification(?string $email, ?string $expiryDate): void
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
            $mail->Body = "
                <html>
                <head>
                    <style>
                        .container {
                            font-family: Arial, sans-serif;
                            text-align: center;
                            padding: 20px;
                            border: 1px solid #ddd;
                            border-radius: 10px;
                            background-color: #f9f9f9;
                        }
                        .code {
                            font-size: 24px;
                            font-weight: bold;
                            color: #333;
                        }
                        .footer {
                            margin-top: 20px;
                            font-size: 12px;
                            color: #777;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <h2>Sub</h2>
                        <p>Dear User your subscription will exipire on</p>
                        <p class='code'>$expiryDate</p>
                        <div class='footer'>
                            <p>renew your subcription before it expires.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            $mail->send();
            $mail->preSend();
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
