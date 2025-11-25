<?php
ini_set("display_errors", 0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Content-Type: application/json; charset=utf-8");


require 'conexion.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    
    case 'GET':

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM repuestos WHERE IDRepuesto = $id";
        $resultado = $conexion->query($sql);

        if (!$resultado) {
            echo json_encode(["error" => $conexion->error]);
            exit;
        }

        echo json_encode($resultado->fetch_assoc(), JSON_UNESCAPED_UNICODE);
        break;
    }

    // Listado completo
    $sql = "SELECT * FROM repuestos";
    $resultado = $conexion->query($sql);

    if (!$resultado) {
        echo json_encode(["error" => $conexion->error]);
        exit;
    }

    $repuestos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $repuestos[] = $fila;
    }

    echo json_encode($repuestos, JSON_UNESCAPED_UNICODE);
    break;


    
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);

        $nombre     = $conexion->real_escape_string($data['Nombre']);
        $categoria  = $conexion->real_escape_string($data['Categoria']);
        $desc       = $conexion->real_escape_string($data['Descripcion']);
        $stock      = intval($data['Stock']);
        $precio     = floatval($data['Precio']);
        $imagenBase64 = $conexion->real_escape_string($data['Imagen']);

        $sql = "INSERT INTO repuestos (Nombre, Categoria, Descripcion, Stock, Precio, Imagen)
                VALUES ('$nombre', '$categoria', '$desc', $stock, $precio, '$imagenBase64')";

        echo json_encode(["success" => $conexion->query($sql)], JSON_UNESCAPED_UNICODE);
        break;

    
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID requerido"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = intval($_GET['id']);

        $nombre     = $conexion->real_escape_string($data['Nombre']);
        $categoria  = $conexion->real_escape_string($data['Categoria']);
        $desc       = $conexion->real_escape_string($data['Descripcion']);
        $stock      = intval($data['Stock']);
        $precio     = floatval($data['Precio']);

        // Si trae imagen nueva, actualizarla
        if (isset($data['Imagen']) && $data['Imagen'] !== "") {
            $imagenBase64 = $conexion->real_escape_string($data['Imagen']);
            $sql = "UPDATE repuestos SET 
                        Nombre='$nombre', 
                        Categoria='$categoria', 
                        Descripcion='$desc',
                        Stock=$stock, 
                        Precio=$precio,
                        Imagen='$imagenBase64'
                    WHERE IDRepuesto = $id";
        } else {
            // Si NO se cambia la imagen
            $sql = "UPDATE repuestos SET 
                        Nombre='$nombre', 
                        Categoria='$categoria', 
                        Descripcion='$desc',
                        Stock=$stock, 
                        Precio=$precio
                    WHERE IDRepuesto = $id";
        }

        echo json_encode(["success" => $conexion->query($sql)], JSON_UNESCAPED_UNICODE);
        break;

    
    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID requerido"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = intval($_GET['id']);
        $sql = "DELETE FROM repuestos WHERE IDRepuesto = $id";

        echo json_encode(["success" => $conexion->query($sql)], JSON_UNESCAPED_UNICODE);
        break;

    
    case 'OPTIONS':
        http_response_code(200);
        break;
}

$conexion->close();
