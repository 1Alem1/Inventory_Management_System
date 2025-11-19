<?php 

$host = "localhost";
$user = "root";
$pass = "";
$nombrebd = "inventario";

$conexion = new mysqli($host, $user, $pass, $nombrebd);

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión: " . $conexion->connect_error]);
    exit();
}
$conexion->set_charset("utf8mb4");


?>