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

$logFilePath = "traces.log"; // Specify the path to your log file
$logMessage = date('Y-m-d H:i:s') . " #### ----> stock_api.php:" . json_encode($method) . PHP_EOL;

file_put_contents($logFilePath, $logMessage, FILE_APPEND);

// Get data from the request
$data = json_decode(file_get_contents("php://input"), true);

// CRUD operations based on the HTTP method
switch ($method)
{
    case 'GET':

        
        
        break;

    case 'POST':

        // Create operation
/*
const dataToInsert = { "materialId": material?.MaterialID, "providerId": material?.ProviderId, "customerId": customerId };
*/

        $customerId = $data['customerId'];
        $providerId = $data['providerId'];
        $materialId = $data['materialId'];
        $quantity = $data['quantity'];
        //buscar primero elbudget del cliente, si no existe, lo creamos
        $sql = "Select BudgetID From budgets Where customers_CustomerID = $customerId";
        $budgetId = 0;        
        
        $result = $conn->query($sql);
        $materials = array();

        while ($row = $result->fetch_assoc()) {
            $materials[] = $row;
        }
        //check in the materials array has more than one element
        if (empty($materials))
        {
            //create the budget
            $sql = "INSERT INTO Budgets (customers_CustomerID) VALUES ($customerId)";
            $executionResult = $conn->query($sql);
            // Get the last inserted ID
            $budgetId = $conn->insert_id;
        }else
        {
            $budgetId = json_encode($materials[0]);
        }

        $sql = "INSERT INTO budgetsdetail (BudgetID, materials_MaterialID, providers_ProviderID) VALUES ($budgetId, $materialId, $providerId, $quantity )";
        $executionResult = $conn->query($sql);
        $echo ($conn->insert_id) > 0;
        break;

    case 'PUT':
       

        break;

    case 'DELETE':
        echo "Error deleting stock: " . $conn->error;
        break;

    default:
        echo "Invalid request method";
}
$conn->close();
?>