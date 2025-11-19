<?php 

function sanitizarEntrada($data)
{
    $data = trim(strip_tags($data));
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function verificarCaptcha($captcha)
{
    if((!isset($_SESSION['captcha']) || empty($captcha)))
    {
        return false;
    }

    $captcha_user = strtolower(trim($captcha));
    $captcha_session = strtolower(trim($_SESSION['captcha']));

    return $captcha_user === $captcha_session;
}


?>