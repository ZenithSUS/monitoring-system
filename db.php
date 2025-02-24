<?php
include_once 'config.php';

class Database {

    protected function __construct(){
        $this->connect();
    }

    protected function connect() : mysqli {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        return $this->checkConnection($conn);
    }

    protected function checkConnection($conn) : mysqli {
        if($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }

    protected function closeConnection($conn) : void {
        $conn->close();
    }
}

?>