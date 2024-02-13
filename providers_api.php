<?php

// Database connection
$servername = "localhost:3306";
$username = "rodrigo";
$password = "1234";
$dbname = "ventasmaterialesconstruccion";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

// Get the HTTP method (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);

// CRUD operations based on the HTTP method
switch ($method)
{
    case 'GET':
        // Read operation
        // $sql = "SELECT ProviderID, ProviderName, ContactName, ContactEmail, ContactPhone FROM providers;";
        // $result = $conn->query($sql);
        // $providers = array();

        // while ($row = $result->fetch_assoc()) {
        //     $providers[] = $row;
        // }
        // echo json_encode($providers);
        // break;

        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id !== null) {
            // If an ID is provided, fetch only the material with that ID
            $sql = "SELECT m.ProviderID, m.ProviderName, m.ContactName, m.ContactEmail, m.ContactPhone
                    FROM providers m
                    WHERE m.ProviderId = $id";
        } else {
            // If no ID is provided, fetch all materials
            $sql = "SELECT m.ProviderID, m.ProviderName, m.ContactName, m.ContactEmail, m.ContactPhone
                    FROM providers m";
        }
        $result = $conn->query($sql);
        $providers = array();

        while ($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }
        //check in the materials array has more than one element
        if (empty($providers)){
            echo json_encode(null);
        }elseif (count($providers) == 1 && $id !== null) {
            echo json_encode($providers[0]);
        } else {
        echo json_encode($providers);
        }
        
        break;

    case 'POST':
        // Create operation
        $providerName = $data['providerName'];
        $contactName = $data['contactName'];
        $contactEmail = $data['contactEmail'];
        $contactPhone = $data['contactPhone'];

        $sql = "INSERT INTO providers (ProviderName, ContactName, ContactEmail, ContactPhone) VALUES ('$providerName', '$contactName', '$contactEmail', '$contactPhone')";
        $executionResult = $conn->query($sql);
        
        // Get the last inserted ID
        $newMaterialID = $conn->insert_id;
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Provider added successfully", "newMaterialID" => $newMaterialID));
        }
        else
        {
            echo "Error creating provider: " . $conn->error;
        }
        break;

    case 'PUT':
        // Update operation
        $providerId = $data['providerId'];
        $providerName = $data['providerName'];
        $contactName = $data['contactName'];
        $contactEmail = $data['contactEmail'];
        $contactPhone = $data['contactPhone'];

        $sql = "UPDATE providers SET ProviderName='$providerName', ContactName='$contactName', ContactEmail='$contactEmail', ContactPhone='$contactPhone' WHERE ProviderID=$providerId;";
        $executionResult = $conn->query($sql);

        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Provider updated successfully"));
        }
        else
        {
            echo "Error creating provider: " . $conn->error;
        }
        
        break;

    case 'DELETE':
        // Delete operation        
        $providerId = $data['providerId'];

        $sql = "DELETE FROM providers WHERE ProviderID=$providerId";
        $executionResult = $conn->query($sql);
        
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Provider deleted successfully"));
        }
        else
        {
            echo "Error creating provider: " . $conn->error;
        }
        
        break;
}
$conn->close();
?>