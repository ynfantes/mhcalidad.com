<?php
include_once '../../includes/constants.php';
$propietario = new propietario();

if (!isset($_GET['id'])) {    
    $propietario->esPropietarioLogueado();
    $session = $_SESSION;
}
$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="cancelacion">
    case "cancelacion":
        $titulo = $_GET['id'] . ".pdf";
        // place this code inside a php file and call it f.e. "download.php"
        $path = $_SERVER['DOCUMENT_ROOT']."/cancelacion.gastos/"; // change the path to fit your websites document structure

        $fullPath = $path.$titulo;

        if ($fd = fopen ($fullPath, "r")) {
            $fsize = filesize($fullPath);
            $path_parts = pathinfo($fullPath);
            $ext = strtolower($path_parts["extension"]);
            switch ($ext) {
                case "pdf":
                header("Content-type: application/pdf"); // add here more headers for diff. extensions
                header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
                break;
                default;
                header("Content-type: application/octet-stream");
                header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose ($fd);
        break;
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="ver">
    case "ver":
        $propiedad = new propiedades();
        $inmuebles = new inmueble();
        $pagos = new pago();

        $propiedades = $propiedad->propiedadesPropietario($_SESSION['usuario']['cedula']);
        $cuenta = Array();

        if ($propiedades['suceed'] == true) {

            foreach ($propiedades['data'] as $propiedad) {

                $inmueble = $inmuebles->ver($propiedad['id_inmueble']);
                $pago = $pagos->listarPagosProcesados($propiedad['id_inmueble'], $propiedad['apto'], 5);


                if ($pago['suceed'] == true) {

                    for ($index = 0; $index < count($pago['data']); $index++) {
                        if ($pago['data'][$index]['id_factura']!='') {
                            $filename = "../../cancelacion.gastos/" . $pago['data'][$index]['id_factura'] . ".pdf";
                            $pago['data'][$index]['recibo'] = file_exists($filename);
                        }
                    }

                    $cuenta[] = Array("inmueble" => $inmueble['data'][0],
                        "propiedades" => $propiedad,
                        "cuentas" => $pago['data']);
                                }
            }
        }
        
        echo $twig->render('enlinea/pago/cancelacion.html.twig', array("session" => $session,
            "cuentas" => $cuenta));


        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="guardar">
    case "guardar":
        $pago = new pago();
        $data = $_POST;

        if (count($data) > 0) {
            unset($data['registrar']);
            $data['fecha']=date("Y-m-d H:i:00 ", time());
            $exito = $pago->registrarPago($data);
        } else {
            header("location:" . URL_SISTEMA . "/pago/registrar");
            return;
        }
        
        echo json_encode($exito);
//        echo $twig->render('condominio/pago.registrar.html.twig', array("session" => $session,
//            "resultado" => $exito,
//            "accion" => "registrar"
//        ));
        break;
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="listar, registrar">
        case "registrar":
        case "listar":
        default :
        $propiedad = new propiedades();
        $facturas = new factura();
        $inmuebles = new inmueble();
        $resultado = Array();
        if ($accion == 'guardar') {
            $resultado = $exito;
        }
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
                            $r = pago::facturaPendientePorProcesar($factura['data'][$index]['periodo'], $factura['data'][$index]['id_inmueble'], $factura['data'][$index]['apto']);
                            if ($r['suceed'] && count($r['data'])>0) {
                                $factura['data'][$index]['pagado'] = 1;
                                $factura['data'][$index]['pagado_detalle']= "<i class='fa fa-calendar-o'></i> ".
                                        Misc::date_format($r['data'][0]['fecha'])."<br>".
                                        strtoupper($r['data'][0]['tipo_pago']." - Ref: ".
                                                $r['data'][0]['numero_documento']."<br>Monto: ".
                                                number_format($r['data'][0]['monto'],2,",","."));
                            } else {
                                $factura['data'][$index]['pagado'] = 0;
                                $factura['data'][$index]['pagado_detalle']='';
                            }
                        }
                    }
                    
                    $cuenta[] = Array(
                            "inmueble"  => $inmueble['data'][0],
                            "propiedades" => $propiedad,
                            "cuentas"   => $factura['data'],
                            "resultado" => $resultado
                            );
                }
            }
        }
            //var_dump($cuenta);
            echo $twig->render('condominio/pago.registrar.html.twig', array(
            "session" => $session,
            "cuentas"   => $cuenta,
            "accion"    => $accion
            ));
        break; 
