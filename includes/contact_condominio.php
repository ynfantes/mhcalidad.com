<?php
include_once '../includes/constants.php';
include '../includes/mailto.php';
$error = '';

if (trim(filter_input(INPUT_POST, 'nombre')) == '') {
    $error .= '<li>Su nombre es requerido.</li>';
}
if (filter_input(INPUT_POST, 'tipo') == '0') {
    $error .= '<li>Seleccione un tipo de contacto.</li>';
}

if (trim(filter_input(INPUT_POST, 'comments')) == '') {
    $error .= '<li>Ingrese un mensaje.</li>';
}

if ($error != '') {
    echo '<div class="alert alert-error">Atención! corrija los errores e inténtelo nuevamente.';
    echo '<ul class="error_messages">' . $error . '</ul>';
    echo '</div>';
} else {

    $mensaje  = "Tipo de Mensaje: <strong>".filter_input(INPUT_POST, 'tipo')."</strong><br />";
    $mensaje .= "Remitente: ".filter_input(INPUT_POST, 'nombre').'<br>';
    $mensaje .= "Email: ".filter_input(INPUT_POST, 'email').'<br>';
    $mensaje .= "Teléfono: ".filter_input(INPUT_POST, 'telefono').'<br>';
    $mensaje .= "Condominio / Apto: ".filter_input(INPUT_POST, 'inmueble').' / '.filter_input(INPUT_POST, 'apto').'<br><br>';
    $mensaje .= str_replace("\n", "<br>",(filter_input(INPUT_POST, 'comments')));
    
    $headers  = "Content-Language:es-ve\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";

    $subject  = "Contacto Condominio MHCalidadAdministrativa.com";
    $headers .= 'From: MHCalidad <mhcalidadadmi@yahoo.es>' . "\r\n" . 'Reply-To:' . filter_input(INPUT_POST, 'email') . "\r\n";
    $headers .= "bcc:zulaymonroy@hotmail.com\n";
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