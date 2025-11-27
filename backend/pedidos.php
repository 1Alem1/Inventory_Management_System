<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

require 'conexion.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isset($_SESSION['email']) || !isset($_SESSION['rol'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

$emailUsuario = $_SESSION['email'];
$stmtUser = $conexion->prepare("SELECT IDUser FROM usuarios WHERE Email = ?");
$stmtUser->bind_param("s", $emailUsuario);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

$user = $resultUser->fetch_assoc();

if (!$user) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit();
}

$idUser = $user['IDUser'];
$stmtUser->close();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $idPedido = intval($_GET['id']);

        $sql = "SELECT pedidos.IDPedido, pedidos.IDUser, usuarios.Nombre, pedidos.Fecha, pedidos.Estado 
        FROM pedidos 
        INNER JOIN usuarios ON pedidos.IDUser = usuarios.IDUser 
        WHERE pedidos.IDPedido = ?";

        if ($_SESSION['rol'] !== 'admin') {
            $sql .= " AND pedidos.IDUser = ?";
        }

        $stmt = $conexion->prepare($sql);

        if ($_SESSION['rol'] !== 'admin') {
            $stmt->bind_param("ii", $idPedido, $idUser);
        } else {
            $stmt->bind_param("i", $idPedido);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $pedido = $resultado->fetch_assoc();

        if (!empty($pedido['Fecha'])) {
            $fecha = new DateTime($pedido['Fecha']);
            $fecha->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
            $pedido['Fecha'] = $fecha->format('d-m-y');
        }

        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['error' => 'Pedido no encontrado']);
            exit();
        }

        $sqlItems = "SELECT item.IDItem, item.IDPedido, item.IDRepuesto, item.Cantidad, repuestos.Nombre, repuestos.Categoria, repuestos.Precio, (item.Cantidad * repuestos.Precio) AS Monto FROM item INNER JOIN repuestos ON item.IDRepuesto = repuestos.IDRepuesto WHERE IDPedido = ?";
        $stmtItems = $conexion->prepare($sqlItems);
        $stmtItems->bind_param("i", $idPedido);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();

        $items = [];

        while ($item = $resultItems->fetch_assoc()) {
            $items[] = $item;
        }

        $pedido['items'] = $items;

        echo json_encode($pedido, JSON_UNESCAPED_UNICODE);
    } else {
        $sql = "SELECT pedidos.IDPedido, pedidos.IDUser, usuarios.Nombre, pedidos.Fecha, pedidos.Estado, (SELECT COUNT(*) FROM item WHERE IDPedido = pedidos.IDPedido) AS totalItems FROM pedidos INNER JOIN usuarios ON pedidos.IDUser = usuarios.IDUser";

        if ($_SESSION['rol'] === 'tecnico') {
            $sql .= " WHERE pedidos.IDUser = ?";
        }

        $sql .= " ORDER BY pedidos.Fecha DESC";

        if ($_SESSION['rol'] === 'tecnico') {
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $idUser);
            $stmt->execute();
            $resultado = $stmt->get_result();
        } else {
            $resultado = $conexion->query($sql);
        }

        $pedidos = [];

        while ($pedido = $resultado->fetch_assoc()) {


            if (!empty($pedido['Fecha'])) {
                $fecha = new DateTime($pedido['Fecha']);
                $fecha->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
                $pedido['Fecha'] = $fecha->format('d-m-Y');
            }

            $stmtItems = $conexion->prepare("SELECT i.IDItem, i.IDRepuesto, r.Nombre, i.Cantidad FROM item i INNER JOIN repuestos r ON i.IDRepuesto = r.IDRepuesto WHERE i.IDPedido = ?");
            $stmtItems->bind_param("i", $pedido['IDPedido']);
            $stmtItems->execute();
            $resItems = $stmtItems->get_result();
            $items = [];
            while ($item = $resItems->fetch_assoc()) {
                $items[] = $item;
            }
            $pedido['items'] = $items;
            $pedidos[] = $pedido;
        }

        echo json_encode($pedidos, JSON_UNESCAPED_UNICODE);
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['items']) || empty($data['items'])) {
        http_response_code(400);
        echo json_encode(['error' => 'El pedido debe contener al menos un item']);
        exit();
    }

    $items = $data['items'];

    $conexion->begin_transaction();

    try {
        $stmtPedido = $conexion->prepare("INSERT INTO pedidos (IDUser, Fecha, Estado) VALUES (?, NOW(), 'Pendiente')");
        $stmtPedido->bind_param("i", $idUser);
        $stmtPedido->execute();
        $idPedido = $stmtPedido->insert_id;

        $stmtItem = $conexion->prepare("INSERT INTO item (IDPedido, IDRepuesto, Cantidad) VALUES (?, ?, ?)");

        foreach ($items as $item) {
            $idRepuesto = intval($item['IDRepuesto']);
            $cantidad = intval($item['cantidad']);

            $stmtStock = $conexion->prepare("SELECT Stock FROM repuestos WHERE IDRepuesto = ?");
            $stmtStock->bind_param("i", $idRepuesto);
            $stmtStock->execute();
            $resultStock = $stmtStock->get_result();
            $repuesto = $resultStock->fetch_assoc();

            if (!$repuesto || $repuesto['Stock'] < $cantidad) {
                throw new Exception('Stock insuficiente para el repuesto ID: ' . $idRepuesto);
            }

            $stmtItem->bind_param("iii", $idPedido, $idRepuesto, $cantidad);
            $stmtItem->execute();
        }

        $conexion->commit();

        echo json_encode(['success' => true, 'message' => 'Pedido creado con exito', 'idPedido' => $idPedido]);
    } catch (Exception $e) {
        $conexion->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Error al crear el pedido', 'details' => $e->getMessage()]);
        exit();
    }
}

