<?php
include_once 'auth.php';

class Register extends Auth
{

    protected function __construct() {
        parent::__construct();
    }

     /**
     * Register user
     * @param string $firstname
     * @param string $middlename
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param string $confirmpassword
     * @return void
     * @throws Exception
     */
    protected function registerUser(?string $firstname = null, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $password = null, ?string $confirmpassword = null) : void {
        $this->checkFields($firstname, $middlename, $lastname, $email, $password, $confirmpassword);

        try {
            if(!empty($this->errors)) {
                throw new Exception(
                    json_encode(
                        array(
                            "status" => 400,
                            "message" => "There is something wrong with the data"
                        )));
            }
            $this->registerUserQuery($firstname, $middlename, $lastname, $email, $password);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Register user query
     * @param string $firstname
     * @param string $middlename
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @return string 
    */
    private function registerUserQuery(?string $firstname = null, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $password = null) : void {
        $firstname = $this->conn->real_escape_string($firstname);
        $middlename = $this->conn->real_escape_string($middlename);
        $lastname = $this->conn->real_escape_string($lastname);
        $email = $this->conn->real_escape_string($email);
        $password = $this->conn->real_escape_string($password);

        try {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (id, first_name, middle_name, last_name, email, password) VALUES (UUID(), ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('sssss', $firstname, $middlename, $lastname, $email, $password);
            if (!$stmt->execute()) {
                throw new Exception($this->queryFailed());
            }
            echo $this->success('register');
        } catch (Exception $e) {
            echo $e->getMessage();
        }
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
    protected function checkFields(?string $firstname = null, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $password = null, ?string $confirmpassword = null) : void {
        if(empty($firstname) || is_null($firstname)) {
            $this->errors['firstname'] = "Please fill the first name";
        } elseif(strlen($firstname) < 2) {
            $this->errors['firstname'] = "First name must be at least 2 characters";
        } else if(!preg_match("/^[a-zA-Z ]*$/", $firstname)) {
            $this->errors['firstname'] = "Only letters and white space allowed";
        }

        if(!empty($middlename) && !is_null($middlename)) {
            if(strlen($middlename) < 2) {
                $this->errors['middlename'] = "Middle name must be at least 2 characters";
            } else if(!preg_match("/^[a-zA-Z ]*$/", $middlename)) {
                $this->errors['middlename'] = "Only letters and white space allowed";
            }
        }

        if(empty($lastname) || is_null($lastname)) {
            $this->errors['lastname'] = "Please fill the last name";
        } elseif(strlen($lastname) < 2) {
            $this->errors['lastname'] = "Last name must be at least 2 characters";
        } else if(!preg_match("/^[a-zA-Z ]*$/", $lastname)) {
            $this->errors['lastname'] = "Only letters and white space allowed";
        }

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
}
?>