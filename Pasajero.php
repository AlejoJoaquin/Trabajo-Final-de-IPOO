<?php
include_once "BaseDeDatos.php";
include_once "Persona.php";

class Pasajero extends Persona {
    private $telefono;
    private $colViajes;
    private $mensajeDeOperacion;
    private $estadoPasajero;

    public function __construct() {
        parent::__construct();
        $this->telefono = "";
        $this->colViajes = [];
        $this->mensajeDeOperacion = "";
        $this->estadoPasajero = true;
    }

    // Getters
    public function getTelefono() {
        return $this->telefono;
    }

    public function getColViajes() {
        return $this->colViajes;
    }

    public function getMensajeDeOperacion() {
        return $this->mensajeDeOperacion;
    }

    public function getEstadoPasajero(){
        return $this->estadoPasajero;
    }

    // Setters
    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setColViajes($colViajes) {
        $this->colViajes = $colViajes;
    }

    public function setMensajeDeOperacion($mensajeDeOperacion) {
        $this->mensajeDeOperacion = $mensajeDeOperacion;
    }

    public function setEstadoPasajero($estadoPasajero){
        $this->estadoPasajero = $estadoPasajero;
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

        if (!parent::buscar($this->getDocumento())) {
            parent::insertar();
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
                     WHERE per.documento = '$documento' AND per.estadoPersona = TRUE AND p.estadoPasajero = TRUE";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                if ($fila = $base->Registro()) {
                    parent::setDocumento($fila["documento"]);
                    parent::setNombre($fila["nombre"]);
                    parent::setApellido($fila["apellido"]);
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
            $consultaPasajero = "UPDATE pasajero SET estadoPasajero = FALSE WHERE pdocumento = '$doc'";
            if ($base->Ejecutar($consultaPasajero)) {
                $consultaPersona = "UPDATE persona SET estadoPersona = FALSE WHERE documento = '$doc'";
                $this->setEstadoPasajero(false);
                parent::setEstadoPersona(false);
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
                     INNER JOIN persona per ON p.pdocumento = per.documento 
                     WHERE per.estadoPersona = TRUE AND p.estadoPasajero = TRUE";

        if ($condicion != "") {
            $consulta .= " AND " . $condicion;
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

    public function cargarViajes() {
        $documento = $this->getDocumento();
        $viaja = new Viaja();
        $viajes = $viaja->obtenerViajesPorPasajero($documento);
        $this->setColViajes($viajes);
    }
}
?>
 