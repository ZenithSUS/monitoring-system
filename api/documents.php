<?php
include_once '../headers.php';
include_once '../authorization_headers.php';
include_once '../queries/documents.php';

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$postOptions = array("get-documents", "get-document", "add-document", "edit-document", "delete-document");

$token = $headers['Authorization'] ?? null;
if($token && strpos($token, 'Bearer ') !== false) {
    $token = explode(' ', $token)[1];
}

class DocumentsRequest extends Documents
{
    public function __construct() {
        parent::__construct();
    }

    public function getAll() : string {
        return $this->getDocuments();
    }

    public function get(?string $id = null) : string {
        return $this->getDocument($id);
    }

    public function add(?string $name = null, $file = null) : string {
        return $this->addDocument($name, $file);
    }

    public function edit(?string $id = null, ?string $name = null, $file = null) : string {
        return $this->editDocument($id, $name, $file);
    }

    public function delete(?string $id = null) : string {
        return $this->deleteDocument($id);
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

$documents = new DocumentsRequest();

if($requestMethod == 'OPTIONS') {
    http_response_code(200);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Authorization, X-Requested-With");
    exit();
}

if(!$documents->verifyUserToken($token)) {
    echo $documents->unauthorizedData();
    exit();
}

if($requestMethod == 'POST') {
    if(!in_array($process, $postOptions)) {
        echo $documents->bad();
        exit();
    }

    if($process == 'get-documents') {
        echo $documents->getAll();
    } else if($process == 'add-document') {
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;
        echo $documents->add($name, $file_path);
    } else if($process == 'edit-document') {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $file = isset($_FILES['file']) ? $_FILES['file'] : null;
        echo $documents->edit($id, $name, $file_path);
    } else if($process == 'delete-document') {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        echo $documents->delete($id);
    }
} else if($requestMethod == 'GET') {
    if(isset($_GET['id'])) {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        echo $documents->get($id);
    } else {
        echo $documents->bad();
    }
} else {
    echo $documents->bad();
}


?>