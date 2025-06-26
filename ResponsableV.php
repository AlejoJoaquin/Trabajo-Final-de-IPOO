<?php
include_once "BaseDeDatos.php";
include_once "Persona.php";

class ResponsableV extends Persona {
    private $numeroEmpleado;
    private $numeroLicencia;
    private $colViajesR;

    public function __construct() {
        parent::__construct();
        $this->numeroEmpleado = "";
        $this->numeroLicencia = "";
        $this->colViajesR = [];
    }

    public function getNumeroEmpleado() {
        return $this->numeroEmpleado;
    }

    public function getNumeroLicencia() {
        return $this->numeroLicencia;
    }

    public function getColViajesR() {
        return $this->colViajesR;
    }

    public function setNumeroEmpleado($numeroEmpleado) {
        $this->numeroEmpleado = $numeroEmpleado;
    }

    public function setNumeroLicencia($numeroLicencia) {
        $this->numeroLicencia = $numeroLicencia;
    }

    public function setColViajesR($colViajesR) {
        $this->colViajesR = $colViajesR;
    }

        public function __toString() {
            return parent::__toString() .
                "Número de Empleado: " . $this->getNumeroEmpleado() . "\n" .
                "Número de Licencia: " . $this->getNumeroLicencia() . "\n";
        }

    public function insertar() {
        $base = new BaseDeDatos();
        $respuesta = false;
        $this->setMensajeDeOperacion('');

        $persona = new Persona();
        $persona->setDocumento($this->getDocumento());

        if (!$persona->buscar($this->getDocumento())) {
            $persona->setNombre($this->getNombre());
            $persona->setApellido($this->getApellido());
            if (!$persona->insertar()) {
                $this->setMensajeDeOperacion($persona->getMensajeDeOperacion());
            } else {
                if ($base->Iniciar()) {
                    $consulta = "INSERT INTO responsable (rnumeroempleado, rnumerolicencia, rdocumento) VALUES ('" .
                        $this->getNumeroEmpleado() . "', '" .
                        $this->getNumeroLicencia() . "', '" .
                        $this->getDocumento() . "')";
                    if ($base->Ejecutar($consulta)) {
                        $respuesta = true;
                    } else {
                        $this->setMensajeDeOperacion($base->getError());
                    }
                } else {
                    $this->setMensajeDeOperacion($base->getError());
                }
            }
        } else {
            if ($base->Iniciar()) {
                $consulta = "INSERT INTO responsable (rnumeroempleado, rnumerolicencia, rdocumento) VALUES ('" .
                    $this->getNumeroEmpleado() . "', '" .
                    $this->getNumeroLicencia() . "', '" .
                    $this->getDocumento() . "')";
                if ($base->Ejecutar($consulta)) {
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


    public function buscar($docEmpleado) {
        $base = new BaseDeDatos();
        $respuesta = false;

        if ($base->Iniciar()) {
            $consulta = "SELECT * 
                FROM responsable r INNER JOIN persona p ON r.rdocumento = p.documento 
                WHERE r.rdocumento = '" . $docEmpleado . "'";

                if($base->Ejecutar($consulta)){
                    $fila = $base->Registro();

                    if ($fila) {
                        $this->setNumeroEmpleado($fila["rnumeroempleado"]);
                        $this->setNumeroLicencia($fila["rnumerolicencia"]);
                        $this->setDocumento($fila["documento"]);
                        $this->setNombre($fila["nombre"]);
                        $this->setApellido($fila["apellido"]);
                        $respuesta = true;
                    }
                }else{
                    $this->setMensajeDeOperacion($base->getError());
                }
            }else{
                $this->setMensajeDeOperacion($base->getError());
            }
        return $respuesta;
    }


    public function modificar() {
            $base = new BaseDeDatos();
            $respuesta = false;

            if ($base->Iniciar()) {
                $consulta = "UPDATE responsable SET rnumerolicencia = '" .
                    $this->getNumeroLicencia() . "' WHERE rnumeroempleado = '" .
                    $this->getNumeroEmpleado() . "'";

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

            if ($base->Iniciar()) {
                $consulta = "DELETE FROM responsable WHERE rdocumento = '" . $this->getDocumento() . "'";

                if ($base->Ejecutar($consulta)) {
                    $respuesta = true;
                } else {
                    $this->setMensajeDeOperacion("No se pudo eliminar el responsable. Puede estar relacionado a otro dato.");
                }
            } else {
                $this->setMensajeDeOperacion("Error al conectar con la base de datos.");
            }

            return $respuesta;
    }

    public function listar($condicion = "") {
            $arreglo = [];
            $base = new BaseDeDatos();
            $consulta = "SELECT * FROM responsable r INNER JOIN persona p ON r.rdocumento = p.documento";

            if ($condicion != "") {
                $consulta .= " WHERE " . $condicion;
            }

            $consulta .= " ORDER BY apellido";

            if ($base->Iniciar()) {
                if ($base->Ejecutar($consulta)) {
                    while ($fila = $base->Registro()) {
                        $objResponsable = new ResponsableV();
                        $objResponsable->setNumeroEmpleado($fila["rnumeroempleado"]);
                        $objResponsable->setNumeroLicencia($fila["rnumerolicencia"]);
                        $objResponsable->setDocumento($fila["documento"]);
                        $objResponsable->setNombre($fila["nombre"]);
                        $objResponsable->setApellido($fila["apellido"]);
                        array_push($arreglo, $objResponsable);
                    }
                } else {
                    $this->setMensajeDeOperacion($base->getError());
                }
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }

            return $arreglo;
    }

    /**
     * Esta funcion recibe un objViaje por parametro y lo mete a la coleccion de viajes
     * el objViaje ya tiene todos los datos cargados porque se hace despues de insertar un viaje
     * @param object $objViaje
     */
    public function cargarViajesResponsable($objViaje){
        $coleccionViajesResponsable = $this->getColViajesR();
        array_push ($coleccionViajesResponsable, $objViaje);
        $this->setColViajesR($coleccionViajesResponsable);
    }
}