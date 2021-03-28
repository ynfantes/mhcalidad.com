<?php
// get the HTML
    ob_start();
    include($modelo_solvencia);
    
    $content = ob_get_clean();
    
    $fecha = getDate();
    $mes = Array('', 'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
    $fecha = $fecha["mday"]." de ".$mes[$fecha["mon"]]." de ".$fecha["year"]; 
    // convert in PDF
    
    require_once('../includes/html2pdf/html2pdf.class.php');
    
    try
    {
        $html2pdf = new HTML2PDF('P', 'Letter', 'fr',true,'UTF-8',array(20, 10, 20, 5));
//      $html2pdf->setModeDebug();
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->setDefaultFont('Arial');
        $content = str_replace("%nombre%",$nombre,$content);
        $content = str_replace("%cedula%",$cedula,$content);
        $content = str_replace("%numero_apartamento%",$numero_apartamento,$content);
        $content = str_replace("%fecha_hoy%", $fecha ,$content);
        
        $content = str_replace("%presidente%",$presidente,$content);
        $content = str_replace("%cedula_presidente%",$cedula_presidente,$content);
        
        if ($_GET['clase']=='h') {
            $content = str_replace("%monto_pago%",$monto_pago,$content);
            $content = str_replace("%fecha_pago%",$fecha_pago,$content);
            $content = str_replace("%periodo%",$periodo,$content);
            $content = str_replace("%banco%",$banco,$content);
            $content = str_replace("%numero_cheque%",$numero_cheque,$content);
        }
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('exemple14.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }