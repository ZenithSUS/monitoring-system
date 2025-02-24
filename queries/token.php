<?php
include_once '../status.php';

class Token extends API {
    
    protected $conn;
    protected $errors = array();

    protected function __construct() {
        $this->conn = $this->connect();
    }

    /**
     * Generate token
     * @return string
    */
    protected function generateToken() : string {
        $token = bin2hex(random_bytes(16));
        return $token;
    }

    /**
     * Verify token
     * @param string $token
     * @return bool
    */
    protected function verifyToken(?string $token = null) : bool {
        $sql = "SELECT token FROM users WHERE token = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows === 1;
    }

    /**
     * Insert token
     * @param string $token
     * @param string $id
     * @return void
    */
    protected function insertToken(string $token, string $id) : void {
        $sql = "UPDATE users SET token = ? WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $token, $id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Delete token
     * @param string $token
     * @return void
    */
    protected function deleteToken(string $token) : void {
        $sql = "UPDATE users SET token = NULL WHERE token = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();
    }    
}
?>