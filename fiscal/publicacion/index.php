<?php
include_once '../includes/constants.php';
session_start();

if(isset($_FILES['archivo'])) {
    
    $publicacion = new publicacion();
    $empresas = new empresa();
    $reportes = new reporte();
    
    $data = $_POST;

    if (isset($data['mes']) && isset($data['año'])) {
        $data['periodo'] = $data['año'].'-';
        $data['periodo'].= isset($data['mes']) ? $data['mes'] : '12';
        $data['periodo'].= '-01';
    } elseif(isset($data['año'])) { 
        $data['periodo'] = $data['año'].'-12-31';
    }
    
    unset(
        $data['año'],
        $data['mes']
    );
    
    $reporte_id = $data['reporte'];
    $filename = uniqid().'.pdf';
    $data['archivo'] = $filename;

    $r = $publicacion->insertar($data);

    if ($r['suceed']) {
        $r['titulo'] = 'Se ha registrado con éxito esta publicación';
        if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/".$filename)) {
            $r['upload']    = true;
            $r['mensaje']   = '[Exito] Archivo guardado en el servidor.';
        } else {
            $r['upload']    = false;
            $r['mensaje']   = '[Error] No se ha podido publicar el archivo en el servidor';
        }
    } else {
        $r['titulo'] = 'No se ha podido registrar la publicación en la base de datos';
    }
    
    $result = $reportes->ver($reporte_id);
    if ($result['suceed'] && count($result['data'])>0) {
        $reporte = $result['data'][0];
    }

    $clientes = $empresas->listarEmpresasActivas();
    if ($clientes['suceed'] && count($clientes['data'])>0) {
        $data = $clientes['data'];
    }
    
    
    echo $twig->render(
        'fiscal/cargas.html.twig',
        array(
            'resultado' => $r,
            'reporte'   => $reporte,
            'clients'   => $data,
            'session'   => $_SESSION
            )
    );

}
