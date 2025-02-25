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
        $sql = "SELECT id, list_of_compliance, departmentid, 
        entity_name, foc, date_submitted, 
        expiration, renewal, person_in_charge, status  
        FROM requirements";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getRequirement(?string $id): string
    {
        $sql = "SELECT id, list_of_compliance, departmentid, 
        entity_name, foc, date_submitted, 
        expiration, DATEDIFF(expiration, CURDATE()) AS days_left, renewal, person_in_charge, status  
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

    protected function addRequirement(
        ?string $loc,
        ?string $departmentid,
        ?string $entity_name,
        ?string $foc,
        ?string $date_submitted,
        ?string $renewal,
        ?string $person_in_charge,
        ?string $status
    ): string {

        $sql = "INSERT INTO requirements (list_of_compliance, departmentId, entity_name, 
        foc, date_submitted, expiration, renewal, person_in_charge, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($foc === "Annually") {
            $expiration = date('Y-m-d', strtotime('+1 year'));
        } elseif ($foc === "Semi-annually") {
            $expiration = date('Y-m-d', strtotime('+6 months'));
        } elseif ($foc === "Quarterly") {
            $expiration = date('Y-m-d', strtotime('+3 months'));
        } else {
            $expiration = date('Y-m-d', strtotime('+1 month'));
        }

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param(
            'sssssssss',
            $loc,
            $departmentid,
            $entity_name,
            $foc,
            $date_submitted,
            $expiration,
            $renewal,
            $person_in_charge,
            $status
        );
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->success() : $this->queryFailed();
    }

    protected function editRequirement(
        ?string $id,
        ?string $loc,
        ?string $departmentid,
        ?string $entity_name,
        ?string $person_in_charge,
        ?string $status
    ): string {
        $sql = "UPDATE requirements SET list_of_compliance = ?, departmentid = ?, entity_name = ?, 
        foc = ?, person_in_charge = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param(
            'sssssssss',
            $loc,
            $departmentid,
            $entity_name,
            $person_in_charge,
            $status,
            $id
        );
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->success() : $this->queryFailed();
    }

    protected function deleteRequirement(?string $id): string
    {
        $sql = "DELETE FROM requirements WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('s', $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->success() : $this->queryFailed();
    }
}
