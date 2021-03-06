<?php

/**
 * Clase que mantiene la tabla empresa
 *
 * @author Edgar
 * @since 1.0
 */
class empresa extends db implements crud {
    
    const tabla = "empresa";
    
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
        return $this->select("*", self::tabla);
    }

    public function ver($id) {
        return $this->select("*", self::tabla, Array("id"=>$id));
    }
    
    public function listarEmpresasActivas() {
        return $this->select("*", self::tabla, Array("inactiva"=>0));
    }
    
    public function login($clave) {
        return $clave;

    }
}