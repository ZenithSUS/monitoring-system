<?php
include_once('../headers.php');
include_once('../queries/auth.php');
include_once('../queries/login.php');
include_once('../queries/register.php');

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$routeOptions = array("login", "register", "logout");

$token = $headers['Authorization'] ?? null;
if (isset($token) && strpos($token, 'Bearer ') !== false) {
    $token = explode(' ', $token)[1];
}

class AuthRequest extends Auth
{

    public function __construct()
    {
        parent::__construct();
    }

    public function bad(): string
    {
        return $this->badRequest();
    }

    public function unauthorized(): string
    {
        return $this->unauthorized();
    }
}

class LoginRequest extends Login
{

    public function __construct()
    {
        parent::__construct();
    }

    public function login(string $email, string $password): string
    {
        return $this->loginUser($email, $password);
    }


    public function logout(string $token): string
    {
        return $this->logoutUser($token);
    }
}

class RegisterRequest extends Register
{

    public function __construct()
    {
        parent::__construct();
    }

    public function register(?string $firstname, ?string $middlename = null, ?string $lastname = null, ?string $email = null, ?string $department = null, ?string $password = null, ?string $confirmpassword = null): void
    {
        $this->registerUser($firstname, $middlename, $lastname, $email, $department, $password, $confirmpassword);
    }
}


$auth = new AuthRequest();
$login = new LoginRequest();
$register = new RegisterRequest();

if ($requestMethod == "POST") {

    if ($process && $process == "login") {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        echo $login->login($email, $password);
    }

    if ($process && $process == "register") {
        $firstname = $_POST['first_name'] ?? null;
        $middlename = $_POST['middle_name'] ?? null;
        $lastname = $_POST['last_name'] ?? null;
        $email = $_POST['email'] ?? null;
        $department = $_POST['department'] ?? null;
        $password = $_POST['password'] ?? null;
        $confirmpassword = $_POST['confirmpassword'] ?? null;
        echo $register->register($firstname, $middlename, $lastname, $email, $department, $password, $confirmpassword);
    }

    if ($process && $process == "logout") {
        $token = $_POST['token'] ?? null;
        echo $login->logout($token);
    }
} else {
    echo $auth->bad();
}
