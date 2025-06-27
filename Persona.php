<?php
//incluimos la base de datos
include_once "BaseDeDatos.php";

class Persona {
    //declaramos los atributos de la clase
    private $documento;
    private $nombre;
    private $apellido;
    private $mensajeDeOperacion;
    private $estadoPersona;//controlamos si la persona esta activa (true) o fue eliminada logicamente (false)

    public function __construct() {
        $this->documento = "";
        $this->nombre = "";
        $this->apellido = "";
        $this->mensajeDeOperacion = "";
        $this->estadoPersona = true;
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

    //implementamos el metodo de insertar que inserta una nueva persona
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

    //implementamos el metodo buscar que busca a una persona en la base de datos
    public function buscar($documento) {
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "SELECT * FROM persona WHERE documento = '" . $documento . "' AND estadoPersona = TRUE";
        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    //si la encuentra, seteamos los valores del objeto
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

    //implementamos el metodo modificar de una persona existente
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

    //implementamos el metodo de eliminar, que elimina a una persona(no la borra de la base de datos, sino que la marca como inactiva)
    public function eliminar() {
        $base = new BaseDeDatos();
        $respuesta = false;
        $doc = $this->getDocumento();

        if ($base->Iniciar()) {
            // Borrado logico de persona
            $consultaPersona = "UPDATE persona SET estadoPersona = FALSE WHERE documento = '" . $doc . "'";
            $this->setEstadoPersona(false);
            // Borrado logico de los pasajeros con mismo doc
            $consultaPasajero = "UPDATE pasajero SET estadoPasajero = FALSE WHERE pdocumento = '" . $doc . "'";

            // Borrado logico de los responsable con mismo doc
            $consultaResponsable = "UPDATE responsable SET estadoResponsable = FALSE WHERE rdocumento = '" . $doc . "'";

            if ($base->Ejecutar($consultaPersona)) {
                if ($base->Ejecutar($consultaPasajero)) {
                    if ($base->Ejecutar($consultaResponsable)) {
                        $respuesta = true;
                    } else {
                        $this->setMensajeDeOperacion($base->getError());
                    }
                } else {
                    $this->setMensajeDeOperacion($base->getError());
                }
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }

        return $respuesta;
    }

    //listara a todas las persona activas, opcionalmente con una condicion
    public function listar($condicion = "") {
        $arreglo = [];
        $base = new BaseDeDatos();
        $consulta = "SELECT * FROM persona WHERE estadoPersona = TRUE";
        //aca si se pasa una condicion adicional, se le agregara a la consulta
        if ($condicion != "") {
            $consulta .= " AND " . $condicion;
        }
        //ordenamos por apellido
        $consulta .= " ORDER BY apellido";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                //por cada fila encontrada, creamos el objeto persona y lo agregamos al arreglo
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
