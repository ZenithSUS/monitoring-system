<?php
include_once 'token.php';

class Documents extends Token {

    protected function __construct()
    {
        parent::__construct();
    }

    protected function getDocuments(): string
    {
        $sql = "SELECT id, name, file_path, reference_number FROM documents";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function getDocument(?string $id): string
    {
        $sql = "SELECT id, name, file_path, reference_number FROM documents WHERE reference_number = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $this->fetched($result) : $this->notFound();
    }

    protected function addDocument(?string $name, ?string $file): string {
        $this->checkFields($name, $file);
        $this->createFile($file);

        if(!empty($this->errors)) {
            return $this->fieldError($this->errors);
        }

        $referenceId = $this->generateReferenceId();
        $file_path = $this->fileConfig['document']['uploadDir'] . $file;
        $sql = "INSERT INTO documents (name, file_path, reference_number) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('sss', $name, $file_path, $referenceId);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->created() : $this->badRequest();
    }

    protected function editDocument(?string $id, ?string $name, ?string $file_path): string {
        $sql = "UPDATE documents SET name = ?, file_path = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('ssi', $name, $file_path, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->edited() : $this->badRequest();
    }

    protected function deleteDocument(?string $id): string {
        $sql = "DELETE FROM documents WHERE id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            return $this->queryFailed();
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows > 0 ? $this->deleted() : $this->badRequest();
    }

    protected function generateReferenceId(): string {
        $year = date('Y');
        $randomString = bin2hex(random_bytes(16));
        return $year . '_' . $randomString;
    }

    // Function to create greek or group image
    private function createFile($file = null) : ?string {
        if($file === null || empty($file) || $file === "") {
            return null;
        }

        $fileName = $image['name'] ?? null;
        $fileSize = $image['size'] ?? null;
        $fileTmpName = $image['tmp_name'] ?? null;
        $fileError = $image['error'] ?? null;
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));
        $fileNameNew = uniqid('', true) . "." . $fileActualExt;
        $targetDirectory = $this->fileConfig['document'] . $fileNameNew; 
        
        $allowed = $this->fileConfig['document']['allowedTypes'];
    
        if ($fileError !== UPLOAD_ERR_OK) {
            $this->errors['fileCreate'] = 'Error Uploading Image!';
        }
    
        if (!in_array($fileActualExt, $allowed)) {
            $this->errors['fileCreate'] = 'Invalid Extension!';
        } 
    
        if ($fileSize > 5000000) {
            $this->errors['fileCreate'] = 'File size too big!';
        }

        if(!empty($this->errors)){
            return null;
        }

        if(!move_uploaded_file($fileTmpName, $targetDirectory)) {
            $this->errors['fileCreate'] = 'Failed to upload image';
            return null;
        }

        return $fileNameNew;
    }

    private function checkFields(?string $name, ?string $file): void {
        if(empty($name) || is_null($name)) {
            $this->errors['name'] = 'Name is required';
        } 

        if(empty($file) || is_null($file)) {
            $this->errors['file'] = 'File is required';
        }
    }
}
