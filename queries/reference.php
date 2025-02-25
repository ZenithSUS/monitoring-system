<?php
include_once 'token.php';

class Reference extends Token
{

    protected function __construct()
    {
        parent::__construct();
    }

    protected function getReferences(): string
    {
        $sql = "SELECT * FROM references";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getReference(?string $id): string
    {
        $sql = "SELECT id, name, file_path,  FROM references WHERE reference_number = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }
}