// </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="listaPagoDetalle">
    case "listaPagosDetalle":
        $pagos = new pago();
        $pago_detalle = $pagos->detalleTodosPagosPendientes();
        if ($pago_detalle['suceed'] && count($pago_detalle['data']) > 0) {
            echo "id_pago,id_inmueble,id_apto,monto,id_factura<br>";
            foreach ($pago_detalle['data'] as $value) {
                echo $value['id_pago'] . ",";
                echo $value['id_inmueble'] . ",";
                echo $value['id_apto'] . ",";
                echo $value['monto'] * 100 . ",";
                echo $value['id_factura'];
                echo "<br>";
            }
        }
        break; 
// </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="listaPagosMaestros">
    case "listaPagosMaestros":
        $pagos = new pago();
        $pagos_maestro = $pagos->listarPagosPendientes();


        if ($pagos_maestro['suceed'] && count($pagos_maestro['data']) > 0) {
            echo "id,fecha,tipo_pago,numero_documento,fecha_documento,monto,banco_origen,";
            echo "banco_destino,numero_cuenta,estatus,email,enviado,telefono<br>";
            foreach ($pagos_maestro['data'] as $pago) {
                echo $pago['id'] . ",";
                echo Misc::date_format($pago['fecha']) . ",";
                echo strtoupper($pago['tipo_pago']) . ",";
                echo $pago["numero_documento"] . ",";
                echo Misc::date_format($pago["fecha_documento"]) . ",";
                echo $pago["monto"] * 100 . ",";
                echo $pago["banco_origen"] . ",";
                echo $pago["banco_destino"] . ",";
                echo str_replace("-", "", "#".$pago["numero_cuenta"]) . ",";
                echo strtoupper($pago["estatus"]) . ",";
                echo $pago["email"] . ",";
                echo $pago["enviado"] . ",";
                echo $pago["telefono"];
                echo "<br>";
            }
        }
        break; 
// </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="listaPagosPendientes">
    case "listarPagosPendientes":
        $pagos = new pago();
        $pagos_maestro = $pagos->listarPagosPendientes();
        
        if ($pagos_maestro['suceed'] && count($pagos_maestro['data']) > 0) {
            
            foreach ($pagos_maestro['data'] as $pago) {

                $pago_detalle = $pagos->detallePagoPendiente($pago['id']);
                
                if ($pago_detalle['suceed'] && count($pago_detalle['data'] > 0)) {
                    $enviado = $pago["enviado"] == 0 ? "False" : "True";
                    echo "|" . $pago['id'] . "|";
                    echo Misc::date_format($pago['fecha']) . "|";
                    echo strtoupper($pago['tipo_pago']) . "|";
                    echo $pago["numero_documento"] . "|";
                    echo Misc::date_format($pago["fecha_documento"]) . "|";
                    echo Misc::number_format($pago["monto"]) . "|";
                    echo $pago["banco_origen"] . "|";
                    echo $pago["banco_destino"] . "|";
                    echo $pago["numero_cuenta"] . "|";
                    echo strtoupper($pago["estatus"]) . "|";
                    echo $pago["email"] . "|";
                    echo $enviado . "|";
                    echo $pago["telefono"] . "|";
                    // --
                    foreach ($pago_detalle['data'] as $value) {
                        echo $value['id_inmueble'] . "|";
                        echo $value['id_apto'] . "|";
                        echo Misc::number_format($value['monto']) . "|";
                        echo $value['id_factura'] . "|";
                        echo $value['periodo'] . "|";
                    }
                    echo "<br>";
                
                }
            
            }
        

        } else {
            echo "0";
        }


        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="confirmación de pago">
    case "confirmar":
        $pago = new pago();
        $id = $_GET['id'];
        $estatus = $_GET['estatus'];
        $r = $pago->procesarPago($id, $estatus);
        echo $r;
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="reenviar email registro pago">
    case "reenviarEmailRegistroPago":
        $pago = new pago();
        $id = $_GET['id'];
        $pago->enviarEmailPagoRegistrado($id);
        break; 
    // </editor-fold>
}