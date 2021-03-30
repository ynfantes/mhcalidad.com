<?php
include_once '../includes/constants.php';

session_start();

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

switch ($accion) {
    
    case 'ingresos-egresos':
    case 'comprobacion':
    case 'general':
    case 'resultado':
        $reporte = 'balance-'.$accion;
        $empresas = new empresa();
        $clientes = $empresas->listarEmpresasActivas();
        $data = array();

        echo $twig->render(
            'contabilidad/cargas.html.twig',
            array(
                'reporte' => $reporte,
                'clients' => $data,
                'session' => $_SESSION
                )
        );
        break;
    
    // default:
    //     echo $twig->render(
    //         'contabilidad/descargas.html.twig',
    //         array(
    //             'session' => $_SESSION
    //             )
    //     );
    //     break;
}