<?php
include_once '../../includes/constants.php';
$db = new db();
session_start();

if (!$_SESSION['cpanel']) {
    header("location:" . ROOT );
    die();
}

$session = $_SESSION;

// Función de comparación personalizada
function compareByNameReversed($a, $b) {
    // Invierte las cadenas antes de comparar
    $reversedA = strrev($a);
    $reversedB = strrev($b);
    return strcmp($reversedA, $reversedB);
}

if ($_SERVER['REQUEST_METHOD']==='DELETE') {
    
    $result = [];
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body);
    $file = realpath("../documentos/".$data->file);
    
    if (file_exists($file)) {
        $result['suceed'] = unlink($file);
    } else {
        $resutl['suceed'] = false;
    }
    echo json_encode($result);

} else {

    $accion = isset($_GET['accion']) ? filter_input(INPUT_GET, 'accion') : "cartelera";

    switch ($accion) {    
        
        case "listar-publicaciones":
            $request_body = file_get_contents('php://input');
            $data = json_decode($request_body);
            $pattern =  $data->prefix.$data->condo.'*.pdf';
            $result = [];
            $files = glob('../documentos/'.$pattern, GLOB_BRACE);
            foreach ($files as $file ) {
                $result[] = basename($file);
            }
            
            usort($result, 'compareByNameReversed');
            echo json_encode($result);
            break;

        case "cartelera":
        default :
            $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
            $ca = $db->select("*", "cartelera", "", "", Array("id_inmueble" => "ASC,", "id" => "DESC"));

            echo $twig->render('condominio/administrador.cartelera.html.twig', 
                array(
                'session'    => $session,
                'inmuebles'  => $in['data'],
                'cartelera'  => $ca['data']
                    )
            );
            break;
        
        case "prerecibo":
            
            $id_inmueble    = isset($_POST['id_inmueble']) ? $_POST['id_inmueble'] : '';
            $periodo        = isset($_POST['periodo']) ? $_POST['periodo'] : '';
            $periodos       = array();
            $detalle        = array();
            $in             = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
            
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
                'session'       => $session,
                'inmuebles'     => $in['data'],
                'periodos'      => $periodos,
                'id_inmueble'   => $id_inmueble,
                'periodo'       => $periodo,
                'detalle'       => $detalle
                    )
            );
            break; 
        
        case "eliminar-itemprerecibo":
            $r = $db->delete("prerecibo_soportes", Array("id" => filter_input(INPUT_POST, "id")));

            if ($r["suceed"] == FALSE) {
                echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
            } else {
                echo filter_input(INPUT_POST, "id");
            }
            break;
        
        case "periodos":
            $pe = $db->select("distinct periodo", "prerecibo_soportes", Array("id_inmueble" => $_POST['id']), "", Array("periodo" => "ASC"));
            if ($pe['suceed']) {
                if (count($pe['data']) > 0) {
                    echo json_encode($pe['data']);
                }
            }
            break; 
        
        case "publicar-soporte": 
            
            $id_inmueble    = isset($_POST['id_inmueble']) ? $_POST['id_inmueble'] : '';
            $periodo        = isset($_POST['periodo']) ? $_POST['periodo'] : '';
            $periodos       = array();
            $detalle        = array();


            if (isset($_FILES['archivo']) && isset($_POST['id'])) {
                
                ini_set('max_execution_time', 20600);
                ini_set('max_input_time', 20600);
                ini_set('upload_max_filesize', '10M');
                ini_set('post_max_size', '10M');
                ini_set('memory_limit', '128M');
                $id = filter_input(INPUT_POST, "id");
                $filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
                
                if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../prerecibo/S" . $id . ".pdf")) {
                    
                    $resultado['suceed']    = TRUE;
                    $resultado['titulo']    = 'Item ' . $id;
                    $resultado['mensaje']   = 'Soporte publicado con éxito';
                    $re = $db->update("prerecibo_soportes", Array("archivo" => "S" . $id . ".pdf"), Array("id" => filter_input(INPUT_POST, 'id')));
                
                } else {

                    $resultado['suceed']    = FALSE;
                    $resultado['titulo']    = 'Item ' . $id;
                    $resultado['mensaje']   = 'Ocurrio al subir el archivo al servidor. Inténtelo nuevamente';

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
                'session'       => $session,
                'inmuebles'     => $in['data'],
                'periodos'      => $periodos,
                'id_inmueble'   => $id_inmueble,
                'periodo'       => $periodo,
                'detalle'       => $detalle
                    )
            );
            break; 
        
        case "eliminar-archivo":
            $filename = "../prerecibo/" . filter_input(INPUT_POST, 'filename');
            
            if (file_exists($filename)) {
                unlink(realpath($filename));
            }
            $db->update("prerecibo_soportes", Array("archivo" => ''), Array("id" => filter_input(INPUT_POST, 'id')));
            break;     
        
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
                'session'    => $session,
                'cartelera'  => $ca['data'],
                'resultado'  => $r
                    )
            );
            break; 
        
        
        case "general-eliminar":

            $r = $db->delete("cartelerageneral", Array("id" => filter_input(INPUT_POST, "id")));

            if ($r["suceed"] == FALSE) {
                echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
            } else {
                echo filter_input(INPUT_POST, "id");
            }
            break;
        
        case "general":
            $r = Array();
            $ca = $db->select("*", "cartelerageneral", "", "", Array("id" => "DESC"));
            if (!$ca['suceed']) {
                $r["titulo"] = "Ups!";
                $r["mensaje"] = $ca['stats']['errno']."<br />".$ca['stats']['error'];
            }
            echo $twig->render('condominio/cartelera.general.html.twig', 
                array(
                'session'    => $session,
                'cartelera'  => $ca['data'],
                'resultado'  => $r
                    )
            );
            break; 
        
        
        case "eliminar-publicacion":


            $r = $db->delete("cartelera", Array("id" => filter_input(INPUT_POST, "id")));

            if ($r["suceed"] == FALSE) {
                echo $r['stats']['errno'] . "<br />" . $r['stats']['error'];
            } else {
                echo filter_input(INPUT_POST, "id");
            }
            break; 
            
        
        case "guardar-publicacion":
            $id_inmueble    = filter_input(INPUT_POST, "id_inmueble");
            $detalle        = filter_input(INPUT_POST, "detalle");
            
            $db = new db();
            $r = $db->insert("cartelera", Array(
                'id_inmueble'   => $id_inmueble,
                'detalle'       => $detalle));

            $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));
            $ca = $db->select("*", "cartelera", "", "", Array("id_inmueble" => "ASC,", "id" => "DESC"));
            
            if($r["suceed"]==FALSE){

                $r["titulo"]    = "Ups!";
                $r["mensaje"]   = $r['stats']['errno']."<br />".$r['stats']['error'];

            } else {
                
                $r["titulo"]    = "Muy Bien!";
                $r["mensaje"]   = "Publicación registrada con éxito. Consulte la lista";

            }
            
            echo $twig->render('condominio/administrador.cartelera.html.twig', 
                array(
                'session'    => $session,
                'inmuebles'  => $in['data'],
                'cartelera'  => $ca['data'],
                'resultado'  => $r
                    )
            );
            
            break; // </editor-fold>
        
        
        case "hidrocapital":
            
            $re = $db->dame_query("select i.id as codigo_inmueble, i.nombre_inmueble, h.* from inmueble i left join inmueble_hidrocapital h 
            on i.id = h.id_inmueble");
            $resultado = Array();
            
            if (!$re['suceed']) {

                $resultado["titulo"]    = "Ups! Ocurrio un error...";
                $resultado["mensaje"]   = "Durante el procesamiento de la información.\nNo se puede mostrar la lista";

            }

            echo $twig->render('condominio/hidrocapital.html.twig', 
                array(
                'session'    => $session,
                'listado'    => $re['data'],
                'resultado'  => $resultado
                    )
            );
            break; // </editor-fold>

        
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
                'session'    => $session,
                'listado'    => $re['data'],
                'resultado'  => $resultado
                    )
            );
            break; // </editor-fold>

        
        case (strpos($accion,'publicar-') !== false):
            $resultado  = array();
            $d_reporte  = array();
            $inmuebles  = array();
            $reportes   = new reporte();
            $menu       = str_replace('publicar-','',$accion);
            $reporte    = $reportes->obtenerReportePorMenu("'".$menu."'");
            
            if ($reporte['suceed'] && count($reporte['data'])>0) {
                
                $d_reporte = $reporte['data'][0];
                $in = $db->select("*", "inmueble", "", "", Array("nombre_inmueble" => "ASC"));

                if ($in['suceed'] && count($in['data'])>0) {
                    $inmuebles = $in['data'];
                }
            }
            
            
            if (isset($_FILES['archivo']) && isset($_POST['id_inmueble'])) {
                
                $id_inmueble    = filter_input(INPUT_POST, "id_inmueble");
                $in             = $db->select("*", "inmueble",Array("id" => $id_inmueble));
                
                $nombre_inmueble = ($in['suceed'] && count($in['data'])>0) ? $in['data'][0]['nombre_inmueble'] : '';

                $prefix         = $d_reporte["prefix"];
                $descripcion    = strtoupper($d_reporte["descripcion"]);
                
                //$filename = basename(html_entity_decode(strtolower($_FILES['archivo']['name']), ENT_QUOTES, 'UTF-8'));
                
                $filename = $prefix.$id_inmueble;

                if (isset($_POST['mes'])) $filename.= '_'.filter_input(INPUT_POST,'mes'); 
                if (isset($_POST['year'])) $filename.= '-'.filter_input(INPUT_POST,'year');
                
                if (@move_uploaded_file($_FILES['archivo']['tmp_name'], "../documentos/$filename.pdf")) {
                    
                    $resultado['suceed']    = TRUE;
                    $resultado['titulo']    = 'Publicado con éxito! 👍';
                    $resultado['mensaje']   = $descripcion.' '.$nombre_inmueble;
                    
                } else {
                    
                    $resultado['suceed']    = FALSE;
                    $resultado['titulo']    = 'Error! No se ha podido publicar ☹';
                    $resultado['mensaje']   = $descripcion.' en '.$nombre_inmueble.'. Inténtelo nuevamente';

                }
            }
            echo $twig->render('condominio/administrador.publicar.html.twig', 
                array(
                'session'   => $session,
                'inmuebles' => $inmuebles,
                'resultado' => $resultado,
                'reporte'   => $d_reporte
                    )
            );
            break;
        

    }
}
