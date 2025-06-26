<?php
include_once "BaseDeDatos.php";

class Persona {
    private $documento;
    private $nombre;
    private $apellido;
    private $mensajeDeOperacion;
    private $estadoPersona;

    public function __construct() {
        $this->documento = "";
        $this->nombre = "";
        $this->apellido = "";
        $this->mensajeDeOperacion = "";
        $this->estadoPersona = false;
    }

    //metodos Getters
    public function getDocumento() {
        return $this->documento;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getMensajeDeOperacion() {
        return $this->mensajeDeOperacion;
    }

    public function getEstadoPersona(){
        return $this->estadoPersona;
    }

    //metodos Setter
    public function setDocumento($documento) {
        $this->documento = $documento;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setMensajeDeOperacion($mensaje) {
        $this->mensajeDeOperacion = $mensaje;
    }

    public function setEstadoPersona($estadoPersona){
        $this->estadoPersona = $estadoPersona;
    }

    public function __toString() {
        return "Nombre: " . $this->getNombre() . "\n" .
               "Apellido: " . $this->getApellido() . "\n" .
               "Documento: " . $this->getDocumento() . "\n";
    }

    public function insertar() {
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "INSERT INTO persona(documento, nombre, apellido) VALUES (
        '" . $this->getDocumento() . "',
        '" . $this->getNombre() . "',
        '" . $this->getApellido() . "')";
        
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $respuesta = true;
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $respuesta;
    }

    public function buscar($documento) {
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "SELECT * FROM persona WHERE documento = '" . $documento . "'";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    $this->setDocumento($fila["documento"]);
                    $this->setNombre($fila["nombre"]);
                    $this->setApellido($fila["apellido"]);
                    $respuesta = true;
                }
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $respuesta;
    }

    public function modificar() {
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "UPDATE persona SET nombre = '" . $this->getNombre() . "', apellido = '" . $this->getApellido() . "' WHERE documento = '" . $this->getDocumento() . "'";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $respuesta = true;
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $respuesta;
    }

    public function eliminar() {
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "DELETE FROM persona WHERE documento = '" . $this->getDocumento() . "'";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                $respuesta = true;
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $respuesta;
    }

    public function listar($condicion = "") {
        $arreglo = [];
        $base = new BaseDeDatos();
        $consulta = "SELECT * FROM persona";
        if ($condicion != "") {
            $consulta .= " WHERE " . $condicion;
        }
        $consulta .= " ORDER BY apellido";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                while ($fila = $base->Registro()) {
                    $obj = new Persona();
                    $obj->setDocumento($fila["documento"]);
                    $obj->setNombre($fila["nombre"]);
                    $obj->setApellido($fila["apellido"]);
                    array_push($arreglo, $obj);
                }
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $arreglo;
    }
}
?>
