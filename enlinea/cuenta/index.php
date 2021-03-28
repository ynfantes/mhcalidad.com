<?php
include_once '../../includes/constants.php';
include_once '../../includes/propietario.php';

propietario::esPropietarioLogueado();
        
$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";
$session = $_SESSION;

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="listar">
    case "listar":
    case "usd":
    default :
        $propiedad = new propiedades();
        $facturas = new factura();
        $inmuebles = new inmueble();
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        $cuenta = Array();
        if ($propiedades['suceed'] == true) {
//            if (count($propiedades['data'])>0) {
//                $condominio = $propiedades['data'][0]['id_inmueble'];
//                $apto = $propiedades['data'][0]['apto'];
//            
//                $deuda = propietario::totalDeudaPropietario($condominio,$apto);
//                $pagado = propietario::totalPagadoPropietario($condominio,$apto);
//                $porc_deuda = $deuda * 100 / ($deuda + $pagado);
//                $porc_pagado = $pagado * 100 / ($deuda + $pagado);
//            }
            foreach ($propiedades['data'] as $propiedad) {
                $inmueble = $inmuebles->ver($propiedad['id_inmueble']);
                $factura = $facturas->estadoDeCuenta($propiedad['id_inmueble'], $propiedad['apto']);
                if ($factura['suceed'] == true) {
                    for ($index = 0; $index < count($factura['data']); $index++) {
                        if ($factura['data'][$index]['numero_factura']!='') {
                            $filename = "../avisos/" . $factura['data'][$index]['numero_factura'] . ".pdf";
                            $factura['data'][$index]['aviso'] = file_exists($filename);    
                        }
                    }
                    $cuenta[] = Array(
                        "inmueble"      => $inmueble['data'][0],
                        "propiedades"   => $propiedad,
                        "cuentas"       => $factura['data']);
                }
            }
        }
        if($accion=='usd') {
            echo $twig->render('condominio/cuenta-usd.html.twig', array(
                "session"   => $session,
                "cuentas"   => $cuenta
                    ));
        } else {
            echo $twig->render('condominio/cuenta.html.twig', array(
                "session"   => $session,
                "cuentas"   => $cuenta
                    ));
        }
        break; // </editor-fold>       
    
    // <editor-fold defaultstate="collapsed" desc="estado de cuenta USD">
    case "usd":
        $propiedad = new propiedades();
        $facturas = new factura();
        $inmuebles = new inmueble();
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        $cuenta = Array();
        if ($propiedades['suceed'] == true) {
            foreach ($propiedades['data'] as $propiedad) {
                $inmueble = $inmuebles->ver($propiedad['id_inmueble']);
                $factura = $facturas->estadoDeCuenta($propiedad['id_inmueble'], $propiedad['apto']);
                if ($factura['suceed'] == true) {
                    for ($index = 0; $index < count($factura['data']); $index++) {
                        if ($factura['data'][$index]['numero_factura']!='') {
                            $filename = "../avisos/" . $factura['data'][$index]['numero_factura'] . ".pdf";
                            $factura['data'][$index]['aviso'] = file_exists($filename);    
                        }
                    }
                    $cuenta[] = Array("inmueble" => $inmueble['data'][0],
                        "propiedades" => $propiedad,
                        "cuentas" => $factura['data']);
                }
            }
        }
        echo $twig->render('condominio/cuenta.html.twig', array("session" => $session,
            "cuentas"   => $cuenta
        ));
        break; // </editor-fold> 
        
    // <editor-fold defaultstate="collapsed" desc="avisos">
    case "avisos":
        $propiedad = new propiedades();
        $inmuebles = new inmueble();
        $avisos = new factura();
        $propiedades = $propiedad->propiedadesPropietario($session['usuario']['cedula']);
        if ($propiedades['suceed'] == true) {
            foreach ($propiedades['data'] as $propiedad) {
                $inmueble = $inmuebles->ver($propiedad['id_inmueble']);
                $inmueble = $inmueble['data'][0];
            }
        }
        $ano_actual = date('Y');
        $ano_anterior = date('Y', strtotime('-1 year'));
        $recibos_ant = Array();
        $recibos_act = Array();
        $result = $avisos->historicoAvisosDeCobro($inmueble['id'], $propiedad['apto'], $ano_anterior);
        if ($result['suceed'] && count($result['data']) > 0) {
            $recibos_ant = $result['data'];
        }
        $result = $avisos->historicoAvisosDeCobro($inmueble['id'], $propiedad['apto'], $ano_actual);
        if ($result['suceed'] && count($result['data']) > 0) {
            $recibos_act = $result['data'];
        }


//        $archivos = glob("../avisos/*".substr($ano_anterior,-2).substr($inmueble['id'],-3).sprintf('%03d', $session['usuario']['id']).".pdf");
//        foreach($archivos as $archivo) {
//            $recibos_ant[] = basename($archivo);
//        }
//        $archivos = glob("../avisos/*".substr($ano_actual,-2).substr($inmueble['id'],-3).sprintf('%03d', $session['usuario']['id']).".pdf");
//        foreach($archivos as $archivo) {
//            $recibos_act[] = basename($archivo);
//        }
        echo $twig->render('condominio/avisos-de-cobro.html.twig',                 Array(
            "ano_anterior" => $ano_anterior,
            "ano_actual" => $ano_actual,
            "recibos_ant" => $recibos_ant,
            "recibos_act" => $recibos_act
        ));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="cobranza_flot">
    case "graficaflot":
        $propiedad = new propiedades();
        $deuda = 0;
        $pagado = 0;
        //$_SESSION['usuario']['cedula']
        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        
        if ($propiedades['suceed'] && count($propiedades['data'])>0) {
            $condominio = $propiedades['data'][0]['id_inmueble'];
            $apto = $propiedades['data'][0]['apto'];
            
            $deuda = propietario::totalDeudaPropietario($condominio,$apto);
            $pagado = propietario::totalPagadoPropietario($condominio,$apto);
            $deuda = $deuda * 100 / ($deuda + $pagado);
            $pagado = $pagado * 100 / ($deuda + $pagado);
            $rows = array();
            $table = array();
            //$table['label'] = $_GET['id'];
            //$rows[] = array('Deuda', $deuda);
            //$rows[] = array('Pagado', $pagado);
            
            $table['deuda'] = $deuda;
            $table['pagado'] = $pagado;
            
            $jsonTable = json_encode($table);

            echo $jsonTable;
            
        }
        break; 
    // </editor-fold>
}