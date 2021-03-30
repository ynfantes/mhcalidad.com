<?php
session_start();

include_once('../includes/db.php');
$db = new db();
$noticias = array();

if (isset($_SESSION['cpanel'])) {
    
    if ($_SESSION['cpanel']==0) {  
        // <editor-fold defaultstate="collapsed" desc="USUARIO GENERAL-mostramos la cartelera particular del edificio">
        $re = $db->select("id_inmueble", "propiedades", Array("cedula" => $_SESSION["cedula"]));
        if ($re["suceed"] == true) {

            if (count($re['data']) == 0) {
                die("No se puedo obtener información del condominio del propietario");
            } else {
                $condominio = $re['data'][0]['id_inmueble'];
                $re = $db->select("*", "cartelera", Array("id_inmueble" => $condominio));
                if (count($re['data']) > 0) {
                    foreach ($re['data'] as $item) {
                        $noticias[]= "<strong>[".date_format(date_create($item["fecha"]), 'd/m/Y')."]</strong> ".$item["detalle"]."........";
                    }
                    $result = json_encode($noticias);
                    echo $result;
                    
                } else {
                    bienvenida();
                }
            }
        } else {
            die("Error: ".$re["error"]); 
        }
        // </editor-fold>

    } else {
        
        // <editor-fold defaultstate="collapsed" desc="ADMINISTRADOR-mostramos toda la cartelera">
        $re = $db->select("*", "cartelera");
        if ($re["suceed"] == true) {
            if (count($re['data']) > 0) {
                foreach ($re['data'] as $item) {
                    $noticias[]= "<strong>[".date_format(date_create($item["fecha"]), 'd/m/Y')."] INMUEBLE ".$item["id_inmueble"].":</strong> ".utf8_decode($item["detalle"]);
                }
                $result = json_encode($noticias);
                echo $result;

            } else {
                bienvenida();
            }
        } else {
            echo $re["error"];
        }
        // </editor-fold>
    }

} else {
    bienvenida();
}

function bienvenida() {
    $noticias[] = "<strong>".date("d-m-Y")."</strong> Bienvenido, gracias por la confianza en nuestros servicios. 
    En MH Calidad Adminsitrativa estamos enfocados en ofrecer un servicio de calidad, 
    el servicio que usted se merece. 🤝";
    $result = json_encode($noticias);
    echo $result;
}