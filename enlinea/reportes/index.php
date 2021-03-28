<?php

session_start();
include_once '../../includes/constants.php';

$db = new db();
$re = $db->select("id_inmueble", "propiedades", Array("cedula" => $_SESSION["cedula"]));
$session = $_SESSION;
if ($re["suceed"] == true) {
    switch ($_GET['doc']) {

        case 'RIF':
            $documento = ".jpg";
            $content = 'Content-type: image/jpeg';
            $titulo = 'RIF.jpg';
            break;
        case 'INGRESOS':
            $documento = ".pdf";
            $content = 'Content-type: image/jpeg';
            $titulo = 'INGRESOS_EGRESOS';
            break;

        case 'ACTA_JUNTA':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = "Acta Junta.pdf";
            break;

        case 'EDO_CTA':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Estado de Cuenta Inmueble.pdf';
            break;
        
        case 'EDO_CTA_MOROSOS':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Estado de Cuenta Morosos.pdf';
            break;

        case 'EDO_CTA_COMBINADO':
            $documento = "EDO_CTA_COMBINADO.pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Estado de Cuenta Combinado.pdf';
            break;

        case 'Cobranza_General':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Cobranza por caja.pdf';

            break;
        case 'Reporte_de_Morosos_General':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Morosos General.pdf';
            break;

        case 'Reporte_de_Morosos_Detallado':
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = 'Morosos Detallado.pdf';
            break;

        default :
            $documento = ".pdf";
            $content = 'Content-type: application/pdf';
            $titulo = $_GET['doc'] . ".pdf";
            break;
    }
    
    $documentos = Array();
    $doc = $documento;
    $id_inmueble = '';
    //echo "Haga click sobre una opción para ver el documento:<br><br>";
    foreach ($re['data'] as $value) {
        if ($value['id_inmueble'] != $id_inmueble) {
            $id_inmueble = $value['id_inmueble'];

            if ($_GET['doc'] != 'EDO_CTA_COMBINADO') {
                    $documento = $_GET['doc'] . "_" . $id_inmueble . $doc;
            }

            $url = ROOT . 'enlinea/documentos/' . $documento;
            //echo "<a href=\"".$url."\" target=\"_blank\">".$documento."</a><br>";
            $filename = '../documentos/' . $documento;
            $documentos[] = Array(
                "inmueble"  => $id_inmueble, 
                "link"      => $url, 
                "documento" => $documento, 
                "existe"    => file_exists($filename)
                );
        }
    }

    echo $twig->render('condominio/reportes.html.twig', array(
        "session" => $session,
        "documentos" => $documentos)
    );
    die();
}

