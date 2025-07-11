<?php
include_once "BaseDeDatos.php";
include_once "Empresa.php";
include_once "ResponsableV.php";
include_once "Pasajero.php";

class Viaje {
    private $idViaje;
    private $vImporte;
    private $vCantMaxPasajeros;
    private $objEmpresa;
    private $vDestino;
    private $objResponsable;
    private $colPasajeros;
    private $mensajeDeOperacion;
    private $estadoViaje;

    public function __construct(){
        $this->idViaje = 0;
        $this->vImporte = 0;
        $this->vCantMaxPasajeros = 0;
        $this->objEmpresa = null;
        $this->vDestino = "";
        $this->objResponsable = null;
        $this->colPasajeros = [];
        $this->mensajeDeOperacion = "";
        $this->estadoViaje = false;
    }

    // Getters
    public function getIdViaje(){
        return $this->idViaje;
    }

    public function getVimporte(){
        return $this->vImporte;
    }

    public function getVcantMaxPasajeros(){
        return $this->vCantMaxPasajeros;
    }

    public function getObjResponsable(){
        return $this->objResponsable;
    }

    public function getObjEmpresa(){
        return $this->objEmpresa;
    }

    public function getVDestino(){
        return $this->vDestino;
    }

    public function getColPasajeros(){
        return $this->colPasajeros;
    }

    public function getMensajeDeOperacion(){
        return $this->mensajeDeOperacion;
    }

    public function getEstadoViaje(){
        return $this->estadoViaje;
    }

    // Setters
    public function setIdViaje($idViaje){
        $this->idViaje = $idViaje;
    }

    public function setVimporte($vImporte){
        $this->vImporte = $vImporte;
    }

    public function setVcantMaxPasajeros($vCantMaxPasajeros){
        $this->vCantMaxPasajeros = $vCantMaxPasajeros;
    }

    public function setObjResponsable($objResponsable){
        $this->objResponsable = $objResponsable;
    }

    public function setObjEmpresa($objEmpresa){
        $this->objEmpresa = $objEmpresa;
    }

    public function setVDestino($vDestino){
        $this->vDestino = $vDestino;
    }

    public function setColPasajeros($colPasajeros){
        $this->colPasajeros = $colPasajeros;
    }

    public function setMensajeDeOperacion($mensajeDeOperacion){
        $this->mensajeDeOperacion = $mensajeDeOperacion;
    }

    public function setEstadoViaje($estadoViaje){
        $this->estadoViaje = $estadoViaje;
    }

    // toString
    public function __toString(){
        $cadenaPasajeros = "";
        foreach($this->getColPasajeros() as $pasajero){
            $cadenaPasajeros .= $pasajero . "\n";
        }
        return "ID del viaje: " . $this->getIdViaje() . "\n" .
               "Importe del Viaje: " . $this->getVimporte() . "\n" .
               "Cantidad Maxima de Pasajeros: " . $this->getVcantMaxPasajeros() . "\n" .
               "Responsable del Viaje: " . $this->getObjResponsable() . "\n" .
               "Empresa: " . $this->getObjEmpresa() . "\n" .
               "Coleccion de Pasajeros: \n" . $cadenaPasajeros;
    }

