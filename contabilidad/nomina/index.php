<?php
include_once '../includes/constants.php';

session_start();
$session = $_SESSION;

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';
$reporte_id = isset($_GET['id']) ? $_GET['id'] : '';

switch ($accion) {
    case 'calculo':
    case 'recibos-pago':
    case 'otros-reportes';
        echo $twig->render(
            'contabilidad/descargas.html.twig',
            array(
                'session' => $session
                )
        );
        break;
    
    default:
        # code...
        break;
}