<?php
include_once "BaseDeDatos.php";
include_once "Viaje.php";
include_once "Pasajero.php";

class Viaja {
    private $objViaje;          // objeto Viaje
    private $objPasajero;       // objeto Pasajero
    private $mensajeDeOperacion;
    private $estadoViajePasajero;

    public function __construct() {
        $this->objViaje = null;
        $this->objPasajero = null;
        $this->mensajeDeOperacion = "";
        $this->estadoViajePasajer = false;
    }

    // Getters
    public function getObjViaje() {
        return $this->objViaje;
    }

    public function getObjPasajero() {
        return $this->objPasajero;
    }

    public function getMensajeDeOperacion() {
        return $this->mensajeDeOperacion;
    }

    public function getEstadoViajePasajero(){
        return $this->estadoViajePasajero;
    }

    // Setters
    public function setObjViaje($objViaje) {
        $this->objViaje = $objViaje;
    }

    public function setObjPasajero($objPasajero) {
        $this->objPasajero = $objPasajero;
    }

    public function setMensajeDeOperacion($mensaje) {
        $this->mensajeDeOperacion = $mensaje;
    }

    public function setEstadoViajePasajero($estadoViajePasajero){
        $this->estadoViajePasajero = $estadoViajePasajero;
    }

    // toString
    public function __toString() {
        $viajeStr = $this->objViaje ? $this->objViaje->__toString() : "Sin viaje";
        $pasajeroStr = $this->objPasajero ? $this->objPasajero->__toString() : "Sin pasajero";
        return "Viaje: " . $viajeStr . " | Pasajero: " . $pasajeroStr . "\n";
    }

    public function insertar() {
    $base = new BaseDeDatos();
    $resp = false;
    $this->mensajeDeOperacion = "";

    if ($this->objViaje && $this->objPasajero) {
        $sql = "INSERT INTO viaja (idviaje, pdocumento) VALUES (
            '" . $this->objViaje->getIdViaje() . "',
            '" . $this->objPasajero->getDocumento() . "')";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->mensajeDeOperacion = $base->getError();
            }
        } else {
            $this->mensajeDeOperacion = $base->getError();
        }
    } else {
        $this->mensajeDeOperacion = "Viaje o Pasajero no inicializados.";
    }

    return $resp;
}

    public function eliminar() {
    $base = new BaseDeDatos();
    $resp = false;
    $this->mensajeDeOperacion = "";

    if ($this->objViaje && $this->objPasajero) {
        $sql = "DELETE FROM viaja WHERE 
                idviaje = '" . $this->objViaje->getIdViaje() . "' AND 
                pdocumento = '" . $this->objPasajero->getDocumento() . "'";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
            }else{
                $this->mensajeDeOperacion = $base->getError();
            }
        }else{
            $this->mensajeDeOperacion = $base->getError();
        }
    }else{
        $this->mensajeDeOperacion = "Viaje o Pasajero no inicializados.";
    }
    return $resp;
    }

    public function modificar($nuevoIdViaje, $nuevoDocumentoPasajero) {
        $base = new BaseDeDatos();
        $resp = false;

        $sql = "UPDATE viaja SET 
                    idviaje = " . $nuevoIdViaje . ", 
                    pdocumento = '" . $nuevoDocumentoPasajero . "' 
                WHERE idviaje = " . $this->objViaje->getIdViaje() . 
                " AND pdocumento = '" . $this->objPasajero->getDocumento() . "'";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                $resp = true;
                // actualizar los objetos (asumí que tenés setters para eso)
                $nuevoViaje = new Viaje();
                $nuevoViaje->buscar($nuevoIdViaje);
                $this->objViaje = $nuevoViaje;

                $nuevoPasajero = new Pasajero();
                $nuevoPasajero->buscar($nuevoDocumentoPasajero);
                $this->objPasajero = $nuevoPasajero;
            } else {
                $this->mensajeDeOperacion = $base->getError();
            }
        } else {
            $this->mensajeDeOperacion = $base->getError();
        }

        return $resp;
    }

    public function buscar($idViaje, $documentoPasajero) {
        $base = new BaseDeDatos();
        $resp = false;
        $this->mensajeDeOperacion = "";

        $sql = "SELECT * FROM viaja WHERE idviaje = '" . $idViaje . "' AND pdocumento = '" . $documentoPasajero . "'";

        if($base->Iniciar()){
            if ($base->Ejecutar($sql)) {
                if ($fila = $base->Registro()) {
                    $viaje = new Viaje();
                    $pasajero = new Pasajero();

                    if ($viaje->buscar($fila["idviaje"]) && $pasajero->buscar($fila["pdocumento"])) {
                        $this->objViaje = $viaje;
                        $this->objPasajero = $pasajero;
                        $resp = true;
                    } else {
                        $this->mensajeDeOperacion = "No se encontró viaje o pasajero relacionado.";
                    }
                }
            } else {
                $this->mensajeDeOperacion = $base->getError();
            }
        }else{
            $this->mensajeDeOperacion = $base->getError();
        }
        return $resp;
    }

    public function listar($condicion = "") {
        $arreglo = [];
        $base = new BaseDeDatos();
        $this->mensajeDeOperacion = "";

        $sql = "SELECT * FROM viaja";
        if ($condicion != "") {
            $sql .= " WHERE " . $condicion;
        }

        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                while ($fila = $base->Registro()) {
                    $viaje = new Viaje();
                    $pasajero = new Pasajero();

                    if ($viaje->buscar($fila["idviaje"]) && $pasajero->buscar($fila["pdocumento"])) {
                        $viaja = new Viaja();
                        $viaja->setObjViaje($viaje);
                        $viaja->setObjPasajero($pasajero);
                        array_push($arreglo, $viaja);
                    }
                }
            } else {
                $this->mensajeDeOperacion = $base->getError();
            }
        } else {
            $this->mensajeDeOperacion = $base->getError();
        }

        return $arreglo;
    }

    /**
     * Esta funcion por medio de una consulta me devuelve un array con todos los viajes en los que participo el pasajero
     * @param string $documentoPasajero
     * @return array[]
     */
    public function obtenerViajesPorPasajero($documentoPasajero) {
        $bd = new BaseDeDatos();
        $viajes = [];

        if ($bd->Iniciar()) {
            $sql = "SELECT idviaje FROM viaja WHERE pdocumento = '$documentoPasajero'";
            if ($bd->Ejecutar($sql)) {
                while ($fila = $bd->Registro()) {
                    $viaje = new Viaje();
                    if ($viaje->buscar($fila['idviaje'])) {
                        array_push($viajes, $viaje);
                    }
                }
            }
        }

        return $viajes;
    }

    /**
     * Esta funcion retorna de una consulta un array con todos los documentos de los pasajeros que estuvieron
     * en un viaje con id $idViaje
     * @param int $idViaje
     * @return array[]
     */
    public function obtenerPasajerosPorViaje($idViaje) {
        $bd = new BaseDeDatos();
        $pasajeros = [];

        if ($bd->Iniciar()) {
            $sql = "SELECT pdocumento FROM viaja WHERE idviaje = '$idViaje'";
            if ($bd->Ejecutar($sql)) {
                while ($fila = $bd->Registro()) {
                    $pasajero = new Pasajero();
                    if ($pasajero->buscar($fila['pdocumento'])) {
                        array_push($pasajeros, $pasajero);
                    }
                }
            }
        }

        return $pasajeros;
    }
}
?>
