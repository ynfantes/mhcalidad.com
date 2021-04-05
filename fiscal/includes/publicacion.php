<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of publicacion
 *
 * @author Edgar
 */
class publicacion extends db implements crud {
    
    const tabla = "publicaciones";
    
    public function actualizar($id, $data) {
        return $this->update(self::tabla, $data, Array("id"=>$id));
    }

    public function borrar($id) {
        return $this->delete(self::tabla, Array("id"=>$id));
    }

    public function borrarTodo() {
        return $this->delete(self::tabla);
    }

    public function insertar($data) {
         return $this->insert(self::tabla, $data);
    }

    public function listar() {
         return $this->select("*", self::tabla, "", "", "id");
    }

    public function ver($id) {
        return $this->select("*", self::tabla, Array("id"=>$id));
    }
    
    public function reporteYaRestrido($data) {
        $r = $this->select("id",self::tabla,Array("empresa_id"=>$data['empresa_id'],"reporte_id"=>$data['reporte_id'],"periodo"=>$data['periodo']));
        if ($r['suceed']) {
            if (count($r['data'])>0) {
                $this->update(self::tabla, $data,Array("id"=>$r['data'][0]['id']));
            }
            return count($r['data'])>0;
        } else {
            return false;
        }
    }
    public function listarReportes($id_empresa, $reporte) {
        $query = 'select publicaciones.*, publicaciones.id pid,
            publicaciones.quincena pquincena, reporte.* 
            from publicaciones join reporte 
            on publicaciones.reporte = reporte.id
            where id_empresa='.$id_empresa.' and reporte='.$reporte.' order by 
            publicaciones.periodo DESC, publicaciones.quincena ASC';
            ;
        return $this->dame_query($query);

    }

    public function listarReportePorLibroyEmpresa($empresa,$libro) {
        return $this->select("*", self::tabla, 
        Array(
            "id_empresa"    => $empresa,
            "reporte"       => $libro
        ),null,Array("periodo"=>"desc"));
    }
}

?>
