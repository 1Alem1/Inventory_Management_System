<?php 

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

require 'sanitizar.php';
require 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $captcha = $_POST['captcha'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password) || empty($captcha)) {
        echo "Todos los campos son obligatorios";
        exit();
    }


    $email = sanitizarEntrada($email);

    if(!verificarCaptcha($captcha)) {
        echo "Captcha incorrecto";
        exit();
    }




    $stmt = $conexion->prepare("SELECT Password, Rol FROM usuarios WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($hashedPassword, $rol);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {

            $_SESSION['email'] = $email;
            $_SESSION['rol'] = $rol;

            echo "loguser|" . $rol;
            exit();
        }

        echo "Contraseña incorrecta";
        exit();
    }

    echo "Usuario no encontrado";
    exit();
}

$conexion->close();
