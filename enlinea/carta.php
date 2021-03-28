<?php
include_once '../includes/constants.php';
$junta_condominio = new junta_condominio();
$data = $_POST;

$modelo_carta = "carta-residencia-".$data['id_inmueble'].".html";
if (!file_exists($modelo_carta)) {
    echo "No est&aacute; disponible la carta de residencia para este condominio.<br>Contacte al administrador del sistema.<br>";
    echo "<a href='javascript:window.history.back();'>Regresar</a>";
    die();
}
$presidente = "";
$cedula_presidente = "";
// datos presidente junta de condominio
$junta = $junta_condominio->obtenerDatosPresidenteJuntaCondominioInmueble($data['id_inmueble']);
if ($junta['suceed']) {
    //var_dump($junta['data']);
    $presidente = $junta['data'][0]['nombre'];
    $cedula_presidente = $junta['data'][0]['cedula'];
} else {
    echo "No se pudo obtener la información de la junta de condominio. Contacte al administrador del sistema.<br>";
    echo "<a href='javascript:window.history.back();'>Regresar</a>";
    die();
}
// get the HTML
ob_start();
include($modelo_carta);

$content = ob_get_clean();

$fecha = getDate();
$mes = Array('', 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$fecha = $fecha["mday"]." de ".$mes[$fecha["mon"]]." de ".$fecha["year"]; 
// convert in PDF
require_once('../includes/html2pdf/html2pdf.class.php');
try
{
    $html2pdf = new HTML2PDF('P', 'Letter', 'fr',true,'UTF-8',array(20, 10, 20, 5));
    //$html2pdf->setModeDebug();
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->setDefaultFont('Arial');
    
    $content = str_replace("%nombre%",$_POST['nombre'],$content);
    $content = str_replace("%cedula%",$_POST['cedula'],$content);
    $content = str_replace("%numero_apartamento%",$data['apto'],$content);
    $content = str_replace("%años%",$data['years'],$content);
    $año = date("Y") - $data['years'];
    $content = str_replace("%año%",$año,$content);
    $content = str_replace("%fecha_hoy%",$fecha,$content);
    
    $content = str_replace("%presidente%",$presidente,$content);
    $content = str_replace("%cedula_presidente%",$cedula_presidente,$content);


    $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
    $html2pdf->Output('Carta-residencia.pdf','D');
}
catch(HTML2PDF_exception $e) {
    echo $e;
    exit;
}