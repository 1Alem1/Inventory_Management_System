<?php 

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