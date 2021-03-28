<?php
include_once '../includes/constants.php';
include '../includes/mailto.php';
$error = '';

if (trim(filter_input(INPUT_POST, 'nombre')) == '') {
    $error .= '<li>Su nombre es requerido.</li>';
}
if (trim(filter_input(INPUT_POST, 'email')) == '') {
    $error .= '<li>Su dirección e-mail es requerida.</li>';
}

if (filter_input(INPUT_POST, 'area') == '0') {
    $error .= '<li>Seleccione un área de atención.</li>';
}

if (trim(filter_input(INPUT_POST, 'comments')) == '') {
    $error .= '<li>Ingrese un mensaje.</li>';
}

if ($error != '') {
    echo '<div class="alert alert-error">Atención! corrija los errores e inténtelo nuevamente.';
    echo '<ul class="error_messages">' . $error . '</ul>';
    echo '</div>';
} else {

    $mensaje  = "Area: ".filter_input(INPUT_POST, 'area')."<br />";
    $mensaje .= "Remitente:".filter_input(INPUT_POST, 'nombre').'<br><br>';
    $mensaje .= str_replace("\n", "<br>",(filter_input(INPUT_POST, 'comments')));
    
    $headers  = "Content-Language:es-ve\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    $subject  = "Contacto MHCalidadAdministrativa.com";
    $headers .= 'From: MHCalidad <mhcalidadadmi@yahoo.es>' . "\r\n" . 'Reply-To:' . filter_input(INPUT_POST, 'email') . "\r\n";
    $headers .= "bcc:ynfantes@gmail.com\n";
    $email    = "mhcalidadadmi@yahoo.es";
    
    $mail = new mailto(SMTP);
    $r = $mail->enviar_email($subject, $mensaje, "", $email);
    
//    if (mail($email, $subject, $mensaje, $headers)) {
    if ($r=="") {
        echo '<div class="alert alert-success"><i class="icon-ok"></i><strong>Menseje enviado con éxito!</strong>'
        . '<br>En breve estaremos contactando con usted. Gracias por su interés.</div>';
    } else {
        echo '<div class="alert alert-error"><i class="icon-warning-sign"></i><strong>Error al enviar el mensaje.</strong>
                  <br>No se pudo enviar el mensaje. Por favor intente nuevamente.</div>';
    }
    
}