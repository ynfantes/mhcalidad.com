<?php
include_once '../includes/db.php';
include_once('../includes/file.php');

$db = new db(); 

$tablas = [
    "factura_detalle", 
    "facturas", 
    "propiedades", 
    "propietarios", 
    "junta_condominio", 
    "inmueble",
    'historico_avisos_cobro',
    'inmueble_deuda_confidencial'
];

if (isset($_GET['codinm'])) {
    $codinm = $_GET['codinm'];
    $db->exec_query("delete from factura_detalle where id_factura in (select numero_factura from facturas wher id_inmueble='".$codinm."')");
    $db->exec_query("delete from facturas where id_inmueble='".$codinm."'");
    $db->exec_query("delete from propietarios where cedula in (select cedula from propiedades where id_inmueble='".$codinm."')");
    $db->exec_query("delete from junta_condominio where id_inmueble='".$codinm."'");
    $db->exec_query("delete from propiedades where id_inmueble='".$codinm."'");
    $db->exec_query("delete from inmueble where id='".$codinm."'");
    $db->exec_query('delete from historico_avisos_cobro where id_inmueble="'.$codinm.'"');
    $db->exec_query("delete from inmueble_deuda_confidencial where id_inmueble='".$codinm."'");
    /*$db->exec_query("delete from movimiento_caja where id_inmueble='".$codinm."'");*/
    if (!MOROSO) {
        $mensaje = "Actualización inmueble ".$codinm."<br>";
    }
} else {
    if (!MOROSO) {
        $mensaje = "Proceso de Actualización Ejecutado<br />";
    }
    foreach ($tablas as $tabla) {
        $r = $db->exec_query("truncate table " . $tabla);
        if (!MOROSO) {
            echo "limpiar tabla: " .$tabla."<br />";
        }
    }
}
if (MOROSO) {
    $mensaje = 'Estimado cliente, a la presenta fecha presenta '.RECIBOS_PENDIENTES.
            ' recibo(s) pendiente(s) de pago, por un monto de Bs.'.DEUDA.
            '<br>Si ya efectuó el pago correspondiente por favor enviar los datos'
            . 'del mismo, vía Whatsapp al 0424-9569266<br> o al correo electronico '
            . 'info@administracion-condominio.com.ve para actualizar nuestros registros.<br><br>';
    echo $mensaje;
} else {

    echo "procesar archivo inmueble<br />";
    $archivo = ACTUALIZ.ARCHIVO_INMUEBLE;
    if(file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        echo "actualizando archivo inmuebles (" . count($lineas) . ")<br />";
        $mensaje .= "procesar archivo inmuebles (" . count($lineas) . ")<br />";
        foreach ($lineas as $linea) {
            
            $registro = explode("\t", $linea);
            
            if ($registro[0] != "") {
                $r = $db->insert("inmueble", Array(
                    "id"                => $registro[0],
                    "nombre_inmueble"   => $registro[1],
                    "deuda"             => $registro[2],
                    "fondo_reserva"     => $registro[3],
                    "beneficiario"      => $registro[4],
                    "banco"             => '',
                    "numero_cuenta"     => '',
                    "supervision"       => '0',
                    "RIF"               => $registro[5]));
                
                if($r["suceed"]==FALSE){
                    //echo ARCHIVO_INMUEBLE."<br />".$r['stats']['errno']."<br />".$r['stats']['error'];
                    echo $r['query'];
                    die();
                }
                
            }
        }
    }

    echo "procesar archivo cuentas <br/>";
    $archivo = ACTUALIZ.ARCHIVO_CUENTAS;
    if(file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        $inmueble = new inmueble();
        echo "actualizando cuentas inmuebles (" . count($lineas) . ")<br />";
        $mensaje.= "actualizando cuentas inmuebles (" . count($lineas) . ")<br />";
        foreach ($lineas as $linea) {
            $id = '';
            $registro = explode("\t", $linea);
        
                if ($registro[0] != "") {
                    $id=$registro[0];
                    $registro = Array(
                        "numero_cuenta" => $registro[1],
                        "banco" => $registro[2]);
        
                    $r = $inmueble->actualizar("'".$id."'",$registro);
                    if ($r["suceed"] == FALSE) {
                        //echo ARCHIVO_INMUEBLE."<br />".$r['stats']['errno']."<br />".$r['stats']['error'];
                        echo $r['query'];
                        die();
                    }
                }
        }
    }

    echo "procesar archivo Cuentas Inmueble<br />";
    $archivo = "./data/CUENTAS_INMUEBLE.txt";
    if (file_exists($archivo)) {
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        echo "Procesar archivo Cuentas Inmuebles (" . count($lineas) . ")<br />";
        $mensaje.="Procesar archivo Cuentas Inmuebles (" . count($lineas) . ")<br />";
        foreach ($lineas as $linea) {
            
            $registro = explode("\t", $linea);

            if ($registro[0] != "") {
                $registro = Array(
                    "id_inmueble" => $registro[0],
                    "numero_cuenta" => $registro[1],
                    "banco" => $registro[2]);


                $r = $inmueble->agregarCuentaInmueble($registro);
                
                if ($r["suceed"] == FALSE) {
                    //echo ARCHIVO_INMUEBLE."<br />".$r['stats']['errno']."<br />".$r['stats']['error'];
                    die($r['query']);
                }

            }
        }
        
    } 

    echo "procesar archivo Junta Condominio<br />";
    $archivo = ACTUALIZ.ARCHIVO_JUNTA_CONDOMINIO;
    if(file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        
        foreach ($lineas as $linea) {
        
            $registro = explode("\t", $linea);
            
            if ($registro[0] != "") {
                
                $datos = [ "id_cargo" => $registro[1], "id_inmueble" => $registro[0], "cedula" => $registro[2] ];
                $r = $db->insert("junta_condominio", $datos);
                if($r["suceed"]==FALSE){
                    echo ARCHIVO_JUNTA_CONDOMINIO."<br />".$r['stats']['errno']."<br />".$r['stats']['error'];
                }
            }
        }
    }

    $archivo = ACTUALIZ.ARCHIVO_PROPIETARIOS;
    echo "procesar archivo Propietarios<br />";
    if(file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        echo "procesar archivo Propietarios(" . count($lineas) . ")<br />";
        $mensaje.="procesar archivo Propietarios(" . count($lineas) . ")<br />";
        foreach ($lineas as $linea) {
            $registro = explode("\t", $linea);
            if ($registro[0] != "") {
                $r = $db->insert("propietarios", Array(
                    'nombre'            => utf8_encode($registro[0]),
                    'clave'             => $registro[1],
                    'email'             => $registro[2],
                    'cedula'            => $registro[3],
                    'telefono1'         => $registro[4],
                    'telefono2'         => $registro[5],
                    'telefono3'         => $registro[6],
                    'direccion'         => utf8_encode($registro[7]),
                    'recibos'           => $registro[8],
                    'email_alternativo' => $registro[9]
                        ));
                if($r["suceed"]==FALSE){
                    echo "<b>Archivo Propietario: ".$archivo.' - '.$r['stats']['errno']."-".$r['stats']['error']."</b>".'<br/>'.$r['query'].'<br/>';
                    die($r['query']);
                }
            }
        }
    }

    echo "procesar archivo Propiedades<br />";
    $archivo = ACTUALIZ.ARCHIVO_PROPIEDADES;
    if (file_exists($archivo)) {
        
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        foreach ($lineas as $linea) {
        
        
            $registro = explode("\t", $linea);
        
            if ($registro[0] != "") {
                $r = $db->insert('propiedades', Array(
                    'cedula' => $registro[0],
                    'id_inmueble' => $registro[1],
                    'apto' => $registro[2],
                    'alicuota' => $registro[3],
                    'meses_pendiente' => $registro[4],
                    'deuda_total' => str_replace("\r", "", $registro[5])
                        ));
                if($r["suceed"]==FALSE){
                    echo "<b>Archivo Propiedades: ".$r['stats']['errno']."-".$r['stats']['error']."</b><br />";
                    die($r['query']);
                }
            }
        }
    }

    echo "procesar archivo Facturas<br />";
    $archivo = ACTUALIZ.ARCHIVO_FACTURA;
    if (file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        foreach ($lineas as $linea) {
            $registro = explode("\t", $linea);
            if ($registro[0] != "") {
                $r = $db->insert('facturas', Array(
                    'id_inmueble' => $registro[0],
                    'apto' => $registro[1],
                    'numero_factura' => $registro[2],
                    'periodo' => $registro[3],
                    'facturado' => $registro[4],
                    'abonado' => $registro[5],
                    'fecha' => $registro[6],
                    'facturado_usd' => $registro[7]
                        ));
                if($r["suceed"]==FALSE){
                    die($r['stats']['errno']."<br />".$r['stats']['error']);
                }
            }
        }
    }

    echo "procesar archivo Detalla Factura<br />";
    $archivo = ACTUALIZ . ARCHIVO_FACTURA_DETALLE;
    if (file_exists($archivo)) {

        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        foreach ($lineas as $linea) {
            $registro = explode("\t", $linea);
            if ($registro[0] != "") {
                $r = $db->insert("factura_detalle", Array(
                    "id_factura" => $registro[0],
                    "detalle" => utf8_encode($registro[1]),
                    "codigo_gasto" => $registro[2],
                    "comun" => $registro[3],
                    "monto" => str_replace("\r","",$registro[4])
                        ));
                if($r["suceed"]==FALSE){
                    die($r['stats']['errno']."<br />".$r['stats']['error'].'<br/>'.$r['query']);
                }
            }
        }
    }

    echo "procesar archivo Historico de Cobro<br />";
    $archivo = ACTUALIZ."HISTORICO_AVISOS_COBRO.txt";
    if (file_exists($archivo)) {
        echo "procesar archivo historico de avisos<br />";
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
            foreach ($lineas as $linea) {
                $registro = explode("\t", $linea);
                if ($registro[0] != "") {
                    $r = $db->insert("historico_avisos_cobro", Array(
                            "id_inmueble" => $registro[0],
                            "apto" => $registro[1],
                            "numero_factura" => $registro[2],
                            "periodo" => $registro[3]),"IGNORE"
                        );
                    if ($r["suceed"] == FALSE) {
                        die($r['stats']['errno'] . "<br />" . $r['stats']['error'] . '<br/>' . $r['query']);
                    }
                }
            }
    }

    echo "procesar archivo Soportes del Prerecibo<br />";
    $archivo = ACTUALIZ."PRERECIBO.txt";
    if (file_exists($archivo)) {
        echo "procesar archivo soportes aviso de cobro<br />";
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
            foreach ($lineas as $linea) {
                $registro = explode("\t", $linea);
                if ($registro[0] != "") {
                    $r = $db->insert("prerecibo_soportes", Array(
                            "id_inmueble" => $registro[0],
                            "codigo_gasto" => $registro[1],
                            "periodo" => $registro[2],
                            "descripcion" => $registro[3],
                            "total" => $registro[4]),"","ON DUPLICATE KEY UPDATE total='".$registro[4]."'"
                        );
                    if ($r["suceed"] == FALSE) {
                        die($r['stats']['errno'] . "<br />" . $r['stats']['error'] . '<br/>' . $r['query']);
                    }
                }
            }
    }

    echo "procesar archivo movimiento caja<br />";
    $archivo = ACTUALIZ."MOVIMIENTO_CAJA.txt";
    if (file_exists($archivo)) {
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        echo "procesar archivo movimiento caja (" . count($lineas) . ")<br />";
        $mensaje .= "procesar archivo movimiento caja (" . count($lineas) . ")<br />";
        $pago = new pago();
        foreach ($lineas as $linea) {

            $registro = explode("\t", $linea);

            if ($registro[0] != "") {

                $registro = Array(
                    "numero_recibo" => $registro[0],
                    "fecha_movimiento" => $registro[1],
                    "forma_pago" => utf8_encode($registro[2]),
                    "monto" => $registro[3],
                    "cuenta" => utf8_encode($registro[4]),
                    "descripcion" => utf8_encode($registro[5]),
                    "id_inmueble" => $registro[6],
                    "id_apto" => str_replace("\r", "", $registro[7])
                            );

                $r = $pago->insertarMovimientoCaja($registro);


                if ($r["suceed"] == FALSE) {
                    die($r['stats']['errno'] . "<br />" . $r['stats']['error'] . '<br/>' . $r['query']);
                }
            }
        }

    }

    echo "procesar archivo Estado de Cuenta Inmueble <br />";
    $archivo = ACTUALIZ.ARCHIVO_EDO_CTA_INM;
    if(file_exists($archivo)) {
        $contenidoFichero = JFile::read($archivo);
        $lineas = explode("\r\n", $contenidoFichero);
        $items = count($lineas) - 1;
        echo "procesar archivo estado de cuenta inmueble (".$items.")<br />";
        $mensaje.="procesar archivo estado de cuenta inmueble (".$items.")<br />";
        foreach ($lineas as $linea) {
        
        
            $registro = explode("\t", $linea);
        
        
            if ($registro[0] != "") {
        
                $registro = [
                    'id_inmueble' => $registro[0],
                    'apto'        => $registro[1],
                    'propietario' => utf8_encode($registro[2]),
                    'recibos'     => $registro[3],
                    'deuda'       => $registro[4],
                    'deuda_usd'   => str_replace("\r", "", $registro[5])
                ];
        
        
                $r = $inmueble->insertarEstadoDeCuentaInmueble($registro);
        
        
                if (!$r["suceed"]) {
                    die($r['stats']['errno'] . "<br />" . $r['stats']['error'] . '<br/>' . $r['query']);
                }
            }
        }
    }

}
echo "****FIN DEL PROCESO DE ACTUALIZACION****<br />";
$fecha = date('d-m-Y');
echo "Fecha: ".$fecha."<br/>";
$mail = new mailto(mailPHP);
$r = $mail->enviar_email(NOMBRE_APLICACION.' '.$fecha,$mensaje, "", 'mhcalidadadmi@yahoo.es ',"");
        
if ($r=="") {
    echo "Email de confirmación enviado con éxito<br>";
} else {
    echo "Falló el envio del emailo de ejecución del proceso<br>";
}
echo "Cierre esta ventana para finalizar.";
