<?php

session_start();

include_once('../includes/db.php');
$db = new db();

$re = $db->select("id_inmueble","propiedades",Array("cedula"=>$_SESSION["cedula"]));

if ($re["suceed"] === true ) {
    
    if(count($re['data'])==0){
        echo "No se encuentra el recurso solicitado";
    } else {
        
        // if (count($re['data'])> 1) {
        //     // redirigirmos la página hacia reportes.php
        //     header("location: ".ROOT."enlinea/reportes/index.php?doc=".$_GET['doc']);
        //     exit();
        // }
        switch (filter_input(INPUT_GET, 'doc')) {
            
            case 'INGRESOS':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='Ingresos vs Egresos';
                break;
            
            case 'RIF':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='RIF';
                break;
            
            case 'ACTA_JUNTA':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo = "Acta Junta.pdf";
                break;
            
            case 'EDO_CTA':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='Estado de Cuenta Inmueble.pdf';
                break;
            
            case 'EDO_CTA_DETALLADO':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='Estado de Cuenta Detallado Inmueble.pdf';
                break;
            
            case 'EDO_CTA_MOROSOS':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='Estado de Cuenta Morosos.pdf';
                break;
            
            case 'EDO_CTA_COMBINADO':
                $documento = "EDO_CTA_COMBINADO.pdf";
                $content='Content-type: application/pdf';
                $titulo='Estado de Cuenta Combinado.pdf';
                break;
            
            case 'Cobranza_General':
                $documento = ".pdf";
                $content='Content-type: application/pdf';
                $titulo='Cobranza por caja.pdf';
                
                break;
            case 'Reporte_de_Morosos_General':
                $documento  = ".pdf";
                $content    = 'Content-type: application/pdf';
                $titulo     = 'Morosos General.pdf';
                break;
            
            case 'Reporte_de_Morosos_Detallado':
                $documento  = ".pdf";
                $content    = 'Content-type: application/pdf';
                $titulo     = 'Morosos Detallado.pdf';
                break;
                
            case 'FACTURAS_POR_CARGAR':
                $documento  = ".pdf";
                $content    = 'Content-type: application/pdf';
                $titulo     = 'Tesorería - Flujo de Caja';
                break;

            case 'CUENTAS_POR_COBRAR_CONSTRUCTORA':
                $documento  = ".pdf";
                $content    = 'Content-type: application/pdf';
                $titulo     = 'Estado de cuenta accionistas';
                break;
            
            default :
                $documento  = ".pdf";
                $content    = 'Content-type: application/pdf';
                $titulo     = $_GET['doc'].".pdf";
                break;
        }
        
        if ($_GET['doc'] !== 'EDO_CTA_COMBINADO') {
            $documento = $_GET['doc']."_".$re['data'][0]['id_inmueble'].$documento;
        }
        if ($_GET['doc']=='Cobranza_General') {
            $documento = str_replace('0003', '0002', $documento);
        }
        if (file_exists("documentos/$documento")) {
            
            $url = URL_SISTEMA.'/documentos/'.$documento;
            echo '<script>';
            echo 'var myWindow = window.open("'.$url.'", "'.$titulo.'", "width=1000,height=800");';
            echo 'window.location.href="'.$_SERVER['HTTP_REFERER'].'"';
            echo '</script>';
            
        } else {
            
            echo '<script>';
            echo 'alert("🤦‍♂️ Ups! Lo sentimos mucho, no hemos publicado el documento que está consultando.\nContacte con su Administrador");';
            echo 'window.history.back();';
            echo '</script>';

        }

    }
} else {
    echo $re["error"];
}