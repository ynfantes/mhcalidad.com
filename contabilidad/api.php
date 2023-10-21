<?php
include_once 'includes/constants.php';

session_start();
$session = $_SESSION;

$request_body = file_get_contents('php://input');

//$result = Array();
//$result['method'] = $_SERVER['REQUEST_METHOD'];
$data = json_decode($request_body);

if ($_SERVER['REQUEST_METHOD']==='POST') {
    
    $reporte =  $data->reporte;
    $criterio = Array("id_empresa"=>$data->id_empresa);

    switch ($reporte) {
        case 'nomina':
        case 'cestaticket':
        case 'prestaciones':
        case 'vacaciones':

            $tabla = "calculo_".$reporte;
            $order = $reporte == 'nomina' ? 
                ["mes"=>"DESC","periodicidad"=>"ASC"] :
                ($reporte == 'vacaciones' ? ["id" => "DESC"] : ["mes"=>"DESC"]);
            break; 
        
        case 'recibos-pago-nomina':
        case 'recibos-pago-cestaticket':
            $tabla = str_replace("-","_",$reporte);
            $order = ($reporte == 'recibos-pago-nomina') ? 
                Array("mes"=>"DESC","periodicidad"=>"ASC") :
                Array("mes"=>"DESC");
            break;
        
        case 'balance-ingresos-egresos':
            $tabla = str_replace("-","_",$reporte);
            $order = Array("mes"=>"DESC");
            break;

        default:
            $tabla = 'otros_reportes';
            $order = Array("id"=>"DESC");
            $criterio['id_reporte'] = $reporte;
            break;

    }
    

    $registro = new registro();
    $registro->tabla($tabla);
    $result = $registro->listarReportes($criterio, $order);
    unset($result['query']);
    $result['tabla']    = $tabla;
    $result['reporte']  = $reporte;
    echo json_encode($result);

} elseif ($_SERVER['REQUEST_METHOD']==='DELETE'){

    $result = Array();
    $reporte =  $data->table;
    
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
            break;
    }

    $registro = new registro();
    $registro->tabla($tabla);
    $result = $registro->ver($data->item);

    if ($result['suceed'] && count($result['data'])>0) {
        
        $archivo = $result['data'][0]['archivo'];

        $result = $registro->borrar($data->item);
        
        if ($result['suceed'] && $result['stats']['affected_rows'] > 0) {
            
            // Eliminar archivo
            unlink('documentos/'.$archivo);
            
        }   
    }
    
    echo json_encode($result);

}
