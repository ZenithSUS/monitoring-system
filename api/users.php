<?php
include_once '../headers.php';
include_once '../authorization_headers.php';
include_once '../queries/users.php';

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$routeOptions = array("get-username");

class UsersRequest extends Users {
    
    public function __construct() {
        parent::__construct();
    }

    public function getUserAccount(string $token) : string {
        return json_encode(["username" => $this->getFullName($token)]);
    }

    public function get() : string {
        return $this->getUsers();
    }

    public function verifyUserToken(?string $token) : bool {
        return $this->verifyToken($token);
    }

    public function unauthorizedData() : string {
        return $this->unauthorized();
    }

    public function bad() : string {
        return $this->badRequest();
    }

}

$users = new UsersRequest();

if($requestMethod == 'OPTIONS') {
    http_response_code(200);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Authorization, X-Requested-With");
    exit();
}

if(!$users->verifyUserToken($token)) {
    echo $users->unauthorizedData();
    exit();
}

if($requestMethod == 'POST') {

    if($process && $process == 'get-username') {
        echo $users->getUserAccount($token);
    }

    if($process && $process == 'get-users') {
        echo $users->get();
    }

}

if(!in_array($process, $routeOptions)) {
    echo $users->bad();
}

?>