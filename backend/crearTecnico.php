<?php 

include "conexion.php";

$emailTecnico = $_POST['email'];
$passwordTecnico = $_POST['password'];
$nombreTecnico = $_POST['nombre'];
$rol = 'tecnico';

$hash = password_hash($passwordTecnico, PASSWORD_DEFAULT);

$stmt = $conexion->prepare('INSERT INTO usuarios (Nombre, Email, Password, Rol) VALUES(?, ?, ?, ?)');
$stmt->bind_param("ssss", $nombreTecnico, $emailTecnico, $hash, $rol);
$stmt->execute();

echo("Tecnico creado exitosamente");

$stmt->close();
$conexion->close();

?>