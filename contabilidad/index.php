<?php
include_once '../includes/constants.php';
session_start();
$session = $_SESSION;

if (isset($session['status']) && $session['status'] == 'logueado') {
    if ($session['pqc']) {
        echo $twig->render('contabilidad/pqc.html.twig',array("session" => $session));
    } else {
        echo $twig->render('contabilidad/index.html.twig',array("session" => $session));
    }
} else {
    header("location:".ROOT."administracion-contabilidad.html");
}