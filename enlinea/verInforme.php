<?php
session_start();

include_once('../includes/db.php');
$db = new db();
$re = $db->select("id_inmueble","propiedades",Array("cedula"=>$_SESSION["cedula"]));
$n=0;
if ($re["suceed"] == true ) {
    
    if(count($re['data'])==0){
        echo "Informe No Publicado";
    } else {
        foreach ($re['data'] as $data) {
            $aviso = $_GET['id'].$data['id_inmueble'];
            $documento = 'documentos/'.$aviso.'.pdf';
            
            if (file_exists($documento)) {
                $url = URL_SISTEMA."/".$documento;
                header('Content-type: application/pdf');
                header('Content-Disposition: attachment; filename="'.$aviso.'.pdf"');
                readfile($url);
                $n++;
            }
        }
        if ($n==0) {
            echo "No est&aacute; disponible este informe.<br>Contacte al administrador.<br>";
            echo "<a href='javascript:window.history.back();'>Regresar</a>";
            die();
        }
    }
} else {
    echo $re["error"];
}