<?php
include_once 'user.php';

class Dashboard extends Users
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function getSubscriptionCounts(): array
    {
        $counts = [
            'monthly' => $this->getCountByDuration('monthly'),
            'annually' => $this->getCountByDuration('annually'),
            'semi_annually' => $this->getCountByDuration('semi-annually'),
            'quarterly' => $this->getCountByDuration('quarterly'),
            'total' => $this->getTotalSubscriptions(),
            'active' => $this->getActiveSubscriptions(),
            'inactive' => $this->getInactiveSubscriptions()
        ];

        return $counts;
    }

    private function getCountByDuration(string $duration): int
    {
        $sql = "SELECT COUNT(*) as count FROM user_subscriptions WHERE duration = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $duration);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    private function getTotalSubscriptions(): int
    {
        $sql = "SELECT COUNT(*) as count FROM user_subscriptions";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    private function getActiveSubscriptions(): int
    {
        $sql = "SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    private function getInactiveSubscriptions(): int
    {
        $sql = "SELECT COUNT(*) as count FROM user_subscriptions WHERE status = 'inactive'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
}
