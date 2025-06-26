<?php
include_once "BaseDeDatos.php";
include_once "clasePersona.php";

class Pasajero extends Persona {
    private $telefono;
    private $colViajes;
    private $mensajeDeOperacion;

    public function __construct() {
        parent::__construct();
        $this->telefono = "";
        $this->colViajes = [];
        $this->mensajeDeOperacion = "";
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getColViajes() {
        return $this->colViajes;
    }

    public function getMensajeDeOperacion() {
        return $this->mensajeDeOperacion;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setColViajes($colViajes) {
        $this->colViajes = $colViajes;
    }

    public function setMensajeDeOperacion($mensajeDeOperacion) {
        $this->mensajeDeOperacion = $mensajeDeOperacion;
    }

    public function __toString() {
        $cadenaViajes = "";
        foreach ($this->getColViajes() as $viaje) {
            $cadenaViajes .= $viaje . "\n";
        }

        $cadena = parent::__toString();
        $cadena .= "Telefono: " . $this->getTelefono() . "\n";
        $cadena .= "Colección de Viajes: \n" . $cadenaViajes;
        return $cadena;
    }

    public function insertar() {
        $base = new BaseDeDatos();
        $respuesta = false;

        $persona = new Persona();
        $persona->setDocumento($this->getDocumento());

        if (!$persona->buscar($this->getDocumento())) {
            $persona->setNombre($this->getNombre());
            $persona->setApellido($this->getApellido());
            $persona->insertar();
        }

        $consulta = "INSERT INTO pasajero(pdocumento, ptelefono) 
                    VALUES ('" . $this->getDocumento() . "', '" . $this->getTelefono() . "')";

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

        $consulta = "SELECT per.documento, per.nombre, per.apellido, p.ptelefono 
                    FROM persona per 
                    INNER JOIN pasajero p ON per.documento = p.pdocumento 
                    WHERE per.documento = '" . $documento . "'";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    $this->setDocumento($fila["documento"]);
                    $this->setNombre($fila["nombre"]);
                    $this->setApellido($fila["apellido"]);
                    $this->setTelefono($fila["ptelefono"]);
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

        $consulta = "UPDATE pasajero 
                    SET ptelefono = '" . $this->getTelefono() . "' 
                    WHERE pdocumento = '" . $this->getDocumento() . "'";

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
        $doc = $this->getDocumento();

        if ($base->Iniciar()) {
            // Se borra pasajero primero por temas de politicas de restriccion SQL
            $consultaPasajero = "DELETE FROM pasajero WHERE pdocumento = '" . $doc . "'";
            if ($base->Ejecutar($consultaPasajero)) {
                //Una vez borado el pasajero, se borra a la persona que tenia el mismo documento
                $consultaPersona = "DELETE FROM persona WHERE documento = '" . $doc . "'";
                if ($base->Ejecutar($consultaPersona)) {
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

        return $respuesta;
    }


    public function listar($condicion = "") {
        $arreglo = [];
        $base = new BaseDeDatos();

        $consulta = "SELECT p.pdocumento, p.ptelefono, per.nombre, per.apellido 
                    FROM pasajero p 
                    INNER JOIN persona per ON p.pdocumento = per.documento";

        if ($condicion != "") {
            $consulta .= " WHERE " . $condicion;
        }

        $consulta .= " ORDER BY per.apellido";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                while ($fila = $base->Registro()) {
                    $obj = new Pasajero();
                    $obj->setDocumento($fila["pdocumento"]);
                    $obj->setNombre($fila["nombre"]);
                    $obj->setApellido($fila["apellido"]);
                    $obj->setTelefono($fila["ptelefono"]);
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


    /** En esta funcion llama a la funcion de viajes que me 
     * retorna un array con todos los viajes de la PK que se pasa por parametro 
     * 
    */
    public function cargarViajes() {
        $documento = $this->getDocumento();

        $viaja = new Viaja();

        $viajes = $viaja->obtenerViajesPorPasajero($documento);

        $this->setColViajes($viajes);
    }
}
?>
