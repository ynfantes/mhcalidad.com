<?php
include_once '../includes/constants.php';

session_start();

$accion  = isset($_GET['accion']) ? $_GET['accion'] : '';
$reporte = isset($_GET['id']) ? $_GET['id'] : '';

if ($_SESSION["usuario"] === "Administrador") {
    menuAdministrador($accion,$reporte,$twig);

} else {

    menuCliente($accion,$reporte,$twig);

}

function menuAdministrador($accion,$reporte,$twig) {
    
    switch ($accion) {
        
        case 'recibos-pago':
            $reporte = $accion.'-'.$reporte;
        case 'calculo':
        case 'otros-reportes':
            $empresas = new empresa();
            $clientes = $empresas->listarEmpresasActivas();
            $data = array();

            if ($clientes['suceed'] && count($clientes['data'])>0) {
                $data = $clientes['data'];
            }
            echo $twig->render(
                'contabilidad/cargas.html.twig',
                array(
                    'reporte' => $reporte,
                    'clients' => $data,
                    'session' => $_SESSION
                    )
            );
            break;
        
            
        default:
            echo $twig->render(
                'contabilidad/descargas.html.twig',
                array(
                    'session' => $session
                    )
            );
    }
}

function menuCliente($accio,$reporte,$twig) {
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
}