    // Insertar
    public function insertar(){
    $base = new BaseDeDatos();
    $respuesta = false;
    $this->setMensajeDeOperacion("");

    if ($this->getObjEmpresa() == null) {
        $this->setMensajeDeOperacion("No se asignó empresa al viaje");
    } elseif ($this->getObjResponsable() == null) {
        $this->setMensajeDeOperacion("No se asignó responsable al viaje");
    } else {
        $consulta = "INSERT INTO viaje(vdestino, vcantmaxpasajeros, idempresa, rdocumento, vimporte) VALUES ('" .
            $this->getVDestino() . "', '" .  
            $this->getVcantMaxPasajeros() . "', '" .
            $this->getObjEmpresa()->getIdEmpresa() . "', '" .
            $this->getObjResponsable()->getDocumento() . "', '" .
            $this->getVimporte() . "')";

        if($base->Iniciar()){
            $id = $base->devuelveIDInsercion($consulta);
            if($id !== null){
                $this->setIdViaje($id);
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


    // Buscar
    public function buscar($idViaje){
        $base = new BaseDeDatos();
        $respuesta = false;
        $consulta = "SELECT * FROM viaje WHERE idviaje = '$idViaje' AND estadoViaje = TRUE";
        if($base->Iniciar()) {
            if($base->Ejecutar($consulta)){
                if($fila = $base->Registro()){
                    $empresa = new Empresa();
                    $empresa->buscar($fila["idempresa"]);

                    $responsable = new ResponsableV();
                    $responsable->buscar($fila["rdocumento"]);

                    $colPasajeros = [];
                    $consultaPasajeros = "SELECT pdocumento FROM viaja WHERE idviaje = '$idViaje' AND estadoViajePasajero = TRUE";
                    if ($base->Ejecutar($consultaPasajeros)){
                        while ($filaP = $base->Registro()){
                            $pasajero = new Pasajero("", "", "", "");
                            $pasajero->buscar($filaP["pdocumento"]);
                            array_push($colPasajeros, $pasajero);
                        }
                    }
                    $this->setIdViaje($idViaje);
                    $this->setVDestino($fila["vdestino"]);
                    $this->setVimporte($fila["vimporte"]);
                    $this->setVcantMaxPasajeros($fila["vcantmaxpasajeros"]);
                    $this->setObjEmpresa($empresa);
                    $this->setObjResponsable($responsable);
                    $this->setColPasajeros($colPasajeros);

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

    // Modificar
    public function modificar(){
        $base = new BaseDeDatos();
        $respuesta = false;

        if ($this->getObjEmpresa() == null || $this->getObjResponsable() == null) {
            $this->setMensajeDeOperacion("No se pudo modificar porque la empresa o responsable es nulo");
        } else {
            $empresaId = $this->getObjEmpresa()->getIdEmpresa();
            $responsableDocumento = $this->getObjResponsable()->getDocumento();

            if ($empresaId == null || $responsableDocumento == null) {
                $this->setMensajeDeOperacion("El id de empresa o el documento del responsable es nulo");
            } else {
                $consulta = "UPDATE viaje SET 
                                vdestino = '" . $this->getVDestino() . "', 
                                vimporte = '" . $this->getVimporte() . "', 
                                vcantmaxpasajeros = '" . $this->getVcantMaxPasajeros() . "', 
                                idempresa = '" . $empresaId . "', 
                                rdocumento = '" . $responsableDocumento . "' 
                            WHERE idviaje = '" . $this->getIdViaje() . "'";

                if ($base->Iniciar()) {
                    if ($base->Ejecutar($consulta)) {
                        $respuesta = true;
                    } else {
                        $this->setMensajeDeOperacion($base->getError());
                    }
                } else {
                    $this->setMensajeDeOperacion($base->getError());
                }
            }
        }

        return $respuesta;
    }


    // Eliminar
    public function eliminar(){
        $base = new BaseDeDatos();
        $respuesta = false;

        $consulta = "UPDATE viaje SET estadoViaje = FALSE WHERE idviaje = '" . $this->getIdViaje() . "'";

        if($base->Iniciar()){
            if($base->Ejecutar($consulta)){
                $this->setEstadoViaje(false);
                $respuesta = true;
            } else {
                $this->setMensajeDeOperacion($base->getError());
            }
        } else {
            $this->setMensajeDeOperacion($base->getError());
        }
        return $respuesta;
    }


    // Listar
    public function listar($condicion = ""){
        $arreglo = [];
        $base = new BaseDeDatos();
        $consulta = "SELECT * FROM viaje WHERE estadoViaje = TRUE";
        if ($condicion != "") {
            $consulta .= " AND " . $condicion;
        }
        $consulta .= " ORDER BY idviaje";

        if ($base->Iniciar()){
            if ($base->Ejecutar($consulta)) {
                while ($fila = $base->Registro()){
                    $viaje = new Viaje();
                    $empresa = new Empresa();
                    $empresa->buscar($fila["idempresa"]);

                    $responsable = new ResponsableV();
                    $responsable->buscar($fila["rdocumento"]);

                    $colPasajeros = [];
                    $consultaPasajeros = "SELECT pdocumento FROM viaja WHERE idviaje = " . $fila["idviaje"] . " AND estadoPasajero = TRUE";
                    if($base->Ejecutar($consultaPasajeros)){
                        while ($filaP = $base->Registro()){
                            $pasajero = new Pasajero("", "", "", "");
                            $pasajero->buscar($filaP["pdocumento"]);
                            array_push($colPasajeros, $pasajero);
                        }
                    }

                    $viaje->setIdViaje($fila["idviaje"]);
                    $viaje->setVDestino($fila["vdestino"]);
                    $viaje->setVimporte($fila["vimporte"]);
                    $viaje->setVcantMaxPasajeros($fila["vcantmaxpasajeros"]);
                    $viaje->setObjEmpresa($empresa);
                    $viaje->setObjResponsable($responsable);
                    $viaje->setColPasajeros($colPasajeros);

                    array_push($arreglo, $viaje);
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
    * esta funcion guarda todos los pasajeros que tuvo un viaje  
    */
    public function cargarPasajeros() {
        $id = $this->getIdViaje();

        $viaja = new Viaja();

        $pasajeros = $viaja->obtenerPasajerosPorViaje($id);

        $this->setColPasajeros($pasajeros);
    }
}
?>