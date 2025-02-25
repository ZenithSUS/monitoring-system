<?php
include_once('../headers.php');
include_once('../queries/auth.php');

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$routeOptions = array("login", "register", "logout");

class AuthRequest extends Auth {

    public function __construct(){
        parent::__construct();
    }

    public function login(string $email, string $password) : string {
        return $this->loginUser($email, $password);
    }

    public function register(?string $firstname, ?string $middlename, ?string $lastname, ?string $email = null, ?string $password = null, ?string $confirmpassword = null) : string {
        return $this->registerUser($firstname, $middlename, $lastname, $email, $password, $confirmpassword);
    }

    public function logout(string $token) : string {
        return $this->logoutUser($token);
    }

    public function bad() : string {
        return $this->badRequest();
    }
}

$auth = new AuthRequest();

if($requestMethod == "POST") {

    if($process && $process == "login") {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        echo $auth->login($email, $password);
    }

    if($process && $process == "register") {
        $firstname = $_POST['first_name'] ?? null;
        $middlename = $_POST['middle_name'] ?? null;
        $lastname = $_POST['last_name'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmpassword = $_POST['confirmpassword'] ?? null;
        echo $auth->register($email, $password, $confirmpassword);
    }

    if($process && $process == "logout") {
        $token = $_POST['token'] ?? null;
        echo $auth->logout($token);
    }


if(!in_array($process, $routeOptions)) {
    echo $auth->bad();
}
}


?>