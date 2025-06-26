<?php
include_once "BaseDeDatos.php";

class Empresa {
    private $idEmpresa;
    private $nombre;
    private $direccion;
    private $colViajesE;
    private $estadoE;
    private $mensajeDeOperacion;

    public function __construct() {
        $this->idEmpresa = 0;
        $this->nombre = "";
        $this->direccion = "";
        $this->colViajesE = [];
        $this->estadoE = true;
        $this->mensajeDeOperacion = false;
    }

    // Getters
    public function getIdEmpresa() {
        return $this->idEmpresa;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function getColViajesE() {
        return $this->colViajesE;
    }

    public function getMensajeDeOperacion() {
        return $this->mensajeDeOperacion;
    }

    public function getEstadoE(){
        return $this->estadoE;
    }

    // Setters
    public function setIdEmpresa($idEmpresa) {
        $this->idEmpresa = $idEmpresa;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }

    public function setColViajesE($colViajesE) {
        $this->colViajesE = $colViajesE;
    }

    public function setMensajeDeOperacion($mensaje) {
        $this->mensajeDeOperacion = $mensaje;
    }

    public function setEstadoE($estadoE){
        $this->estadoE = $estadoE;
    }

    public function __toString() {
        $cadenaViajes = "";
        foreach ($this->getColViajesE() as $viaje) {
            $cadenaViajes .= $viaje . "\n";
        }

        return "Id de la Empresa: " . $this->getIdEmpresa() . "\n" .
               "Nombre: " . $this->getNombre() . "\n" .
               "Dirección: " . $this->getDireccion() . "\n" .
               "Colección de Viajes:\n" . $cadenaViajes;
    }

    public function insertar() {
        $base = new BaseDeDatos();
        $respuesta = false;

        if ($this->getNombre() == "" || $this->getDireccion() == "") {
            $this->setMensajeDeOperacion("Nombre o Dirección no pueden estar vacíos");
        } else {
            $consulta = "INSERT INTO empresa (enombre, edireccion) VALUES ('" . $this->getNombre() . "', '" . $this->getDireccion() . "')";
            if ($base->Iniciar()) {
                $id = $base->devuelveIDInsercion($consulta);
                if ($id != null) {
                    $this->setIdEmpresa($id);
                    $respuesta = true;
                } else {
                    $this->setMensajeDeOperacion($base->getError());
                }
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        }

        return $respuesta;
    }

    public function buscar($idEmpresa) {
        $base = new BaseDeDatos();
        $respuesta = false;

        if (!is_numeric($idEmpresa) || intval($idEmpresa) <= 0) {
            $this->setMensajeDeOperacion("ID de empresa inválido");
            $respuesta =  false;
        }

        if ($base->Iniciar()) {
            $consulta = "SELECT * FROM empresa WHERE idempresa = " . intval($idEmpresa) . "AND estadoE = TRUE";
            if ($base->Ejecutar($consulta)) {
                $fila = $base->Registro();
                if ($fila != null) {
                    $this->setIdEmpresa($fila['idempresa']);
                    $this->setNombre($fila['enombre']);
                    $this->setDireccion($fila['edireccion']);
                    $respuesta = true;
                } else {
                    $this->setMensajeDeOperacion("No se encontró la empresa con ID " . $idEmpresa);
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

        if ($this->getIdEmpresa() <= 0) {
            $this->setMensajeDeOperacion("ID de empresa inválido para modificar");
        } elseif ($this->getNombre() == "" || $this->getDireccion() == "") {
            $this->setMensajeDeOperacion("Nombre o Dirección no pueden estar vacíos");
        } elseif ($base->Iniciar()) {
            $consulta = "UPDATE empresa SET enombre = '" . $this->getNombre() . "
                                        ', edireccion = '" . $this->getDireccion() . "
                                    ' WHERE idempresa = " . intval($this->getIdEmpresa());
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

        if ($this->getIdEmpresa() <= 0) {
            $this->setMensajeDeOperacion("ID de empresa inválido para eliminar");
        } elseif ($base->Iniciar()) {
            $consulta = "UPDATE empresa SET estadoE = FALSE WHERE idempresa = " . intval($this->getIdEmpresa());
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
        $consulta = "SELECT * FROM empresa WHERE estadoE = TRUE";
        if ($condicion != "") {
            $consulta .= " AND " . $condicion;
        }
        $consulta .= " ORDER BY enombre";

        if ($base->Iniciar()) {
            if ($base->Ejecutar($consulta)) {
                while ($fila = $base->Registro()) {
                    $objEmpresa = new Empresa();
                    $objEmpresa->setIdEmpresa($fila['idempresa']);
                    $objEmpresa->setNombre($fila['enombre']);
                    $objEmpresa->setDireccion($fila['edireccion']);
                    array_push($arreglo, $objEmpresa);
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