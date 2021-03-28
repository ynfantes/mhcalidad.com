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
    
    const tabla = "reporte_publicado";
    
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
    
    public function listarReportePorLibroyEmpresa($empresa,$libro) {
        return $this->select("*", self::tabla, Array("empresa_id"=>$empresa,"reporte_id"=>$libro),null,Array("periodo"=>"desc"));
    }
}

?>
