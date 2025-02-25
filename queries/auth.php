<?php
include_once 'token.php';

class Auth extends Token {
    public function __construct() {
        parent::__construct();
    }


    /**
     * Email exists
     * @param string $email
     * @return bool
    */
    protected function emailExists(string $email) : bool {
        $sql = "SELECT email FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Check password
     * @param string $password
     * @return array
    */
    protected function checkPassword(string $password) : array {
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $specialchars = preg_match('/[^A-Za-z0-9]/', $password);
        $numericVal = preg_match('/[0-9]/', $password);

       return [
            "Uppercase" => $uppercase,
            "Lowercase" => $lowercase,
            "Special characters" => $specialchars,
            "Numeric value" => $numericVal
       ];
    }

    /**
     * Validate password
     * @param array $password
     * @return bool
    */
    protected function validatePassword(array $password) : bool {
  
        foreach (array_keys($password) as $hasType){
            $status = $hasType ? true : false;
        }
        return $status;
    }
}
?>