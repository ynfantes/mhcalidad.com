<?php
include_once '../includes/constants.php';
session_start();
$session = $_SESSION;

if (isset($session['status']) && $session['status'] == 'logueado') {
    echo $twig->render('fiscal/index.html.twig',array("session" => $session));
} else {
    header("location:" . ROOT . "fiscal.html");
}