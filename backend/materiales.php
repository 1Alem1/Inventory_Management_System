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
        $imagen = $conexion->real_escape_string($data['Imagen']);

        $sql = "INSERT INTO repuestos (Nombre, Categoria, Descripcion, Stock, Precio, Imagen)
                VALUES ('$nombre', '$categoria', '$desc', $stock, $precio, '$imagen')";

        $ok = $conexion->query($sql);

        if (!$ok) {
            echo json_encode(["error" => $conexion->error]);
            exit;
        }

        $idRepuesto = $conexion->insert_id;

        $admin = 1;
        $tipo = 1; 

        $stmtMovimiento = $conexion->prepare("INSERT INTO movimientos (IDRepuesto, Tipo, Fecha, IDUser, IDPedido) VALUES (?, ?, NOW(), ?, NULL)");
        $stmtMovimiento->bind_param("iii", $idRepuesto, $tipo, $admin);
        
        if (!$stmtMovimiento->execute()) {
            echo json_encode(["error" => "Repuesto creado pero error al registrar movimiento: " . $stmtMovimiento->error]);
            exit;
        }

        echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
        break;

    
case 'PUT':
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($_GET['id'])) {
        echo json_encode(["error" => "ID requerido"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $id = intval($_GET['id']);

    $sqlStockActual = "SELECT Stock FROM repuestos WHERE IDRepuesto = $id";
    $resultStock = $conexion->query($sqlStockActual);
    $repuestoActual = $resultStock->fetch_assoc();
    $stockAnterior = $repuestoActual['Stock'];

    $nombre     = $conexion->real_escape_string($data['Nombre']);
    $categoria  = $conexion->real_escape_string($data['Categoria']);
    $desc       = $conexion->real_escape_string($data['Descripcion']);
    $stockNuevo = intval($data['Stock']);
    $precio     = floatval($data['Precio']);
    $imagen     = $conexion->real_escape_string($data['Imagen']);

    $conexion->begin_transaction();

    try {
        $sql = "UPDATE repuestos SET 
                    Nombre='$nombre', 
                    Categoria='$categoria', 
                    Descripcion='$desc',
                    Stock=$stockNuevo, 
                    Precio=$precio,
                    Imagen='$imagen'
                WHERE IDRepuesto = $id";

        $resultado = $conexion->query($sql);

        if (!$resultado) {
            throw new Exception($conexion->error);
        }

        if ($stockNuevo > $stockAnterior) {
            $admin = 1; 
            $tipo = 1; 

            $stmtMovimiento = $conexion->prepare("INSERT INTO movimientos (IDRepuesto, Tipo, Fecha, IDUser, IDPedido) VALUES (?, ?, NOW(), ?, NULL)");
            $stmtMovimiento->bind_param("iii", $id, $tipo, $admin);
            
            if (!$stmtMovimiento->execute()) {
                throw new Exception("Error al registrar movimiento: " . $stmtMovimiento->error);
            }
        }

        $conexion->commit();
        echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        $conexion->rollback();
        echo json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    break;

    
    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "ID requerido"], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $id = intval($_GET['id']);
    $conexion->begin_transaction();
    
    try {
        $sqlMovimientos = "DELETE FROM movimientos WHERE IDRepuesto = $id";
        $conexion->query($sqlMovimientos);
        
        $sqlItems = "DELETE FROM item WHERE IDRepuesto = $id";
        $conexion->query($sqlItems);
    
        $sqlRepuesto = "DELETE FROM repuestos WHERE IDRepuesto = $id";
        $resultRepuesto = $conexion->query($sqlRepuesto);
        
        if (!$resultRepuesto) {
            throw new Exception($conexion->error);
        }

        $conexion->commit();
        
        echo json_encode(["success" => true], JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {

        $conexion->rollback();
        echo json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    }
    break;
}

$conexion->close();
