<?php
include_once 'token.php';

class Auth extends Token {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Login user
     * @param string $account
     * @param string $password
     * @return string
     */
    protected function loginUser(?string $email = null, ?string $password = null) : string { 
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);

        if(!$stmt->execute()) {
            return $this->queryFailed();
        }


        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if(!password_verify($password, $row['password'])) {
                $this->errors['auth_error'] = 'Email or password is incorrect';
                return $this->fieldError($this->errors);
            }

            $token = $this->generateToken();
            $this->insertToken($token, $row['id']);
            return $this->success('login', $token);
        }

        
        if(!$this->checkLoginAuth($email, $password)){
            $this->errors['auth_error'] = 'Email or password is incorrect';
            return $this->fieldError($this->errors);
        }

        return $this->queryFailed();
    }

    /**
     * Check login auth
     * @param string $account
     * @return bool
    */
    private function checkLoginAuth(string $email) : bool {
        $sql = "SELECT email FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Register user
     * @param string $firstname
     * @param string $middlename
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param string $confirmpassword
     * @return string
     */
    protected function registerUser(?string $firstname = null, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $password = null, ?string $confirmpassword = null) : string {
        $this->checkFields($firstname, $middlename, $lastname, $email, $password, $confirmpassword);

        if(!empty($this->errors)) {
            return $this->fieldError($this->errors);
        }

        return $this->registerUserQuery($firstname, $middlename, $lastname, $email, $password);
    }

    /**
     * Logout user
     * @param string $token
     * @return string
     */
    protected function logoutUser(?string $token) : string {
        $this->deleteToken($token);
        return $this->success('logout');
    }

    /**
     * Check fields
     * @param string firstname
     * @param string middlename
     * @param string lastname
     * @param string $email
     * @param string $password
     * @param string $confirmpassword
     * @return void
    */
    private function checkFields(?string $firstname = null, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $password = null, ?string $confirmpassword = null) : void {
        if(empty($email) || is_null($email)) {
            $this->errors['email'] = "Please fill the email";
        } else if ($this->checkEmail($email) == false) {
            $this->errors['email'] = "Please enter a valid email";
        } else if($this->emailExists($email)) {
            $this->errors['email'] = "Email already exists";
        }


        if(empty($password) || is_null($password)) {
            $this->errors['password'] = "Please fill the password";
        } else if(strlen($password) < 8) {
            $this->errors['password'] = "Password must be at least 8 characters";
        } else if($this->validatePassword($this->checkPassword($password))) {
            foreach($this->checkPassword($password) as $type => $hasType){
                if(!$hasType) $this->errors['passwordValid'][$type] = $type . " is required";
            }
        }

        if(empty($confirmpassword) || is_null($confirmpassword)) {
            $this->errors['confirmpassword'] = "Please fill the confirm password";
        } else if($confirmpassword != $password) {
            $this->errors['confirmpassword'] = "Passwords do not match";
        }

    }

    /**
     * Register user query
     * @param string $email
     * @param string $username
     * @param string $password
     * @return string 
    */
    private function registerUserQuery(string $firstname, string $middlename, string $lastname, string $email, string $password) : string {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (id, username, password) VALUES (UUID(), ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $email, $password);
        return $stmt->execute() ? $this->success('register') : $this->queryFailed();
    }


    /**
     * Check email
     * @param string $email
     * @return bool
    */
    private function checkEmail(string $email) : bool {
        $valid_names = [
            "gmail.com",
            "yahoo.com",
            "hotmail.com",
            "outlook.com"
        ];
        $emailName = explode("@", $email);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        if(!in_array(end($emailName), $valid_names)) return false;
        if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/", $email)) return false;
        return true;
    }

    /**
     * Email exists
     * @param string $email
     * @return bool
    */
    private function emailExists(string $email) : bool {
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
    private function checkPassword(string $password) : array {
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
    private function validatePassword(array $password) : bool {
  
        foreach (array_keys($password) as $hasType){
            $status = $hasType ? true : false;
        }
        return $status;
    }
}
?>