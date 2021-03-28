<?php
include_once './includes/constants.php';

session_start();
$documento = $_SESSION['usuario']['Id']."_".filter_input(INPUT_GET, 'doc');
$content='Content-type: application/pdf';

if (file_exists(realpath('./documentos/'.$documento.'.pdf'))) {
    $url = ROOT.'contabilidad/documentos/'.$documento.".pdf"; 
    header('Content-Disposition: attachment; filename="'.$documento.'"');
    header($content);
    readfile($url);
} else {
    if (filter_input(INPUT_GET, 'doc')=='CONSTANCIA_DE_TRABAJO') {
        $archivos = Array();
        $urls = Array();
        $filesizes = Array();
        $dir = realpath('./documentos/');
        $archivos = glob("" . $directorio . "*CONSTANCIA_DE_TRABAJO*.pdf");
        foreach($archivos as $archivo) {
            $file[] = basename($archivo,".pdf");
        }
//        if ($dh = opendir($dir)){
//        while (($file = readdir($dh)) !== false){
//            if ($file!='.' && $file!='..') {
//                $data = explode('.',$file);
//                if (strpos($data[0], $documento)!==false) {
//                    $archivos[] = $data[0];
//                    $urls[] = ROOT.'contabilidad/documentos/'.$file;
//                    $filesizes[] = filesize($dir.'/'.$data[0].'.pdf');
//                }
//            }
//        }
//        closedir($dh);
//        }
        if (count($archivos)==1) {
            $url = $urls[0]; 
            header('Content-Disposition: attachment; filename="'.$archivos[0].'"');
            header($content);
            readfile($url);
        } else {
            header('location:' . URL_SISTEMA . '/ver.php?doc='.filter_input(INPUT_GET, 'doc'));
        }
    } else {
        echo "Esper unos segundos....<br>Lo estamos direccionando a la pagina principal.....";
    }
?>
<script type="text/javascript">
    alert("El informe que trata de consultar no ha sido publicado");
    //window.history.back();
    window.location = '<? echo URL_SISTEMA ?>';
</script>
<?php 
}