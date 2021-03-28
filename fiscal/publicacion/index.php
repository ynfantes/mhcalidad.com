<?php
include_once '../includes/constants.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : "listar";

switch ($accion) {
    case 'registrar':
        $publicado = new publicacion();
        unset($_GET['accion']);
        $data = $_GET;
        $data['periodo'] = Misc::format_mysql_date($data['periodo']);
        if ($publicado->reporteYaRestrido($data)) {
            echo "Bien: El reporte ya estaba publicado y ha sido actualizado";
        } else {
            $resultado = $publicado->insertar($data);
            if ($resultado['suceed']) {
                echo "Muy bien: El reporte ha sido publicado.";
            } else {
                echo $resultado["stats"]["error"];
            }
        }
        break;
}

?>
