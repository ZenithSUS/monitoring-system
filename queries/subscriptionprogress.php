<?php
include_once 'requirements.php';

class SubscriptionProgress extends Requirements
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function resetUserProgress(?string $id): bool
    {
        $sql = "UPDATE requirements SET status = 'incomplete', date_submitted = CURDATE() WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $userId);
        return $stmt->execute();
    }

    public function handleSubscriptionExpiry(?string $userId, ?string $expiryDate): bool
    {
        $currentDate = date('Y-m-d');
        if ($currentDate >= $expiryDate) {
            return $this->resetUserProgress($userId);
        }
        return false;
    }

    public function renewSubscription(?string $userId, int $subscriptionDurationDays = 30): bool
    {
        // Get current date as starting point
        $startDate = date('Y-m-d');

        // Calculate new expiry date based on subscription duration
        $expiryDate = date('Y-m-d', strtotime("+{$subscriptionDurationDays} days"));

        // Update subscription data in database
        $sql = "UPDATE requirements SET date_submitted = ?, expiration = ? WHERE user_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $startDate, $expiryDate, $userId);
        $subscriptionUpdated = $stmt->execute();

        if (!$subscriptionUpdated) {
            return false;
        }

        // Reset user progress after renewal
        return $this->resetUserProgress($userId);
    }
}
