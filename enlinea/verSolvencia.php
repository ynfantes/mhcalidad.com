<?php
include_once('../includes/db.php');
$db = new db();
session_start();
$re = $db->select("id_inmueble, apto","propiedades",Array("cedula"=>$_SESSION["cedula"]));

if ($re["suceed"] == true ) {
    if(count($re['data'])==0){
        echo "no se puede mostrar la solvencia";
    } else {
        $de = $db->select("*","facturas",Array("apto"=>$re['data'][0]['apto'],"id_inmueble"=>$re['data'][0]['id_inmueble']));
        
        if ($de["suceed"]==true) {
            
            if(count($de['data'])<1) {
                // seteamos la variables del texto de la solvencia
                $cedula = number_format($_SESSION['cedula'],'0','','.');
                $nombre = $_SESSION['nombre'];
                $numero_apartamento = $re['data'][0]['apto'];
                
                $junta_condominio = $db->dame_query("
            select propietarios.nombre as 'miembro', propietarios.cedula  
            from junta_condominio
            inner join inmueble on inmueble.id = junta_condominio.id_inmueble
            inner join propietarios on propietarios.cedula = junta_condominio.cedula
            left join cargo_jc on cargo_jc.id = junta_condominio.id_cargo
            where inmueble.id='".$re['data'][0]['id_inmueble']."' and cargo_jc.descripcion='PRESIDENTE'");
            
                $presidente="--";
                $cedula_presidente="";
                
                if ($junta_condominio['suceed'] == true && count($junta_condominio['data'])>0) {
                    $presidente = $junta_condominio['data'][0]['miembro'];
                    $cedula_presidente = number_format($junta_condominio['data'][0]['cedula'],0,",",".");
                }
                if (filter_input(INPUT_GET, 'clase')=='s') {
                    $inmueble=$re['data'][0]['id_inmueble'];
                    $modelo_solvencia = "solvencia-".$inmueble.".html";
                    if (!file_exists($modelo_solvencia)) {
                        //echo $modelo_solvencia."<br>";
                        echo "No est&aacute; disponible este reporte. Contacte al administrador del sistema.<br>";
                        echo "<a href='javascript:window.history.back();'>Regresar</a>";
                        die();
                    }
//                    if ($inmueble=='0001') {
//                        $modelo_solvencia = "solvenciaModB.html";
//                        //include("solvencia1.php");
//                    } elseif($de['data'][0]['id_inmueble']=='0003') {
//                        $modelo_solvencia = "solvenciaModA.html";
//                        //include("solvencia2.php");
//                    } else {
//                        $modelo_solvencia = "solvenciaMod0006.html";
//                    }
                    
                } else {
                    
                    foreach ($re['data'] as $inm) {
                        if ($inm['id_inmueble'] != '0002') {
                            $inmueble = $inm['id_inmueble'];
                            $numero_apartamento = $inm['apto'];
                            break;
                        }
                    }
//                    $inmueble = $re['data'][0]['id_inmueble'];
//                    
//                    if (count($re['data'])> 1) {
//                        if ($re['data'][0]['id_inmueble']=='0002') {
//                            $inmueble = $re['data'][1]['id_inmueble'];
//                        }
//                        
//                    }

                    $hi = $db->select("*", "inmueble_hidrocapital", Array("id_inmueble"=>$inmueble));
                    $monto_pago = 0;
                    $fecha_pago = date("d/m/Y");
                    $banco = "S/E";
                    $numero_cheque = "000000";
                    $periodo = date("m/Y");
                    
                    if ($hi['suceed']==true) {
                        
                        if(count($hi['data'])!=0) {
                            $monto_pago = number_format($hi['data'][0]['monto_pago'],2,",",".");
                            $fecha_pago = date('d-m-Y',strtotime($hi['data'][0]['fecha_pago']));
                            $banco = $hi['data'][0]['banco'];
                            $numero_cheque = $hi['data'][0]['numero_cheque'];
                            $periodo = $hi['data'][0]['periodo'];
                        }
                    }
                    $modelo_solvencia = "solvenciaHidro-".$inmueble.".html";
                    if (!file_exists($modelo_solvencia)) {
                        echo "No est&aacute; disponible este reporte. Contacte al administrador del sistema.<br>";
                        echo "<a href='javascript:window.history.back();'>Regresar</a>";
                        die();
                    }
//                    if ($inmueble =='0001') {
//                        $modelo_solvencia = "solvenciaHidro-0001.html";
//                    } elseif($inmueble =='0003') {
//                        $modelo_solvencia = "solvenciaHidro-0003.html";
//                    } else {
//                        $modelo_solvencia = "solvenciaHidro-0006.html";
//                    }
                }
                include("solvencia1.php");
            } else {
                echo "<strong>PARA EMITIR LA SOLVENCIA EN L&Iacute;NEA, DEBE ESTAR AL D&Iacute;A CON EL CONDOMINIO Y ESTACIONAMIENTO.<br>  LA ADMINISTRACI&Oacute;N </strong>
                    <br>En breves segundos lo llevaremos a la p&aacute;gina principal....";
            }
        } else {
            echo "No se puedo verificar el Estado de Cuenta.";
        }
    }
} else {
    echo "Fallo";
    echo $re["error"];
}

//include("solvencia1.php");
?>
<META HTTP-EQUIV="REFRESH" CONTENT="5;URL=index.php"> 