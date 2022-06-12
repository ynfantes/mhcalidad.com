<?php
include_once '../includes/constants.php';
session_start();

$accion     = isset($_GET['accion']) ? $_GET['accion'] : "";
$reporte_id = isset($_GET['id']) ? $_GET['id']:'';

if ($_SESSION["usuario"] === "Administrador") {

    menuAdministrador($accion,$reporte_id,$twig);

} else {

    menuCliente($accion,$reporte_id,$twig);

} 

function menuAdministrador($accion,$reporte_id,$twig) {
    $data = array();
    $reporte = array();

    $empresas = new empresa();
    $reportes = new reporte();

    $clientes = $empresas->listarEmpresasActivas();
    $data = array();
    if ($clientes['suceed'] && count($clientes['data'])>0) {
        $data = $clientes['data'];
    }

    $result = $reportes->obtenerReportePorMenu($reporte_id);
    
    if ($result['suceed'] && count($result['data'])>0) {
        $reporte = $result['data'][0];
    }
    
    echo $twig->render(
        'fiscal/cargas.html.twig',
        array(
            'reporte'   => $reporte,
            'clients'   => $data,
            'session'   => $_SESSION
            )
    );
}

function menuCliente($accion,$reporte_id,$twig) {
    
    $publicado = new publicacion();
    $reportes = new reporte();
    
    $result = $reportes->obtenerReportePorMenu($reporte_id);
    
    if ($result['suceed'] && count($result['data'])>0) {
        $reporte = $result['data'][0];
    }
    
    $libros = $publicado->listarReportePorLibroyEmpresa($_SESSION['usuario']['Id'], $reporte['id']);
    
    echo $twig->render('fiscal/libro.html.twig', array(
        "session"   => $_SESSION,
        "libros"    => $libros['data'],
        "reporte"   => $reporte
            )
    );
        
}
