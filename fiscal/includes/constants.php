<?php

// <editor-fold defaultstate="collapsed" desc="configuracion regional">
date_default_timezone_set("America/Caracas");
// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="init">
$debug = true;
$sistema = "/nuevo/";
$email_error = true;
$mostrar_error = true;

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Cheqeuo servidor">
if ($_SERVER['SERVER_NAME'] == "www.mhcalidadadministrativa.com" | $_SERVER['SERVER_NAME'] == "mhcalidadadministrativa.com") {
    $user = "mhcalida_fiscal";
    $password = "fiscal5231";
    $db = "mhcalida_fiscal";
    $email_error = true;
    $mostrar_error = FALSE;
    $debug = FALSE;
    $sistema = "/nuevo/";
} else {
    $user = "mhcalida_fiscal";
    $password = "fiscal5231";
    $db = "mhcalida_fiscal";
    
}

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Acceso a la BD">
define("HOST", "localhost");
define("USER", $user);
define("PASSWORD", $password);
define("DB", $db);
// </editor-fold>
//<editor-fold defaultstate="collapsed" desc="configuracion de ficheros del sistema">
define("SISTEMA", $sistema);
define("EMAIL_ERROR", $email_error);
define("EMAIL_CONTACTO", "ynfantes@gmail.com");
define("EMAIL_TITULO", "error");
define("MOSTRAR_ERROR", $mostrar_error);
define("DEBUG", $debug);

define("TITULO", "MH Calidad Administrativa");
/**
 * para las urls
 */
define("ROOT", 'http://' . $_SERVER['SERVER_NAME'] . SISTEMA);
define("URL_SISTEMA", ROOT . "enlinea");
/**
 * para los includes
 */
define("SERVER_ROOT", $_SERVER['DOCUMENT_ROOT'] . SISTEMA);
define("mailPHP",0);
define("sendMail",1);
define("SMTP",2);
//</editor-fold>

//<editor-fold defaultstate="collapsed" desc="autoload">

function __autoload($clase) {
    include_once SERVER_ROOT . "/fiscal/includes/" . $clase . ".php";
}

spl_autoload_register("__autoload", false);
//</editor-fold>
//<editor-fold defaultstate="collapsed" desc="cerrar sesión">
if (isset($_GET['logout']) && $_GET['logout'] == true) {
    session_start();
    if (isset($_SESSION['status'])) {
        unset($_SESSION['status']);
        unset($_SESSION['usuario']);
        session_unset();
        session_destroy();

        if (isset($_COOKIE[session_name()]))
            setcookie(session_name(), '', time() - 1000);

    }
    header("location:" . ROOT . "fiscal.html");
}
//</editor-fold>

define("SMTP_SERVER","host.caracaspanel.com");
define("PORT","465");
define("USER_MAIL","info@mhcalidadadministrativa.com");
define("PASS_MAIL","edgar5231");
define("ADMIN","122932636");