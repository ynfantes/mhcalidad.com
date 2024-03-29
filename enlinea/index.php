<?php
include_once '../includes/constants.php';
include_once '../includes/file.php';

$archivo = ACTUALIZ.ARCHIVO_ACTUALIZACION;
$fecha_actualizacion = JFile::read($archivo);
$propietario = new propietario();
$propietario->esPropietarioLogueado();
$propiedad = new propiedades();
$inmuebles = new inmueble();

$session            = $_SESSION;
$porc_deuda         = 0;
$porc_pagado        = 0;
$nombre_condominio  = '';

if (isset($session['usuario']['cedula'])) {
    
    $propiedades = $propiedad->propiedadesPropietario($session['usuario']['cedula']);

    if ($propiedades['suceed'] && count($propiedades['data'])>0) {

        $resultado  = $inmuebles->ver($propiedades['data'][0]['id_inmueble']);
        
        if ($resultado['suceed'] && count($resultado['data'])>0) {
            $nombre_condominio = $resultado['data'][0]['nombre_inmueble'];
        }
        
        $condominio  = $propiedades['data'][0]['id_inmueble'];
        $apto        = $propiedades['data'][0]['apto'];
        $deuda       = $propietario->totalDeudaPropietario($condominio,$apto);
        $pagado      = $propietario->totalPagadoPropietario($condominio,$apto);
        $total       = $deuda + $pagado;
        $porc_deuda  = $deuda * 100 / $total;
        $porc_pagado = $pagado * 100 / $total;
        
    }
}



echo $twig->render(
    'condominio/index.html.twig',
    array(
        "session"               => $session,
        "fecha_actualizacion"   => $fecha_actualizacion,
        "deuda"                 => $porc_deuda,
        "pagado"                => $porc_pagado,
        "inmueble"              => $nombre_condominio
    )
);