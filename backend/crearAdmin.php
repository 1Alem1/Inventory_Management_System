<?php


header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

include 'conexion.php';


$emailAdmin = "admin@gmail.com";
$passwordAdmin = "admin123";
$nombre = "Mateo";


$hash = password_hash($passwordAdmin, PASSWORD_DEFAULT);
$stmt = $conexion->prepare("INSERT INTO usuarios (Nombre, Email, Password, Rol) VALUES (?, ?, ?, ?)");
$rol = "admin";
$stmt->bind_param("ssss", $nombre, $emailAdmin, $hash, $rol);
$stmt->execute();

echo "Administrador creado exitosamente.";

$stmt->close();
$conexion->close();



?>