<?php
include_once('./includes/constants.php');
$db = new db();

$noticias = array();
$re = $db->select("*", "cartelerageneral");
if ($re["suceed"] == true) {
    if (count($re['data']) > 0) {
        foreach ($re['data'] as $item) {
            $noticias[]= $item["detalle"];
        }
        $result = json_encode($noticias);
        echo $result;
    }
}