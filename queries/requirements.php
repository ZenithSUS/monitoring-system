<?php
include_once 'users.php';

class Requirements extends Users
{
    protected function __construct()
    {
        parent::__construct();
    }

    protected function getRequirements(): string {
        $sql = "SELECT id, list_of_compliance, departmentid, 
        entity_name, foc, date_submitted, 
        expiration, renewal, person_in_charge, status  
        FROM requirements";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getRequirement(?string $id): string {
        $sql = "SELECT id, list_of_compliance, departmentid, 
        entity_name, foc, date_submitted, 
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

    protected function addRequirement(?string $list_of_compliance, ?string $departmentid,
    ?string $entity_name, ?string $frequency_of_compliance, ?string $date_submitted, 
    ?string $expiration, ?string $renewal, ?string $person_in_charge, ?string $status): string {
        $sql = "INSERT INTO requirements (list_of_compliance, departmentid, entity_name, 
        frequency_of_compliance, date_submitted, expiration, renewal, person_in_charge, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('sssssssss', $list_of_compliance, $departmentid, $entity_name, 
        $frequency_of_compliance, $date_submitted, $expiration, $renewal, $person_in_charge, $status);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->success() : $this->queryFailed();
    }

    protected function editRequirement(?string $id, ?string $list_of_compliance, ?string $departmentid,
    ?string $entity_name, ?string $frequency_of_compliance, ?string $date_submitted,
    ?string $expiration, ?string $renewal, ?string $person_in_charge, ?string $status): string {
        $sql = "UPDATE requirements SET list_of_compliance = ?, departmentid = ?, entity_name = ?, 
        foc = ?, date_submitted = ?, expiration = ?, renewal = ?, person_in_charge = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('ssssssssss', $list_of_compliance, $departmentid, $entity_name, 
        $frequency_of_compliance, $date_submitted, $expiration, $renewal, $person_in_charge, $status, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->success() : $this->queryFailed();
    }

    protected function deleteRequirement(?string $id): string {
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
