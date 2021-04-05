<?php
include_once 'includes/constants.php';

session_start();
$session = $_SESSION;

$request_body = file_get_contents('php://input');

//$result = Array();
//$result['method'] = $_SERVER['REQUEST_METHOD'];
$data = json_decode($request_body);

if ($_SERVER['REQUEST_METHOD']=='POST') {
    
    $registro = new publicacion();
    $reporte =  $data->reporte;
    $result = $registro->listarReportes($data->id_empresa, $data->reporte);
    unset($result['query']);
    echo json_encode($result);

} elseif ($_SERVER['REQUEST_METHOD']=='DELETE'){

    $result = Array();
    $registro = new publicacion();
    
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
