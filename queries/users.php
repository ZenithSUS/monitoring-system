<?php
include_once 'token.php';

class Users extends Token {

    protected function __construct() {
        parent::__construct();
    }

    /**
     * Get username by token 
     * @param string $token 
     * @return string
    */     
    protected function getFullName(string $token) : string {
        $sql = "SELECT CONCAT(first_name, ' ', middle_name, ' ', last_name) AS full_name FROM users WHERE token = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['full_name'];
    }

    protected function getUsers() : string {
        $sql = "SELECT * FROM users";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }
}
?>