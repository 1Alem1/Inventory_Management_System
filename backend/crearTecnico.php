<?php 

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");


include 'conexion.php';

$nombreTecnico = $_POST['nombre'] ?? '';
$emailTecnico = $_POST['email'] ?? '';
$passwordTecnico = $_POST['password'] ?? '';

if (empty($nombreTecnico) || empty($emailTecnico) || empty($passwordTecnico)) {
    http_response_code(400);
    echo json_encode(['error' => 'Todos los campos son obligatorios']);
    exit();
}

$stmtCheck = $conexion->prepare('SELECT IDUser FROM usuarios WHERE Email = ?');
$stmtCheck->bind_param("s", $emailTecnico);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['error' => 'El email ya está registrado']);
    $stmtCheck->close();
    $conexion->close();
    exit();
}
$stmtCheck->close();

$rol = 'tecnico';
$hash = password_hash($passwordTecnico, PASSWORD_DEFAULT);

$stmt = $conexion->prepare('INSERT INTO usuarios (Nombre, Email, Password, Rol) VALUES(?, ?, ?, ?)');
$stmt->bind_param("ssss", $nombreTecnico, $emailTecnico, $hash, $rol);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Técnico creado exitosamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al crear el técnico']);
}

$stmt->close();
$conexion->close();

?>