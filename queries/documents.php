<?php
include_once 'token.php';

class Users extends Token
{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function getDocuments(): string
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getDocument(?string $id): string
    {
        $sql = "SELECT id, name, file_path,  FROM users WHERE reference_number = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }
}
