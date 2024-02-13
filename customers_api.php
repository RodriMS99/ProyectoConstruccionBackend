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
        $sql = "SELECT CustomerID, CustomerName, ContactName, ContactEmail, ContactPhone FROM customers;";
        $result = $conn->query($sql);
        $customers = array();

        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
        echo json_encode($customers);
        break;

    case 'POST':
        // Create operation
        $customerName = $data['customerName'];
        $contactName = $data['contactName'];
        $contactEmail = $data['contactEmail'];
        $contactPhone = $data['contactPhone'];

        $sql = "INSERT INTO customers (CustomerName, ContactName, ContactEmail, ContactPhone) VALUES('$customerName', '$contactName', '$contactEmail', '$contactPhone');";
        $executionResult = $conn->query($sql);
        
        // Get the last inserted ID
        $newMaterialID = $conn->insert_id;
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Customer added successfully", "newMaterialID" => $newMaterialID));
        }
        else
        {
            echo "Error creating customer: " . $conn->error;
        }
        break;

    case 'PUT':
        // Update operation
        $customerId = $data['customerId'];
        $customerName = $data['customerName'];
        $contactName = $data['contactName'];
        $contactEmail = $data['contactEmail'];
        $contactPhone = $data['contactPhone'];

        $sql = "UPDATE customers SET CustomerName='$customerName', ContactName='$contactName', ContactEmail='$contactEmail', ContactPhone='$contactPhone' WHERE CustomerID=$customerId;";
        $executionResult = $conn->query($sql);

        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Customer updated successfully"));
        }
        else
        {
            echo "Error creating customer: " . $conn->error;
        }
        
        break;

    case 'DELETE':
        // Delete operation
        $customerId = $data['customerId'];

        $sql = "DELETE FROM customers WHERE CustomerID=$customerId";
        $executionResult = $conn->query($sql);
        
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Customer deleted successfully"));
        }
        else
        {
            echo "Error creating customer: " . $conn->error;
        }
        
        break;
}
$conn->close();
?>