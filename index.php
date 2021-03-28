<?php

include_once 'includes/constants.php';

$accion = isset($_GET['accion']) ? $_GET['accion']:'';

if ($accion!="") {
    $mantenimiento = false;
    $avance = 0;
    if ($accion=='administracion-condominio') {
        $result = array();
        $propietarios = new propietario();
        $propiedades = new propiedades();
        $facturas = new factura();
        $result = $propietarios->listar();

        if (!$result['suceed'] || empty($result['data'])) {
            $mantenimiento=TRUE;
        } else {
            $result = $propiedades->listar();
            if (!$result['suceed'] || empty($result['data'])) {
                $mantenimiento=TRUE;

            } else {
                $result = $facturas->listar();
                if (!$result['suceed'] || empty($result['data'])) {
                    $mantenimiento=TRUE;
                }
            }    
        }
        if ($mantenimiento || MOROSO) {
            $mantenimiento = true;
            $mail = new mailto(SMTP);
            $min = date("i");
            $avance = $min * 100 / 60;
            if ($_SERVER['SERVER_NAME'] == "www.mhcalidadadministrativa.com" | $_SERVER['SERVER_NAME'] == "mhcalidadadministrativa.com") {
                $mail->enviar_email(TITULO.' [Mantenimiento]','Sincronice la página web','','ynfantes@gmail.com','Valoriza2');
            }

        }
    }
    echo $twig->render($accion.'.html.twig',Array(
                "mantenimiento" => $mantenimiento,
                "avance"        => $avance
                ));
} else {
    echo $twig->render('index.html.twig');
}