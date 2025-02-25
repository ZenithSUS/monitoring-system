<?php
include_once 'users.php';

class Requirements extends Users
{
    protected function __construct()
    {
        parent::__construct();
    }

    protected function getRequirements(): string
    {
        $sql = "SELECT * FROM requirements";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getRequirement(?string $id): string
    {
        $sql = "SELECT id, list_of_compliance, departmentid, 
        entity_name, frequency_of_compliance, date_submitted, 
        expiration, renewal, person_in_charge, status  
        FROM requirements WHERE document_reference = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }
}
