<?php

/*
 * Clase para hacer mantenimiento a la tabla productos
 * 
 * @author Edgar Messia
 */
class productos extends db implements crud {
    
    const tabla = "productos";
    
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
    
    public function obtenerInformacionCompletaProducto($id) {
        $query = "select p.*, c.id categoria_id, c.nombre from productos p join categorias c 
            on p.categoria_id = c.id where p.id = ".$id;
        return $this->dame_query($query);
    }
    public function obtenerListaProductosMasVistosPorCategoria($id_categoria,$numero_pagina, $item_por_pagina,$id_producto) {
        $hasta = $numero_pagina * $item_por_pagina;
        $desde = $hasta - $item_por_pagina;
        $query = "select * from productos where categoria_id=".$id_categoria.
                " and id <>".$id_producto ." order by visto DESC limit ".$desde.",".$hasta;
        return $this->dame_query($query);
    }
    
    public function obtenerListaDeProductosPorCategoria($id_categoria) {
        return $this->select("*", self::tabla, Array("categoria_id"=>$id_categoria));
        
    }
    
    public function obtenerProductoMasVistoPorCategoris($id_categoria) {
        return $this->select("*", self::tabla, Array("publicar"=>'s',"categoria_id"=>$id_categoria),'',Array("visto"=>"DESC"));
    }
    
    public function acortarUrlDelProducto($url) {
        $goo = new GoogleUrlApi('AIzaSyDxLyTpE-oh_Pz3vSHVsrOUdPpIGvXitEA','https://www.googleapis.com/urlshortener/v1/url');
        return $goo->shorten($url);
    }
}
?>