if ($method === 'PUT') {
    if ($_SESSION['rol'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $idPedido = intval($data['idPedido']);
    $nuevoEstado = $data['estado'] ?? null;

    if (!in_array($nuevoEstado, ['Pendiente', 'Aprobado', 'Rechazado'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Estado no valido']);
        exit();
    }

    $conexion->begin_transaction();

    try {
if ($nuevoEstado === 'Aprobado') {
    $stmtPedido = $conexion->prepare("SELECT IDUser FROM pedidos WHERE IDPedido = ?");
    $stmtPedido->bind_param("i", $idPedido);
    $stmtPedido->execute();
    $resultPedido = $stmtPedido->get_result();
    $pedidoData = $resultPedido->fetch_assoc();
    $idUserPedido = $pedidoData['IDUser']; 
    
    $stmtItems = $conexion->prepare("SELECT IDRepuesto, Cantidad FROM item WHERE IDPedido = ?");
    $stmtItems->bind_param("i", $idPedido);
    $stmtItems->execute();
    $resultItems = $stmtItems->get_result();

    $stmtUpdateStock = $conexion->prepare("UPDATE repuestos SET Stock = Stock - ? WHERE IDRepuesto = ?");

    while ($item = $resultItems->fetch_assoc()) {
        $idRepuesto = $item['IDRepuesto'];
        $cantidad = $item['Cantidad'];

        $stmtCheckStock = $conexion->prepare("SELECT Stock FROM repuestos WHERE IDRepuesto = ?");
        $stmtCheckStock->bind_param("i", $idRepuesto);
        $stmtCheckStock->execute();
        $resultCheck = $stmtCheckStock->get_result();
        $repuesto = $resultCheck->fetch_assoc();

        if (!$repuesto || $repuesto['Stock'] < $cantidad) {
            throw new Exception('Stock insuficiente para el repuesto ID: ' . $idRepuesto);
        }

        $stmtUpdateStock->bind_param("ii", $cantidad, $idRepuesto);
        $stmtUpdateStock->execute();
    }

    $tipo = 0;
    $stmtMovimiento = $conexion->prepare("INSERT INTO movimientos (IDRepuesto, Tipo, Fecha, IDUser, IDPedido) VALUES (NULL, ?, NOW(), ?, ?)");
    $stmtMovimiento->bind_param("iii", $tipo, $idUserPedido, $idPedido);
    $stmtMovimiento->execute();
}

        $stmt = $conexion->prepare("UPDATE pedidos SET Estado = ? WHERE IDPedido = ?");
        $stmt->bind_param("si", $nuevoEstado, $idPedido);
        $stmt->execute();

        $conexion->commit();
        echo json_encode([
            'success' => true,
            'message' => "Pedido $nuevoEstado exitosamente"
        ]);
    } catch (Exception $e) {
        $conexion->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar el pedido', 'details' => $e->getMessage()]);
        exit();
    }
}

if ($method === 'DELETE') {
    $idPedido = intval($_GET['id']);

    $stmt = $conexion->prepare("SELECT Estado, IDUser FROM pedidos WHERE IDPedido = ?");
    $stmt->bind_param("i", $idPedido);
    $stmt->execute();
    $result = $stmt->get_result();
    $pedido = $result->fetch_assoc();

    if (!$pedido) {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado']);
        exit();
    }

    if ($_SESSION['rol'] !== 'admin' && $pedido['IDUser'] != $idUser) {
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit();
    }

    $conexion->begin_transaction();

    try {
        $stmtDeleteItems = $conexion->prepare("DELETE FROM item WHERE IDPedido = ?");
        $stmtDeleteItems->bind_param("i", $idPedido);
        $stmtDeleteItems->execute();

        $stmtDeletePedido = $conexion->prepare("DELETE FROM pedidos WHERE IDPedido = ?");
        $stmtDeletePedido->bind_param("i", $idPedido);
        $stmtDeletePedido->execute();

        $conexion->commit();

        echo json_encode(['success' => true, 'message' => 'Pedido eliminado exitosamente']);
    } catch (Exception $e) {
        $conexion->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Error al eliminar el pedido', 'details' => $e->getMessage()]);
        exit();
    }
}
$conexion->close();
