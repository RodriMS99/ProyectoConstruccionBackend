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

$logFilePath = "traces.log"; // Specify the path to your log file
$logMessage = date('Y-m-d H:i:s') . " - " . json_encode($data) . PHP_EOL;

//file_put_contents($logFilePath, $logMessage, FILE_APPEND);

// CRUD operations based on the HTTP method
switch ($method)
{
    case 'GET':
        // Read operation
        //$sql = "select m.MaterialId,m.MaterialName,m.Description,m.ImageUrl,p.ProviderName,s.Quantity,s.UnitPrice from materials m inner join stock s on (s.MaterialID = m.MaterialId) inner join providers p on (p.ProviderId = s.ProviderId);";
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id !== null) {
            // If an ID is provided, fetch only the material with that ID
            $sql = "SELECT m.MaterialID, m.MaterialName, m.Description, m.ImageUrl
                    FROM materials m
                    WHERE m.MaterialId = $id";
        } else {
            // If no ID is provided, fetch all materials
            $sql = "SELECT m.MaterialID,s.providers_ProviderID as ProviderId, p.ProviderName, m.MaterialName, m.Description, m.ImageUrl, s.UnitPrice
                    FROM materials m Inner Join stock s on (s.materials_MaterialID = m.MaterialID)
                    Inner Join providers p On (p.ProviderID = s.providers_ProviderID) ORder by m.MaterialName";
        }
        $result = $conn->query($sql);
        $materials = array();

        while ($row = $result->fetch_assoc()) {
            $materials[] = $row;
        }
        //check in the materials array has more than one element
        if (empty($materials)){
            echo json_encode(null);
        }elseif (count($materials) == 1 && $id !== null) {
            echo json_encode($materials[0]);
        } else {
        echo json_encode($materials);
        }
        
        break;

    case 'POST':
        // Create operation
        $materialName = $data['materialName'];
        $description = $data['description'];
        $imageUrl = $data['imageUrl'];

        $sql = "INSERT INTO Materials (MaterialName, Description, ImageUrl) VALUES ('$materialName', '$description', '$imageUrl')";
        $executionResult = $conn->query($sql);
        
        // Get the last inserted ID
        $newMaterialID = $conn->insert_id;
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Material added successfully", "newMaterialID" => $newMaterialID));
        }
        else
        {
            echo "Error creating material: " . $conn->error;
        }
        break;

    case 'PUT':
        // Update operation
        $materialID = $data['materialId'];
        $materialName = $data['materialName'];
        $description = $data['description'];
        $imageUrl = $data['imageUrl'];
        $imageFile = $data['imageFile'];


        $sql = "UPDATE Materials SET MaterialName='$materialName', Description='$description', ImageUrl='$imageUrl' WHERE MaterialID=$materialID";
        $executionResult = $conn->query($sql);

        if ($executionResult === true)
        {
            file_put_contents($logFilePath, "#####--->VAMOS A SUBIR EL FICHERO". PHP_EOL, FILE_APPEND);

            try {
                file_put_contents($logFilePath, "#####--->INICIO TRY". PHP_EOL, FILE_APPEND);
                $folderPath = "uploads/";
                file_put_contents($logFilePath, "#####--->PASO 1 HECHO". PHP_EOL, FILE_APPEND);
                
                $image_parts = explode(";base64,", $imageFile);
                file_put_contents($logFilePath, "#####--->PASO 2 HECHO". PHP_EOL, FILE_APPEND);

                $image_type_aux = explode("image/", $image_parts[0]);
                file_put_contents($logFilePath, "#####--->PASO 3 HECHO". PHP_EOL, FILE_APPEND);

                $image_base64 = base64_decode($image_parts[1]);
                file_put_contents($logFilePath, "#####--->PASO 4 HECHO". PHP_EOL, FILE_APPEND);

                $file = $folderPath . $imageUrl;
                file_put_contents($logFilePath, "#####--->PASO 5 HECHO". PHP_EOL, FILE_APPEND);

                if(file_put_contents($file, $image_base64))
                {
                    file_put_contents($logFilePath, "#####--->fichero subido con éxito!!!". PHP_EOL, FILE_APPEND);
                }    //code...
            } catch (Exception $e) {
                //print the error
                file_put_contents($logFilePath, "#####--->ERROR: ".$e->getMessage(). PHP_EOL, FILE_APPEND);
            }
            file_put_contents($logFilePath, "#####--->FIN TRY". PHP_EOL, FILE_APPEND);
            

            echo json_encode(array("message" => "Material updated successfully"));
        }
        else
        {
            echo "Error creating material: " . $conn->error;
        }
        
        break;

    case 'DELETE':
        // Delete operation
        $materialID = $data['materialId'];

        $sql = "DELETE FROM Materials WHERE MaterialID=$materialID";
        $executionResult = $conn->query($sql);
        
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "Material deleted successfully"));
        }
        else
        {
            echo "Error creating material: " . $conn->error;
        }
        
        break;
}
$conn->close();
?>