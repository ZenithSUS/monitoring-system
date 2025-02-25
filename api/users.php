<?php
include_once '../headers.php';
include_once '../authorization_headers.php';
include_once '../queries/users.php';

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$routeOptions = array("get-username", "get-users", "get-user");

$token = $headers['Authorization'] ?? null;
if(isset($token) && strpos($token, 'Bearer ') !== false) {
    $token = explode(' ', $token)[1];
}


class UsersRequest extends Users {
    
    public function __construct() {
        parent::__construct();
    }

    public function getUserAccount(string $token) : string {
        return json_encode(["username" => $this->getFullName($token)]);
    }

    public function getAll() : string {
        return $this->getUsers();
    }

    public function get(?string $id = null) : string {
        return $this->getUser($id);
    }

    public function verifyUserToken(?string $token = null) : bool {
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
        echo $users->getAll();
    }

    if(!in_array($process, $routeOptions)) {
        echo $users->bad();
    }
}

if($requestMethod == 'GET') {
    if(isset($_GET['id'])) {
        $id = $_GET['id'] ?? null;
        echo $users->get($id);
    } else {
        echo $users->bad();
    }
}

?>