<?php
//defined('_JEXEC') or die("Acceso Restringido");
include_once("../includes/db.php");
$db = new db();
propietario::esPropietarioLogueado();
//$log = new log();
//$log->registrar($usuario_db['data'][0]['cedula'], 5, 'consulta de estado de cuenta');
$inmuebles = $db->dame_query("select distinct inmueble.*
    from inmueble 
    inner join propiedades on propiedades.id_inmueble = inmueble.id
    inner join propietarios on propiedades.cedula = propietarios.cedula
where propietarios.cedula =" . $_SESSION["cedula"]);
?>
<?php if ($inmuebles['suceed'] && count($inmuebles['data']) > 0) {
    foreach ($inmuebles['data'] as $inmueble) {
        ?>

        <h1 class="contentheading clearfix" style="width: 100%"><?php echo $inmueble['id'] . " - " . $inmueble['nombre_inmueble'] ?></h1>
        <div class="article-tools clearfix"></div>
        <ul>
            <?php
            $propiedades = $db->dame_query("select * from propiedades where cedula =" . $_SESSION['cedula']);
            if ($propiedades['suceed'] && count($propiedades['data']) > 0) {
                foreach ($propiedades['data'] as $propiedad) {
                    ?>
                    <li><label>Carta(s) de Residencia Apartamento: </label><?php echo $propiedad['apto']; ?></li>
                    <li>&nbsp;</li>
                    <?php }
                    // scanning directories for image files
                    if (is_dir("documentos")){
                        $i=0;
                        $root_dir= "documentos/".$inmueble['id'].$propiedad['apto']."*.pdf";
                        if (glob($root_dir)) {
                   
                        foreach (glob($root_dir) as $nombre_archivo) {
                            $i++;
                          echo "<li><label>$i.- <a href=\"$nombre_archivo\" target=\"_blank\">". basename($nombre_archivo) ."</a></li>";
                          }
                        }
                        if($i==0) {
                            echo "<h2>No tiene cartas de residencia publicadas</h2>";
                            ?>
                    <p>(para solicitarla favor comunicarse por los Telefonos 0212-5731047 / 0426-9860909 / 0416-9398347)</p>
                            <?php
                        }
                    } else {
                        echo "<h2>No se puede acceder al directorio de las Cartas de Residencia.</h2>";
                    }
                }
            }
            ?>
        </ul>

        <?php
}
?>