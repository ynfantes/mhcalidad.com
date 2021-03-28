<?php
include './includes/constants.php';
include './includes/mailto.php';
$mail = new mailto(SMTP);
$r = $mail->enviar_email("Prueba", "Este es un mensaje de prueba", '', "ynfantes@gmail.com", "Edgar Messia");
                    
if ($r=='') {
    $resultado.= $n.".- Mensaje enviado a Ok!\n";
} else {
    $resultado.= $n.".- Mensaje enviado a Falló\n";
}
echo $resultado;