<?php
include_once '../../includes/constants.php';
if (isset($_GET['logout'])) {
    die();
}
$propietario = new propietario();
//$bitacora = new bitacora();

$accion = isset($_GET['accion']) ? $_GET['accion'] : "ver";
$id = isset($_GET['id']) ? $_GET['id'] : "perfil";
if ($id!= 'sac' && !is_numeric($id)) {
    $propietario->esPropietarioLogueado();
    $session = $_SESSION;
}

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="ver o actualizar">
    case "ver":
    case "actualizar":

        /* @var $datos_personales callable */
        $datos_personales = $propietario->ver($session['usuario']['id']);
        /*$bitacora->insertar(Array(
            "id_sesion"=>$session['id_sesion'],
            "id_accion"=> 3,
            "descripcion"=>$_SESSION['usuario']['nombre'],
        ));*/
     
        echo $twig->render('condominio/propietario.html.twig', array(
            "session"       => $session,
            "propietario"   => $datos_personales['data'][0],
            "accion"        => $accion,
            "id"            => $id));


        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="modificar">
    case "modificar":
        $data = $_POST;
        unset($data['actualizar']);
        
//        if ($_GET['id'] == 'perfil') {
            $exito = $propietario->actualizar($session['usuario']['id'], $data);
            $mensaje = "Datos actualizados con éxito!";
//            $bitacora->insertar(Array(
//                "id_sesion"=>$session['id_sesion'],
//                "id_accion"=> 14,
//                "descripcion"=>'',
//            ));
//        } else {
//            $exito = $propietario->ver($session['usuario']['id']);
//            if ($exito['suceed'] && count($exito['data']) > 0) {
//                if ($exito['data'][0]['clave'] == $data['clave_actual']) {
//                    unset($data['clave_actual']);
//                    $exito = $propietario->actualizar($session['usuario']['id'], $data);
//                    
//                    $mensaje = "Cambio de clave efectuado con éxito!.";
//                    $bitacora->insertar(Array(
//                        "id_sesion"=>$session['id_sesion'],
//                        "id_accion"=> 7,
//                        "descripcion"=>'',
//                    ));
//                } else {
//                    $mensaje = "Clave actual no concuerda.";
//                    $exito['suceed'] = false;
//                }
//            } else {
//                $mensaje = "El cambio de clave no se pudo procesar.";
//            }
//        
//        }
        if ($exito['suceed']) {
            $exito['mensaje'] = $mensaje;
        } else {
            if ($mensaje == "") {

                $mensaje = "Los cambios no puedieron guardarse.";
            }
            $exito['mensaje'] = $mensaje;
        }
        $datos_personales = $propietario->ver($session['usuario']['id']);
        
        echo $twig->render('condominio/propietario.html.twig', array(
            "session" => $session,
            "propietario" => $datos_personales['data'][0],
            "accion" => "actualizar",
            "resultado" => $exito));
            //"id" => $_GET['id'])
            //);
        break;
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="password">
    case "password":
        $datos_personales = $propietario->ver($session['usuario']['id']);
        echo $twig->render('condominio/password.html.twig', array(
            "session" => $session,
            "propietario" => $datos_personales['data'][0]));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="actualizar password">
    case "actualizar-password":
        $data = $_POST;
        $datos_personales = $propietario->ver($session['usuario']['id']);
        if ($data['clave_actual'] == '' || $data['clave'] == '') {
            $exito['suceed'] = false;
            $exito['mensaje'] = 'Debe rellenar ambos campos';
        } else {
            if ($data['clave_actual'] != $datos_personales['data'][0]['clave']) {
                $exito['suceed'] = false;
                $exito['mensaje'] = 'La contraseña actual no coincide con la registrada en el sistema';
            } else {
                if ($data['clave_actual'] == $data['clave']) {
                    $exito['suceed'] = false;
                    $exito['mensaje'] = 'La nueva contraseña no puede ser igual al anterior';
                } else {
                    unset($data['actualizar']);
                    unset($data['clave_actual']);
                    $data['modificado'] = 1;
                    $exito = $propietario->actualizar($session['usuario']['id'], $data);
                    $mensaje = "Contraseña actualizada con éxito!";
                    if ($exito['suceed']) {
                        $exito['mensaje'] = $mensaje;
                    } else {
                        if ($mensaje == "") {
                            $mensaje = "No puedo actualizarse la contraseña.";
                        }
                        $exito['mensaje'] = $mensaje;
                    }
                }
            }
        }
        echo $twig->render('condominio/password.html.twig', array(
            "session" => $session,
            "propietario" => $datos_personales['data'][0],
            "resultado" => $exito));
        //"id" => $_GET['id'])
        //);
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="claves actualizadas">
    case "clavesActualizadas":
        $listado = $propietario->listarPropietariosClavesActualizadas();


        if ($listado['suceed'] && count($listado['data'] > 0)) {
            foreach ($listado['data'] as $clave) {


                $propietario->actualizar($clave["id"], Array("cambio_clave" => 0));


                echo $clave["id_inmueble"] . "|";
                echo $clave["apto"] . "|";
                echo $clave["clave"] . "<br>";
            
                
            }
        }
        break; // </editor-fold>
       
    // <editor-fold defaultstate="collapsed" desc="propietarios actualizados">
    case "actualizados":
        $resultado = $propietario->obtenerPropietariosActualizados();
        if ($resultado['suceed'] && count($resultado['data']) > 0) {
            
            foreach ($resultado['data'] as $actualizado) {
                
                echo "|".$actualizado['cedula']."|".$actualizado['id_inmueble'];
                echo "|".$actualizado['apto']."|".$actualizado['clave']."|".substr(utf8_decode($actualizado['direccion']),0,254);
                echo "|".$actualizado['telefono1']."|".$actualizado['telefono2'];
                echo "|".$actualizado['telefono3']."|".$actualizado['email']."|".$actualizado['email_alternativo']."<br>";

            }
        }
        break;
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="enviar clave del servicio">

    case "clave-servicio":
        $propietario = new propietario();
        
        if (!is_numeric($id))
            $id = null;
        $propietario->envioMasivoEmail('Acceso servicio web', '../plantillas/clave-servicio.html', $id);
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="contacto">
    case "contacto":
        $inmueble = Array();
        $propiedad = new propiedades();
        $propiedades = $propiedad->propiedadesPropietario($session['usuario']['cedula']);
        if ($propiedades['suceed'] && count($propiedades['data']) > 0) {
            $inmueble = $propiedades['data'][0];
        }
        echo $twig->render('condominio/contacto.html.twig', Array(
            "session" => $session,
            "inmueble" => $inmueble
        ));
        break; 
    // </editor-fold>

}