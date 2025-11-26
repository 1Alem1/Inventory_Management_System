<?php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

require 'conexion.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT 
        m.IDMovimiento,
        m.Tipo,
        m.Fecha,
        u.Nombre AS Nombre,
        m.IDPedido,
        m.IDRepuesto,
        CASE 
            WHEN m.IDPedido IS NOT NULL THEN (
                SELECT SUM(i.Cantidad * r.Precio) 
                FROM item i 
                INNER JOIN repuestos r ON i.IDRepuesto = r.IDRepuesto 
                WHERE i.IDPedido = m.IDPedido
            )
            WHEN m.Tipo = 0 THEN (
                SELECT Stock * Precio 
                FROM repuestos 
                WHERE IDRepuesto = m.IDRepuesto
            )
            ELSE NULL
        END AS Monto
    FROM movimientos m
    INNER JOIN usuarios u ON m.IDUser = u.IDUser
    ORDER BY m.Fecha DESC";

    $resultado = $conexion->query($sql);

    $movimientos = [];

    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $movimientos[] = $fila;
        }
    }
    
    echo json_encode($movimientos, JSON_UNESCAPED_UNICODE);
    $conexion->close();
}