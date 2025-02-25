<?php
include_once 'user.php';

class UserProgress extends Users
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function getUserProgress(?string $userId): array
    {
        $sql = "SELECT progress FROM user_progress WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result->fetch_assoc() : [];
    }

    public function resetUserProgress(?string $userId): bool
    {
        $sql = "UPDATE user_progress SET progress = 0 WHERE user_id = ?";
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
        $sql = "UPDATE user_subscriptions SET 
            start_date = ?, 
            expiry_date = ?, 
            status = 'active' 
            WHERE user_id = ?";

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
