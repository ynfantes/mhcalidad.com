<?php
session_start();
include_once '../../includes/constants.php';

$db         = new db();
$criterio   = ["cedula" => $_SESSION["cedula"]];
$re         = $db->select("id_inmueble", "propiedades", $criterio);
$session    = $_SESSION;
$documentos = Array();

if (
    $_GET['doc']  == 'BALANCE_GENERAL' 
    || $_GET['doc'] == 'COMUNICADO'
    || $_GET['doc'] == 'FLUJO_DE_CAJA'
    ) {

    $files = scandir('../documentos/');
    
    if ($re["suceed"] === true && count($re['data'])>0) {
        
        foreach ($re['data'] as $value) {

            foreach ($files as $file) {
                
                if (!strstr($file, $_GET['doc']."_".$value['id_inmueble']) == "") {
                    
                    $url = ROOT . 'enlinea/documentos/'.$file;
                    $filename = '../documentos/'.$file;
                    $documentos[] = Array(
                        'inmueble'  => $value['id_inmueble'], 
                        'link'      => $url, 
                        'documento' => $file, 
                        'existe'    => file_exists($filename)
                        );

                }
                
            }

        }

    }    

} else {

    
    if ($re["suceed"] == true) {
    
        switch ($_GET['doc']) {
    
            case 'RIF':
                $documento = ".pdf";
                $content = 'Content-type: image/pdf';
                $titulo = 'RIF.pdf';
                break;
            case 'INGRESOS':
                $documento = ".pdf";
                $content = 'Content-type: image/pdf';
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
        $doc        = $documento;
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
                    'inmueble'  => $id_inmueble, 
                    'link'      => $url, 
                    'documento' => $documento, 
                    'existe'    => file_exists($filename)
                    );
            }
        }
    }

}
$params = ["session" => $session,"documentos" => $documentos];
echo $twig->render('condominio/reportes.html.twig', $params);