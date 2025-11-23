<?php 

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$response = [
    'authenticated' => false,
    'rol' => null,
    'email' => null
];

if (isset($_SESSION['email']) && isset($_SESSION['rol'])) {
    $response['authenticated'] = true;
    $response['rol'] = $_SESSION['rol'];
    $response['email'] = $_SESSION['email'];
}

echo json_encode($response);
?>