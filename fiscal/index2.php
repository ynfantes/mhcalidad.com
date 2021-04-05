<?php
/*
 * login área de contabilidad
 */
include_once('./includes/db.php');
$db = new db();
$re = $db->select("*","empresa",Array("clave"=>filter_input(INPUT_POST, "clave")));

if ($re["suceed"] == true ) {
    
    if(count($re['data'])==0){
        if (filter_input(INPUT_POST, "clave")== ADMIN) {
           session_start();
           $_SESSION["usuario"] = "Administrador";
           $_SESSION['status']  = 'logueado';
           echo "Administrador";
        } else {
            echo "<p style=\"padding:0\"><i class=\"fa fa-warning\"></i> Usuario no registrado.</p>";
        }
    } else {
        session_start();
        $_SESSION['usuario'] = $re['data'][0];
        $_SESSION['status'] = 'logueado';
        echo count($re['data']);
    }
} else {
    echo $re["error"];
}
?>