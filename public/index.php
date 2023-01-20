<?php

require '../vendor/autoload.php';

use App\Modules\Response;
use App\Controllers\RefreshTokenController;
use App\Controllers\LoginController;
use App\Controllers\RegisterController;

//== Response construct ==//
function constructResponse(Response $response)
{
    header('Content-Type:application/json');
    http_response_code($response->statusCode);
    echo json_encode($response->message);
}

//== Database connection ==//
$host = "localhost";
$dbname = "gerenciamento_riscos";
$username = "root";
$password = "123456789";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $pe) {
    return constructResponse(Response::error("Could not connect to the database die $dbname:" . $pe->getMessage()));
}

//== Start router ==//
$firstUrl = substr($_SERVER['REQUEST_URI'], 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = match ($firstUrl) {
        'login' => LoginController::post($conn, $_POST),
        'cadastro' => RegisterController::post($conn, $_POST),
        'refresh-token' => RefreshTokenController::post($conn, $_POST),
        // 'delete-token' => LoginController::deleteToken($_POST),
        default => Response::error('rota invalida')
    };
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $response = match ($firstUrl) {
        default => Response::error('rota invalida')
    };
} else {
    $response = Response::error('rota invalida');
}

//== Return response ==//
return constructResponse($response);
