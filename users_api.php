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

$endpoint = "/proyecto-construccion-back/users_api.php/authenticate";

// Check if the request is for authentication
if ($method === 'POST' && $_SERVER['REQUEST_URI'] === $endpoint) {
    $username = $data['userName'];
    $password = $data['password'];
    $sql = "SELECT UserId, UserName, Password, UserType FROM users WHERE UserName='$username' AND Password='$password'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(null);
    }
    return;
}

// CRUD operations based on the HTTP method
switch ($method)
{
    case 'GET':
        // Read operation
        $sql = "SELECT UserID, UserName, Password, UserType FROM users";

        $result = $conn->query($sql);
        $usuarios = array();

        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode($usuarios);
        break;

    case 'POST':
        // Create operation
        $userName = $data['userName'];
        $password = $data['password'];
        $userType = $data['userType'];

        // check if all parameters came

        $sql = "INSERT INTO users (UserName, Password, UserType) VALUES ('$userName', '$password', '$userType')";
        $executionResult = $conn->query($sql);
        
        // Get the last inserted ID
        $newUserId = $conn->insert_id;
        if ($executionResult === true)
        {
            echo json_encode(array("message" => "user added successfully", "newUserId" => $newUserId));
        }
        else
        {
            echo "Error creating user: " . $conn->error;
        }
        break;

    case 'PUT':
        // Update operation
        $userId = $data['userId'];
        $userName = $data['userName'];
        $password = $data['password'];
        $userType = $data['userType'];

        $sql = "UPDATE users SET User='$userName', password='$password', usertype='$userType' WHERE userid=$userId;";
        $executionResult = $conn->query($sql);

        if ($executionResult === true)
        {
            echo json_encode(array("message" => "User updated successfully"));
        }
        else
        {
            echo "Error creating user: " . $conn->error;
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