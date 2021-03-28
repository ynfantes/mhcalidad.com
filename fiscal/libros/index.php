<?php
include_once '../includes/constants.php';
session_start();
$session = $_SESSION;

$accion = isset($_GET['accion']) ? $_GET['accion'] : "";
$reporte_id = isset($_GET['id'])? $_GET['id']:'';

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="libro ventas">
    case 'ventas':

        $publicado = new publicacion();

        $lista = $publicado->listarReportePorLibroyEmpresa($_SESSION['usuario']['Id'], 0);
        $libros = Array();
        if ($lista['suceed']) {
            $libros = $lista['data'];

        }
        echo $twig->render('fiscal/libro.html.twig', array(
            "session" => $session,
            "libros" => $libros,
            "tipo" => "Libro de Ventas"
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="libro compras">
    case 'compras':

        $publicado = new publicacion();

        $lista = $publicado->listarReportePorLibroyEmpresa($_SESSION['usuario']['Id'], 0);
        $libros = Array();
        if ($lista['suceed']) {
            $libros = $lista['data'];

        }
        echo $twig->render('fiscal/libro.html.twig', array(
            "session" => $session,
            "libros" => $libros,
            "tipo" => "Libro de Compras"
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="libro resumen">
    case 'resumen':
        $publicado = new publicacion();
        $lista = $publicado->listarReportePorLibroyEmpresa($_SESSION['usuario']['Id'], 2);
        $libros = Array();
        if ($lista['suceed']) {
            $libros = $lista['data'];

        }
        echo $twig->render('fiscal/libro.html.twig', array(
            "session" => $session,
            "libros" => $libros,
            "tipo" => "Libro Resumen"
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="reportes">
    case 'reporte':
        $publicado = new publicacion();
        $reporte = new reporte();
        $libros = $publicado->listarReportePorLibroyEmpresa($_SESSION['usuario']['Id'], $reporte_id);
        $r = $reporte->ver($reporte_id);
        echo $twig->render('fiscal/libro.html.twig', array(
            "session" => $session,
            "libros" => $libros['data'],
            "tipo" => $r['data'][0]['descripcion']
                )
        );
        break; // </editor-fold>

    default:
        break;
}