<?php
/*
 *  login condominio en línea
 */
/*include_once('../includes/constants.php');

$db = new db();
$re = $db->select("*","propietarios",Array("cedula"=>  "'".filter_input(INPUT_POST, 'cedula')."'"));

if ($re["suceed"] == true ) {
    if(count($re['data'])==0){
        if (filter_input(INPUT_POST, "cedula")== ADMIN) {
           session_start();
           $_SESSION["cpanel"] = 1;
           $_SESSION["usuario"] = "Administrador";
           $_SESSION['status']  = 'logueado';
           echo "Administrador";
        } else {
            echo "<p style=\"padding:0\"><i class=\"fa fa-warning\"></i> Usuario no registrado.</p>";
        }
    } else {
        session_start();
        $junta_condominio = '';
        $res = $db->select("*","junta_condominio",Array("cedula"=>$re['data'][0]['cedula']));
        if ($res['suceed'] && count($res['data'])> 0) {
            $junta_condominio = $res['data'][0]['id_inmueble'];
        }
        $_SESSION["usuario"] = $re['data'][0];
        $_SESSION['junta']   = $junta_condominio;
        $_SESSION["nombre"]  = $re['data'][0]['nombre'];
        $_SESSION["email"]   = $re['data'][0]['email'];
        $_SESSION["cedula"]  = $re['data'][0]['cedula'];
        $_SESSION['status']  = 'logueado';
        $_SESSION["cpanel"]  = 0;
        
        echo count($re['data']);
    }
} else {
    echo $re["error"];
}*/
include_once '../includes/constants.php';

$propietario = new propietario();

$result = array();
$cedula = '';
$password = '';

if (isset($_POST['login'])) {
    $cedula     = $_POST['cedula'];
    $password   = $_POST['password'];
    $result     = $propietario->login($cedula, $password, 0);
    echo json_encode($result);    
} else {
    if (isset($_POST['email'])) {
        $result = $propietario->recuperarContraSena($_POST['email']);
        echo json_encode($result);
    } else {
        echo $twig->render(
            'index2.html.twig', 
            array(
                "mensaje"   => $result,
                "cedula"    => $cedula,
                "password"  => $password
                )
            );
    }
}