<?php
date_default_timezone_set("America/Caracas");
$debug          = false;
$sistema        = "/";
$email_error    = false;
$mostrar_error  = true;

// </editor-fold>
// <editor-fold defaultstate="collapsed" desc="Cheqeuo servidor">
if ($_SERVER['SERVER_NAME'] == "www.mhcalidadadministrativa.com" | $_SERVER['SERVER_NAME'] == "mhcalidadadministrativa.com") {
    $user           = "mhcalida_fiscal";
    $password       = "fiscal5231";
    $db             = "mhcalida_fiscal";
    $email_error    = true;
    $mostrar_error  = FALSE;
    $debug          = FALSE;
    $sistema        = "/";
} else {
    $user       = "root";
    $password   = "";
    $db         = "mhcalida_fiscal";
    
}

define("HOST", "localhost");
define("USER", $user);
define("PASSWORD", $password);
define("DB", $db);
define("SISTEMA", $sistema);
define("EMAIL_ERROR", $email_error);
define("EMAIL_CONTACTO", "ynfantes@gmail.com");
define("EMAIL_TITULO", "error");
define("MOSTRAR_ERROR", $mostrar_error);
define("DEBUG", $debug);
define("TITULO", "MH Calidad Administrativa");
define("ROOT", 'http://' . $_SERVER['SERVER_NAME'] . SISTEMA);
define("URL_SISTEMA", ROOT . "enlinea");
define("SERVER_ROOT", $_SERVER['DOCUMENT_ROOT'] . SISTEMA);
define("mailPHP",0);
define("sendMail",1);
define("SMTP",2);

include_once SERVER_ROOT . 'includes/twig/lib/Twig/Autoloader.php';
include_once SERVER_ROOT . 'includes/extensiones.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem(SERVER_ROOT . 'template');
$twig = new Twig_Environment($loader, array(
            'debug' => true,
            'cache' => false,
            /*'cache' => SERVER_ROOT . 'cache',*/
            "auto_reload" => true)
);
$twig->addExtension(new extensiones());
$twig->addExtension(new Twig_Extension_Debug());

spl_autoload_register(function($clase) {
    include_once SERVER_ROOT . "fiscal/includes/" . $clase . ".php";
});


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

define("SMTP_SERVER","host.caracaspanel.com");
define("PORT","465");
define("USER_MAIL","info@mhcalidadadministrativa.com");
define("PASS_MAIL","edgar5231");
define("ADMIN","122932636");