<?php 


session_start();

function randomGen($length){
    $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
    $key = "";

    for($i=0; $i<$length; $i++){
        $key .= $pattern[rand(0,35)];
    }
    return $key;
}

$_SESSION['captcha'] = randomGen(5);

$captcha = imagecreatefromgif("../public/bgcaptcha.gif");
$colText = imagecolorallocate($captcha, 255, 0, 0);

imageString($captcha, 5, 16, 7, $_SESSION['captcha'], $colText);


header("Content-type: image/gif");
imagegif($captcha);

?>