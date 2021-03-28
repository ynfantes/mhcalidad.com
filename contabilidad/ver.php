<?php
include_once '../includes/constants.php';

session_start();
$session = $_SESSION;
$patron = filter_input(INPUT_GET, "doc");
$directorio = realpath(".")."/documentos/";
$archivos = glob("".$directorio.$session['usuario']['Id']."_*$patron*.pdf");
$file = Array();
foreach($archivos as $archivo) {
  $file[] = basename($archivo,".pdf");
}

echo $twig->render('contabilidad/descargas.html.twig',
        array(
            "session"   => $session,
            "archivos"  => $file,
            "doc"       => $patron
        )
    );