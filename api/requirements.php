<?php
include_once '../headers.php';
include_once '../authorization_headers.php';
include_once '../queries/requirements.php';

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? null;
$process = isset($_POST['process']) ? $_POST['process'] : null;
$postOptions = array("get-requirements", "get-requirement", "add-requirement", "edit-requirement", "delete-requirement");

$token = $headers['Authorization'] ?? null;
if($token && strpos($token, 'Bearer ') !== false) {
    $token = explode(' ', $token)[1];
}

class RequirementsRequest extends Requirements
{
    public function __construct() {
        parent::__construct();
    }

    public function getAll() : string {
        return $this->getRequirements();
    }

    public function get(?string $id = null) : string {
        return $this->getRequirement($id);
    }

    public function add(?string $loc = null, ?string $departmentId = null, ?string $entityName = null, 
    ?string $frequencyOfCompliance = null, ?string $dateSubmitted = null, 
    ?string $expiration = null, ?string $renewal = null, 
    ?string $personInCharge = null, ?string $status = null) : string {
        return $this->addRequirement($loc, $departmentId, $entityName, $frequencyOfCompliance, $dateSubmitted, $expiration, $renewal, $personInCharge, $status);
    }

    public function edit(?string $id = null, ?string $loc = null, ?string $departmentId = null,
    ?string $entityName, ?string $frequencyOfCompliance,
    ?string $dateSubmitted, ?string $expiration, ?string $renewal, ?string $personInCharge,
    ?string $status = null) : string {
        return $this->editRequirement($id, $loc, $departmentId, $entityName, $frequencyOfCompliance, $dateSubmitted, $expiration, $renewal, $personInCharge, $status);
    }

    public function delete(?string $id = null) : string {
        return $this->deleteRequirement($id);
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

$requirements = new RequirementsRequest();

if($requestMethod == 'OPTIONS') {
    http_response_code(200);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Authorization, X-Requested-With");
}


else if($requestMethod == 'POST') {
    if(in_array($process, $postOptions)) {
        if($requirements->verifyUserToken($token)) {
            $id = isset($_POST['id']) ? $_POST['id'] : null;
            $loc = isset($_POST['LOC']) ? $_POST['LOC'] : null;
            $departmentId = isset($_POST['departmentId']) ? $_POST['departmentId'] : null;
            $entityName = isset($_POST['entityName']) ? $_POST['entityName'] : null;
            $frequencyOfCompliance = isset($_POST['frequencyOfCompliance']) ? $_POST['frequencyOfCompliance'] : null;
            $dateSubmitted = isset($_POST['dateSubmitted']) ? $_POST['dateSubmitted'] : null;
            $expiration = isset($_POST['expiration']) ? $_POST['expiration'] : null;
            $renewal = isset($_POST['renewal']) ? $_POST['renewal'] : null;
            $personInCharge = isset($_POST['personInCharge']) ? $_POST['personInCharge'] : null;
            $status = isset($_POST['status']) ? $_POST['status'] : null;

            switch($process) {
                case 'get-requirements':
                    echo $requirements->getAll();
                    break;
                case 'get-requirement':
                    echo $requirements->get($id);
                    break;
                case 'add-requirement':
                    echo $requirements->add($loc, $departmentId, $entityName, $frequencyOfCompliance, $dateSubmitted, $expiration, $renewal, $personInCharge, $status);
                    break;
                case 'edit-requirement':
                    echo $requirements->edit($id, $loc, $departmentId, $entityName, $frequencyOfCompliance, $dateSubmitted, $expiration, $renewal, $personInCharge, $status);
                    break;
                case 'delete-requirement':
                    echo $requirements->delete($id);
                    break;
                default:
                    echo $requirements->bad();
                    break;
            }
        }
        else {
            echo $requirements->unauthorizedData();
        }
    }
    else {
        echo $requirements->bad();
    }
}
else {
    echo $requirements->bad();
}

?>