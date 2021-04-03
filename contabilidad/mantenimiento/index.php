<?php
include_once '../includes/constants.php';

session_start();

$request_body = file_get_contents('php://input');

if($_SERVER['REQUEST_METHOD']=='POST') {

    
    $data = json_decode($request_body);
    
    if ($data === null) {
        
        $data = $_POST;
        
        $empresas = new empresa();
        
        if (isset($data['accion'])) {
            $id = $data['id'];
            unset($data['id'], $data['accion']);
            $r = $empresas->actualizar($id,$data);

            if ($r['suceed']) {
                $r['titulo'] = 'Cliente actualizado con éxito';
            } else {
                $r['titulo'] = 'No se pudo actualizar los datos este cliente';
            }

        } else {
            $r = $empresas->insertar($data);
            if ($r['suceed']) {
                $r['titulo'] = 'Cliente registrado con éxito';
            } else {
                $r['titulo'] = 'No se pudo registrar este cliente';
            }
        }
        
        
        $result = $empresas->listar();
        if ($result['suceed'] && count($result['data'])>0) {
            $data = $result['data'];
        }
        $opciones = Array(
            
            "session"   => $_SESSION,
            "listado"   => $data,
            "resultado" => $r
        );

        echo $twig->render(
            'contabilidad/clientes.html.twig',
            $opciones
        );

    } else {
       $empresas = new empresa();
        $result = $empresas->actualizar($data->item,Array('inactiva'=>$data->status));
        echo json_encode($result); 
    }
    
} elseif ($_SERVER['REQUEST_METHOD']=='DELETE') {
    
    $result = Array();
    $data = json_decode($request_body);
    $empresas = new empresa();
    $result = $empresas->borrar($data->id);
    unset($result['query']);
    echo json_encode($result);

} else {
    
    $accion = isset($_GET['accion']) ? $_GET['accion'] : '';
    switch ($accion) {
        
        case 'editar-cliente':
            $cliente = array();
            if(isset($_GET['id'])) {
                $empresas   = new empresa();
                $result     = $empresas->ver($_GET['id']);    
                if ($result['suceed'] && count($result['data'])>0) {
                    $cliente = $result['data'][0];
                }
            }
            echo $twig->render(
                'contabilidad/cliente.nuevo.html.twig',
                Array(
                    'session'   => $_SESSION,
                    'cliente'   => $cliente,
                    'accion'    => 'actualizar-cliente'
                )
            );
            break;

        case 'clientes':
            $data = array();
            $empresas = new empresa();
            $result = $empresas->listar();
            if ($result['suceed'] && count($result['data'])>0) {
                $data = $result['data'];
            }
            $opciones = Array(
                "session"   => $_SESSION,
                "listado"   => $data
            );

            echo $twig->render(
                'contabilidad/clientes.html.twig',
                $opciones
            );
            break;
        
        case 'registrar-nuevo':
            
            echo $twig->render(
                'contabilidad/cliente.nuevo.html.twig',
                Array(
                    'session'   => $_SESSION
                )
            );
            break;
        
        case 'guardar-cliente':
            $data = array();
            $cliente = $_POST;
            
            $empresas = new empresa();
            // Guardamos datos del cliente
            $result = $empresas->insertar($cliente);
            $result = $empresas->listar();
            if ($result['suceed'] && count($result['data'])>0) {
                $data = $result['data'];
            }
            $opciones = Array(
                "session"   => $_SESSION,
                "listado"   => $data
            );

            echo $twig->render(
                'contabilidad/clientes.html.twig',
                $opciones
            );

            break;

        case 'cambiar-status-cliente':
            $data = $_POST;
            var_dump($data);
            echo json_enconde($data);
            break;
        default:
            # code...
            break;
    }   
}
