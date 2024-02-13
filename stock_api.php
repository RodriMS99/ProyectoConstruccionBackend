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

        $materialId = isset($_GET['materialId']) ? $_GET['materialId'] : null;
        $stockId = isset($_GET['stockId']) ? $_GET['stockId'] : null;
        $endpointMethod = isset($_GET['method']) ? $_GET['method'] : null;
        
        // Custom endpoint to get stock by material ID
        if ($endpointMethod !== null)
        {
            if ($endpointMethod === "getStockByMaterialId")
            {
                if ($materialId === null)
                {
                    echo json_encode(array("error" => "Material ID not provided"));
                    break;
                }
                $sql = "SELECT s.StockId,m.MaterialID, m.MaterialName, m.Description, m.ImageUrl, p.ProviderID, p.ProviderName, s.Quantity, s.UnitPrice
                FROM materials m
                left JOIN stock s ON (s.materials_MaterialID = m.MaterialId)
                left JOIN providers p ON (p.ProviderId = s.providers_ProviderID)
                WHERE m.MaterialId = $materialId";
                
                $result = $conn->query($sql);
                $stock = array();
    
                while ($row = $result->fetch_assoc()) {
                    $stock[] = $row;
                }
                echo json_encode($stock);
            }

            if ($endpointMethod === "getStockByStockId")
            {
                if ($stockId === null)
                {
                    echo json_encode(array("error" => "Stock ID not provided"));
                    break;
                }
                $sql = "SELECT s.StockId,m.MaterialID, m.MaterialName, m.Description, m.ImageUrl, p.ProviderID, p.ProviderName, s.Quantity, s.UnitPrice
                FROM materials m
                left JOIN stock s ON (s.materials_MaterialID = m.MaterialId)
                left JOIN providers p ON (p.ProviderId = s.providers_ProviderID)
                WHERE s.StockId = $stockId";
                
                $result = $conn->query($sql);
                $stock = array();
    
                while ($row = $result->fetch_assoc()) {
                    $stock[] = $row;
                }
                if ($stockId === null) {
                    echo json_encode($stock);
                }else
                {
                    echo json_encode($stock[0]);
                }
                
            }
        }
        else
        {
            // Read operation
            $sql = "SELECT StockId, materials_MaterialID, providers_ProviderID, Quantity, LastUpdate, UnitPrice FROM stock;";
            $result = $conn->query($sql);
            $providers = array();

            while ($row = $result->fetch_assoc()) {
                $providers[] = $row;
            }
            echo json_encode($providers);
        }
        
        break;

    case 'POST':
        
        $materialId = $data['materialId'];
        $providerId = $data['providerId'];
        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];

/*

*/

        $sql = "INSERT INTO stock(materials_MaterialID, providers_ProviderID, Quantity, LastUpdate, UnitPrice) VALUES('$materialId', '$providerId', '$quantity', current_timestamp(), '$unitPrice')";
        $executionResult = $conn->query($sql);
        
        // Get the last inserted ID
        $newStockID = $conn->insert_id;
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "stock added successfully", "newStockID" => $newStockID));
        }
        else
        {
            echo "Error creating provider: " . $conn->error;
        }
        break;


        break;

    case 'PUT':
        $stockId = $data['stockId'];
        $quantity = $data['quantity'];
        $unitPrice = $data['unitPrice'];
        

        $sql = "UPDATE stock SET Quantity=$quantity, UnitPrice=$unitPrice WHERE StockId=$stockId;";
        $executionResult = $conn->query($sql);

        if ($executionResult === true)
        {
            echo json_encode(array("message" => "stock updated successfully"));
        }
        else
        {
            echo "Error creating provider: " . $conn->error;
        }
        
        break;

        break;

    case 'DELETE':
        echo "Error deleting stock: " . $conn->error;
        break;

    default:
        echo "Invalid request method";
}
$conn->close();
?>