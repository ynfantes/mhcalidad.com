<?php
/*
 * Clase para hacer mantenimiento a la tabla productos
 * 
 * @author Edgar Messia
 */
class registro extends db implements crud {

    private $tabla;
    
    public function tabla($tabla){
        
        $this->tabla = $tabla;
    }

    public function actualizar($id, $data) {
        return $this->update($this->tabla, $data, Array("id"=>$id));
    }
    public function borrar($id) {
        return $this->delete($this->tabla, Array("id"=>$id));
    }

    public function borrarTodo() {
        return $this->delete($this->tabla);
    }

    public function insertar($data) {
        return $this->insert($this->tabla, $data);
    }

    public function listar() {
        return $this->select("*", $this->tabla, "", "");
    }

    public function ver($id) {
        return $this->select("*", $this->tabla, Array("id"=>$id));
    }

    public function listarReportes($criterio, $order) { 
        return $this->select('*', $this->tabla, $criterio, null, $order);
    }
}
?>