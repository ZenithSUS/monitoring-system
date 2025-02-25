<?php
include_once 'auth.php';

class Login extends Auth
{

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Login user
     * @param string $account
     * @param string $password
     * @return string
     */
    protected function loginUser(?string $email = null, ?string $password = null): string
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);

        if (!$stmt->execute()) {
            return $this->queryFailed();
        }


        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (!password_verify($password, $row['password'])) {
                $this->errors['auth_error'] = 'Email or password is incorrect';
                return $this->fieldError($this->errors);
            }

            $token = $this->generateToken();
            $this->insertToken($token, $row['id']);
            return $this->success('login', $token);
        }


        if (!$this->checkLoginAuth($email, $password)) {
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
    protected function checkLoginAuth(string $email): bool
    {
        $sql = "SELECT email FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Logout user
     * @param string $token
     * @return string
     */
    protected function logoutUser(?string $token): string
    {
        $this->deleteToken($token);
        return $this->success('logout');
    }
}
