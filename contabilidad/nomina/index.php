<?php
include_once '../includes/constants.php';

session_start();

if ($_SERVER['REQUEST_METHOD']=='POST') {
    
    $data = $_POST;

    if(isset($_FILES['archivo'])) {
        
        $reporte = $data['reporte'];
        
        switch ($reporte) {
            case 'nomina':
            case 'cestaticket':
            case 'prestaciones':
            case 'vacaciones':
                $tabla = "calculo_".$reporte;                
                break; 
            
            case 'recibos-pago-nomina':
            case 'recibos-pago-cestaticket':
            case 'balance-ingresos-egresos':
                $tabla = str_replace("-","_",$reporte);
                break;
             
            default:
                $tabla = 'otros_reportes';
                $data['id_reporte'] = $reporte;
                break;
        }

        $r = array();        
        $registro = new registro();
        $registro->tabla($tabla);
        $filename = uniqid().'.pdf';
        $data['archivo'] = $filename;

        if (isset($data['mes']) && isset($data['year'])) {
            $data['mes'] = $data['year'].'-'.$data['mes'].'-01';
        }
        unset(
            $data['reporte'],
            $data['year']
        );

        $r = $registro->insertar($data);

        if ($r['suceed']) {
            $r['titulo'] = 'Se ha registrado con éxito esta publicación';
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/".$filename)) {
                $r['upload']    = true;
                $r['mensaje']   = '[Exito] Archivo guardado en el servidor.';
            } else {
                $r['upload']    = false;
                $r['mensaje']   = '[Erro] No se ha podido publicar el archivo en el servidor';
            }
        } else {
            $r['titulo'] = 'No se ha podido registrar la publicación en la base de datos';
        }
        
        $empresas = new empresa();
        $clientes = $empresas->listarEmpresasActivas();
        $data = array();

        if ($clientes['suceed'] && count($clientes['data'])>0) {
            $data = $clientes['data'];
        }
        echo $twig->render(
            'contabilidad/cargas.html.twig',
            array(
                'resultado' => $r,
                'reporte'   => $reporte,
                'clients'   => $data,
                'session'   => $_SESSION
                )
        );
    }
    
} else {
    
    $accion  = isset($_GET['accion']) ? $_GET['accion'] : '';
    $reporte = isset($_GET['id']) ? $_GET['id'] : '';

    if ($_SESSION["usuario"] === "Administrador") {

        menuAdministrador($accion,$reporte,$twig);

    } else {

        menuCliente($accion,$reporte,$twig);

    }    

}

function menuAdministrador($accion,$reporte,$twig) {
    
    switch ($accion) {
        
        case 'recibos-pago':
            $reporte = $accion.'-'.$reporte;
            break;

        case 'contabilidad':
            $reporte = 'balance-'.$reporte;
            break;

        // default:
        //     echo $twig->render(
        //         'contabilidad/descargas.html.twig',
        //         array(
        //             'session' => $_SESSION
        //             )
        //     );
    }
    $empresas = new empresa();
    $clientes = $empresas->listarEmpresasActivas();
    $data = array();
    if ($clientes['suceed'] && count($clientes['data'])>0) {
        $data = $clientes['data'];
    }
    echo $twig->render(
        'contabilidad/cargas.html.twig',
        array(
            'reporte' => $reporte,
            'clients' => $data,
            'session' => $_SESSION
            )
    );
}

function menuCliente($accion,$reporte,$twig) {

    $criterio = Array("id_empresa"=>$_SESSION['usuario']['Id']);
    
    $titulo = '';
    if ($accion == 'recibos-pago') {
        $reporte = "$accion-$reporte";
    }
    
    switch ($reporte) {
    
        case 'nomina':
        case 'cestaticket':
        case 'prestaciones':
        case 'vacaciones':
            $titulo = "cálculo $reporte";
            $tabla = "calculo_".$reporte;
            $order = ($reporte == 'nomina') ? 
                Array("mes"=>"DESC","periodicidad"=>"ASC") :
                $reporte == 'vacaciones' ? Array("id"=>"DESC") :
                Array("mes"=>"DESC");
            break; 
        
        case 'recibos-pago-nomina':
        case 'recibos-pago-cestaticket':
            $titulo = str_replace("-"," ", $reporte);
            $tabla = str_replace("-","_",$reporte);
            $order = ($reporte == 'recibos-pago-nomina') ? 
                Array("mes"=>"DESC","periodicidad"=>"ASC") :
                Array("mes"=>"DESC");
            break;
        
        case 'ingresos-egresos':
            $titulo = 'Resumen '.str_replace("-"," ", $reporte);
            $tabla = 'balance_'.str_replace("-","_",$reporte);
            $order = Array("mes"=>"DESC");
            break;
        
        case 'comprobacion':
        case 'general':
        case 'resultado':
            $reporte = 'balance-'.$reporte;
            
        default:
            $titulo.= str_replace("-"," ", $reporte);
            $tabla = 'otros_reportes';
            $order = Array("id"=>"DESC");
            $criterio['id_reporte'] = $reporte;
            break;
    }
    $registro = new registro();
    $registro->tabla($tabla);
    
    $result = $registro->listarReportes($criterio, $order);
    unset($result['query']);
    // echo '<pre>';
    // print_r($result);
    // echo '</pre>';
    echo $twig->render(
        'contabilidad/descargas.html.twig',
        array(
            'session'   => $_SESSION,
            'listado'   => $result,
            'titulo'    => $titulo
            )
    );
}
