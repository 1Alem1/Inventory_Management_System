<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

require 'conexion.php';


$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT IDRepuesto, Nombre, Categoria, Descripcion, Stock, Precio, Imagen from repuestos where IDRepuesto = $id";
        $resultado = $conexion->query($sql);

        echo json_encode($resultado->fetch_assoc(), JSON_UNESCAPED_UNICODE);
        $conexion->close();
    } else {
        $sql = "SELECT IDRepuesto, Nombre, Categoria, Descripcion, Stock, Precio, Imagen FROM repuestos";

        $resultado = $conexion->query($sql);

        $repuestos = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $repuestos[] = $fila;
            }
        }
        echo json_encode($repuestos, JSON_UNESCAPED_UNICODE);
        $conexion->close();
    }
}
