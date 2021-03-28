<?php
include_once '../../includes/constants.php';
include_once '../../includes/propietario.php';

propietario::esPropietarioLogueado();

$factura = new factura();

$r = $factura->facturaPerteneceACliente($_GET['id'], $_SESSION['usuario']['cedula']);

if ($r==true) {
    $titulo = "Aviso_Cobro_".$_GET['id'].".pdf";
    $content="Content-type: application/pdf";
    $url = URL_SISTEMA."/avisos/".$_GET['id'].".pdf";
    header('Content-Disposition: attachment; filename="'.$titulo.'"');
    header($content);
    readfile($url);
    
} else {
    echo "La relación de gastos que intenta ver no le pertenece. "
    . "<strong>Imposible mostrar esta relación de gastos en este momento.</strong>";
}