<?php
/* IMPORTANTE !!!!  Clase para (PHP 5, PHP 7)*/

class BaseDeDatos {
    private $HOSTNAME;
    private $BASEDATOS;
    private $USUARIO;
    private $CLAVE;
    private $CONEXION;
    private $QUERY;
    private $RESULT;
    private $ERROR;

    /**
     * Constructor de la clase que inicia las variables instancias de la clase
     * vinculadas a la conección con el Servidor de BD
     */
    public function __construct() {
        $this->HOSTNAME = "127.0.0.1";
        $this->BASEDATOS = "bdViajes";
        $this->USUARIO = "root";
        $this->CLAVE = "";
        $this->RESULT = null;
        $this->QUERY = "";
        $this->ERROR = "";
        $this->CONEXION = null;
    }

    /**
     * Función que retorna una cadena
     * con una pequeña descripción del error si lo hubiera
     *
     * @return string
     */
    public function getError() {
        return "\n" . $this->ERROR;
    }

    /**
     * Inicia la conexión con el Servidor y la Base Datos MySQL.
     * Retorna true si la conexión con el servidor se pudo establecer y false en caso contrario
     *
     * @return boolean
     */
    public function Iniciar() {
        $resp = false;
        try {
            $dsn = "mysql:host={$this->HOSTNAME};dbname={$this->BASEDATOS};charset=utf8";
            $this->CONEXION = new PDO($dsn, $this->USUARIO, $this->CLAVE);
            $this->CONEXION->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $resp = true;
        } catch (PDOException $e) {
            $this->ERROR = $e->getCode() . ": " . $e->getMessage();
        }
        return $resp;
    }

    /**
     * Ejecuta una consulta en la Base de Datos.
     * Recibe la consulta en una cadena enviada por parámetro.
     *
     * @param string $consulta
     * @return boolean
     */
    public function Ejecutar($consulta) {
        $resp = false;
        $this->QUERY = $consulta;
        $this->ERROR = "";
        try {
            $this->RESULT = $this->CONEXION->query($consulta);
            $resp = true;
        } catch (PDOException $e) {
            $this->ERROR = $e->getCode() . ": " . $e->getMessage();
        }
        return $resp;
    }

    /**
     * Devuelve un registro retornado por la ejecución de una consulta.
     * El puntero se desplaza al siguiente registro de la consulta
     *
     * @return mixed|null
     */
    public function Registro() {
        $resp = null;
        if ($this->RESULT) {
            $this->ERROR = "";
            $registro = $this->RESULT->fetch(PDO::FETCH_ASSOC);
            if ($registro) {
                $resp = $registro;
            } else {
                $this->RESULT = null; // Liberar resultado
            }
        } else {
            $this->ERROR = "Sin resultado válido para recorrer.";
        }
        return $resp;
    }

    /**
     * Devuelve el id de un campo autoincrement utilizado como clave de una tabla
     * Retorna el id numérico del registro insertado, devuelve null en caso que la ejecución de la consulta falle
     *
     * @param string $consulta
     * @return int|null id de la tupla insertada
     */
    public function devuelveIDInsercion($consulta) {
        $resp = null;
        $this->QUERY = $consulta;
        $this->ERROR = "";
        try {
            $this->CONEXION->exec($consulta);
            $resp = $this->CONEXION->lastInsertId();
        } catch (PDOException $e) {
            $this->ERROR = $e->getCode() . ": " . $e->getMessage();
        }
        return $resp;
    }
}
?>
