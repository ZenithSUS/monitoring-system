<?php
include_once 'db.php';

class API extends Database {
    protected $conn;
    protected $errors = array();
    protected $fileConfig = [
        "document" => [
            "maxSize" => 1000000,
            "allowedTypes" => ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx"],
            "uploadDir" => "../uploads/documents/"
        ]
    ];

    protected function __construct() {
        $this->conn = $this->connect();
    }

    /**
     * Success response
     * @param string $type
     * @param string $token
     * @return string
     */
    protected function success(string $type = null, string $token = null) : string {

        $response = array (
            'status' => 200,
            'message' => 'success'
        );

        if(isset($token)) $response['token'] = $token;
        if(isset($type)) $response['message'] = $type. ' successful';

        header("HTTP/1.1 200 OK");
        return json_encode($response);
    }

    /**
     * Created response
     * @return string
     */
    protected function created() : string {
        $response = array (
            'status' => 201,
            'message' => 'created'
        );
        header("HTTP/1.1 201 Created");
        return json_encode($response);
    }

    /**
     * Edited response
     * @return string
    */
    protected function edited() : string {
        $response = array (
            'status' => 200,
            'message' => 'edited'
        );
        header("HTTP/1.1 200 Edited");
        return json_encode($response);
    }

    /**
     * Deleted response
     * @return string
     */
    protected function deleted() : string {
        $response = array (
            'status' => 200,
            'message' => 'deleted'
        );
        header("HTTP/1.1 200 Deleted");
        return json_encode($response);
    }

    /**
     * Fetched response
     * @param string $type
     * @return string
    */
    protected function fetched($result, ?string $type = null, ?int $totalPages = null) : string {
        !$type ? $result = $result->fetch_all(MYSQLI_ASSOC) : $result = $result->fetch_assoc();
        
        $response = array (
            'status' => 200,
            'message' => 'Query successful',
            'data' => $result
        );
        if(isset($totalPages)) $response['totalPages'] = $totalPages;
        
        header("HTTP/1.1 200 OK");
        return json_encode($response);
    }

    /**
     * Query failed response
     * @return string
    */
    protected function queryFailed() : string {
        $response = array (
            'status' => 500,
            'message' => 'Query failed'
        );
        header("HTTP/1.1 500 Internal Server Error");
        return json_encode($response);
    }
    
    /**
     * Bad request response
     * @return string
    */
    protected function badRequest() : string {
        $response = array (
            'status' => 400,
            'message' => 'Bad Request'
        );
        header("HTTP/1.1 400 Bad Request");
        return json_encode($response);
    }
    
    /**
     * Unauthorized response
     * @return string
    */
    protected function unauthorized() : string {
        $response = array (
            'status' => 401,
            'message' => 'Unauthorized'
        );
        header("HTTP/1.1 401 Unauthorized");
        return json_encode($response);
    }

    /**
     * Not found response
     * @return string
    */
    protected function notFound() : string {
        $response = array (
            'status' => 404,
            'message' => 'Not Found'
        );
        header("HTTP/1.1 404 Not Found");
        return json_encode($response);
    }

    /**
     * Field error response
     * @param array $errors
     * @return string
    */
    protected function fieldError(array $errors) : string {
        $response = array(
            "status" => 422,
            "message" => "Unprocessable Content",
            "error" => $errors
        );
        header("HTTP/1.1 422 Unprocessable Content");
        return json_encode($response);
    }
}
?>
