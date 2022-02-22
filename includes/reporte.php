<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reporte
 *
 * @author Edgar
 */
class reporte extends db implements crud {
    const tabla = "reporte";
    
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

    public function obtenerReportePorMenu($menu) {
        return $this->select("*", self::tabla, Array("menu"=>$menu));
    }
}