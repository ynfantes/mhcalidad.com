<?php
include_once '../../includes/constants.php';
$db = new db();
session_start();
if (!$_SESSION['cpanel']) {
    header("location:" . ROOT );
    die();
}
$accion = isset($_GET['accion']) ? filter_input(INPUT_GET, 'accion') : "cartelera";

$session = $_SESSION;

switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="cartelera condominio">
    case "cartelera":
    default :
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        $ca = $db->select("*", "cartelera", "", "", Array("id_inmueble" => "ASC,", "id" => "DESC"));


        echo $twig->render('condominio/administrador.cartelera.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "cartelera" => $ca['data']
                )
        );
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="prerecibo">
    case "prerecibo":
        $id_inmueble = isset($_POST['id_inmueble']) ? $_POST['id_inmueble'] : '';
        $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : '';
        $periodos = array();
        $detalle = array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if ($periodo != '') {
            $pe = $db->select("distinct periodo", "prerecibo_soportes", Array("id_inmueble" => "'" . $id_inmueble . "'"), "", Array("periodo" => "ASC"));
            if ($pe['suceed'] && count($pe['data']) > 0) {
                $periodos = $pe['data'];
            }
        }


        if ($id_inmueble != '' && $periodo != '') {
            $listado = $db->select("*", "prerecibo_soportes", Array("id_inmueble" =>$id_inmueble,"periodo"=>$periodo), "", Array("codigo_gasto" => "ASC"));
            if ($listado['suceed'] && count($listado['data']) > 0) {
                for ($index = 0; $index < count($listado['data']); $index++) {
                    if ($listado['data'][$index]['archivo']!='') {
                        $filename = "../prerecibo/".$listado['data'][$index]['archivo'];
                        $listado['data'][$index]['existe'] = file_exists($filename);    
                    } else {
                        $listado['data'][$index]['existe'] = false;
                    }
                }
                $detalle = $listado['data'];
            }
        }
        echo $twig->render('condominio/administrador.prerecibo.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "periodos" => $periodos,
            "id_inmueble" => $id_inmueble,
            "periodo" => $periodo,
            "detalle" => $detalle
                )
        );
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="eliminar-itemprerecibo">
    case "eliminar-itemprerecibo":
        $r = $db->delete("prerecibo_soportes", Array("id" => filter_input(INPUT_POST, "id")));

        if ($r["suceed"] == FALSE) {
            echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
        } else {
            echo filter_input(INPUT_POST, "id");
        }
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="periodos">
    case "periodos":
        $pe = $db->select("distinct periodo", "prerecibo_soportes", Array("id_inmueble" => $_POST['id']), "", Array("periodo" => "ASC"));
        if ($pe['suceed']) {
            if (count($pe['data']) > 0) {
                echo json_encode($pe['data']);
            }
        }
        break; 
    // </editor-fold>
      
    // <editor-fold defaultstate="collapsed" desc="publicar-soporte">
    case "publicar-soporte":
        $id_inmueble = isset($_POST['id_inmueble']) ? $_POST['id_inmueble'] : '';
        $periodo = isset($_POST['periodo']) ? $_POST['periodo'] : '';
        $periodos = array();
        $detalle = array();


        if (isset($_FILES['archivo']) && isset($_POST['id'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id = filter_input(INPUT_POST, "id");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../prerecibo/S" . $id . ".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Item ' . $id;
                $resultado['mensaje'] = 'Soporte publicado con éxito';
                $re = $db->update("prerecibo_soportes", Array("archivo" => "S" . $id . ".pdf"), Array("id" => filter_input(INPUT_POST, 'id')));
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Item ' . $id;
                $resultado['mensaje'] = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';
            }
        }
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if ($periodo != '') {
            $pe = $db->select("distinct periodo", "prerecibo_soportes", Array("id_inmueble" => "'" . $id_inmueble . "'"), "", Array("periodo" => "ASC"));
            if ($pe['suceed'] && count($pe['data']) > 0) {
                $periodos = $pe['data'];
            }
        }
        if ($id_inmueble != '' && $periodo != '') {
            $listado = $db->select("*", "prerecibo_soportes", Array("id_inmueble" =>$id_inmueble,"periodo"=>$periodo), "", Array("codigo_gasto" => "ASC"));
            if ($listado['suceed'] && count($listado['data']) > 0) {
                for ($index = 0; $index < count($listado['data']); $index++) {
                    if ($listado['data'][$index]['archivo'] != '') {
                        $filename = "../prerecibo/" . $listado['data'][$index]['archivo'];
                        $listado['data'][$index]['existe'] = file_exists($filename);

                    }
                }
                $detalle = $listado['data'];
            }
        }
        echo $twig->render('condominio/administrador.prerecibo.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "periodos" => $periodos,
            "id_inmueble" => $id_inmueble,
            "periodo" => $periodo,
            "detalle" => $detalle
                )
        );
        break; 
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="eliminar archivo soporte">
    case "eliminar-archivo":
        $filename = "../prerecibo/" . filter_input(INPUT_POST, 'filename');
        
        if (file_exists($filename)) {
            unlink(realpath($filename));
        }
        $db->update("prerecibo_soportes", Array("archivo" => ''), Array("id" => filter_input(INPUT_POST, 'id')));
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="general-guardar">
    case "general-guardar":
        $db = new db();
        $r = $db->insert("cartelerageneral", Array("detalle" => filter_input(INPUT_POST, "detalle")));


        if ($r["suceed"] == FALSE) {
            $r["titulo"] = "Ups!";
            $r["mensaje"] = $r['stats']['errno'] . "<br />" . $r['stats']['error'];
        } else {
            $r["titulo"] = "Muy Bien!";
            $r["mensaje"] = "Publicación registrada con éxito. Consulte la lista";
        }
        $ca = $db->select("*", "cartelerageneral", "", "", Array("id" => "DESC"));
        echo $twig->render('condominio/cartelera.general.html.twig', 
            array(
            "session" => $session,
            "cartelera" => $ca['data'],
            "resultado" => $r
                )
        );
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="general-eliminar">
    case "general-eliminar":

        $r = $db->delete("cartelerageneral", Array("id" => filter_input(INPUT_POST, "id")));

        if ($r["suceed"] == FALSE) {
            echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
        } else {
            echo filter_input(INPUT_POST, "id");
        }
        break;
    // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="cartelera-general-listar">
    case "general":
        $r = Array();
        $ca = $db->select("*", "cartelerageneral", "", "", Array("id" => "DESC"));
        if (!$ca['suceed']) {
            $r["titulo"] = "Ups!";
            $r["mensaje"] = $ca['stats']['errno']."<br />".$ca['stats']['error'];
        }
        echo $twig->render('condominio/cartelera.general.html.twig', 
            array(
            "session" => $session,
            "cartelera" => $ca['data'],
            "resultado" => $r
                )
        );
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="eliminar publicación cartelera">
    case "eliminar-publicacion":


        $r = $db->delete("cartelera", Array("id" => filter_input(INPUT_POST, "id")));

        if ($r["suceed"] == FALSE) {
            echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
        } else {
            echo filter_input(INPUT_POST, "id");
        }
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="guardar publicación cartelera">
    case "guardar-publicacion":
        $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
        $detalle = filter_input(INPUT_POST, "detalle");
        
        $db = new db();
        $r = $db->insert("cartelera", Array(
            "id_inmueble" => $id_inmueble,
            "detalle" => $detalle));


        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        $ca = $db->select("*", "cartelera", "", "", Array("id_inmueble" => "ASC,", "id" => "DESC"));
        
        if($r["suceed"]==FALSE){
            $r["titulo"] = "Ups!";
            $r["mensaje"] = $r['stats']['errno']."<br />".$r['stats']['error'];
        } else {
            $r["titulo"] = "Muy Bien!";
            $r["mensaje"]= "Publicación registrada con éxito. Consulte la lista";
        }
        
        echo $twig->render('condominio/administrador.cartelera.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "cartelera" => $ca['data'],
            "resultado" => $r
                )
        );
        
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="hidrocapital-listar">
    case "hidrocapital":
        
        $re = $db->dame_query("select i.id as codigo_inmueble, i.nombre_inmueble, h.* from inmueble i left join inmueble_hidrocapital h 
        on i.id = h.id_inmueble");
        $resultado = Array();
        if (!$re['suceed']) {
            $resultado["titulo"] = "Ups! Ocurrio un error...";
            $resultado["mensaje"] = "Durante el procesamiento de la información.\nNo se puede mostrar la lista";

        }
        echo $twig->render('condominio/hidrocapital.html.twig', 
            array(
            "session" => $session,
            "listado" => $re['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="hidrocapital-guardar">
    case "hidrocapital-guardar":
        $resultado = Array();
        if (isset($_POST['guardar'])) {
            unset($_POST['guardar']);


            $_POST['monto_pago'] = str_replace(".", "", filter_input(INPUT_POST, 'monto_pago'));
            $_POST['monto_pago'] = str_replace(",", ".", filter_input(INPUT_POST, 'monto_pago'));
            $_POST['fecha_pago'] = str_replace("/", "-", filter_input(INPUT_POST, 'fecha_pago'));
            $_POST['fecha_pago'] = date("Y-m-d 00:00:00 ", strtotime(filter_input(INPUT_POST, 'fecha_pago')));

            $re = $db->select("*", "inmueble_hidrocapital", Array("id_inmueble" => filter_input(INPUT_POST, 'id_inmueble')));

            if ($re['suceed'] == true) {
                if (count($re['data']) > 0) {
                    $re = $db->update("inmueble_hidrocapital", $_POST, Array("id_inmueble" => filter_input(INPUT_POST, 'id_inmueble')));
                    $resultado['mensaje'] = "Registro actualizado con éxito";
                } else {
                    $re = $db->insert("inmueble_hidrocapital", $_POST);
                    $resultado['mensaje'] = "Registro guardado con éxito";
                }
            }
            $resultado['suceed'] = $re['suceed'];
            if ($re['suceed'] == true) {
                $resultado['titulo'] = "Muy Bien!";
            } else {
                $resultado['titulo'] = "Ups!";
                $resultado['mensaje'] = $re['stats']['errno'] . "<br />" . $re['stats']['error'];
            }
        }
        $re = $db->dame_query("select i.id as codigo_inmueble, i.nombre_inmueble, h.* from inmueble i left join inmueble_hidrocapital h 
        on i.id = h.id_inmueble");




        echo $twig->render('condominio/hidrocapital.html.twig', 
            array(
            "session" => $session,
            "listado" => $re['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="acta junta">
    case "publicar-acta":
        $resultado  = array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/ACTA_JUNTA_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Acta de junta publicada con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el acta al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.acta.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="publicar rif">
    case "publicar-rif":
        $resultado = Array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/RIF_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Registro de información fiscal publicado con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el RIF al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.rif.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="publicar prerecibo">
    case "publicar-prerecibo":
        $resultado = Array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/PRERECIBO_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Registro del prerecibo publicado con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.prerecibog.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="publicar nomina">
    case "publicar-nomina":
        $resultado = Array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/NOMINA_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Registro de la nómina publicado con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.nomina.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="publicar fondo">
    case "publicar-fondo":
        $resultado = Array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/FONDO_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Registro del reporte de fondos publicado con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.fondo.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>
        
    // <editor-fold defaultstate="collapsed" desc="publicar fondo">
    case "publicar-ingresos":
        $resultado = Array();
        $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
        if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
            ini_set('max_execution_time', 20600);
            ini_set('max_input_time', 20600);
            ini_set('upload_max_filesize', '10M');
            ini_set('post_max_size', '10M');
            ini_set('memory_limit', '128M');
            $id_inmueble = filter_input(INPUT_POST, "id_inmueble");
            $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
            if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/INGRESOS_".$id_inmueble.".pdf")) {
                $resultado['suceed'] = TRUE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Registro del reporte publicado con éxito';
            } else {
                $resultado['suceed'] = FALSE;
                $resultado['titulo'] = 'Condominio '.$id_inmueble;
                $resultado['mensaje'] = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';
            }
        }
        echo $twig->render('condominio/administrador.ingresos.html.twig', 
            array(
            "session" => $session,
            "inmuebles" => $in['data'],
            "resultado" => $resultado
                )
        );
        break; // </editor-fold>
}