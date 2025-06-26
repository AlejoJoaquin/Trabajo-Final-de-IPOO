<?php

// Función para validar si es número entero positivo
function esNum($num) {
    return ctype_digit($num) && intval($num) > 0;
}

include_once 'Persona.php';
include_once 'Viaja.php';
include_once 'Viaje.php';
include_once 'Empresa.php';
include_once 'Pasajero.php';
include_once 'ResponsableV.php';

$bd = new BaseDeDatos();
if ($bd->iniciar()){
    echo "se ha iniciado correctamente \n";
}else{
    echo "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH \n";
}
$salir = false;
while (!$salir) {
    echo "BIENVENIDO, INGRESE UNA OPCION\n";
    echo "1) Persona\n";
    echo "2) ResponsableV\n";
    echo "3) Pasajero\n";
    echo "4) Viaja\n";
    echo "5) Viaje\n";
    echo "6) Empresa\n";
    echo "7) Salir\n";
    echo "Opción: ";
    $opcion = trim(fgets(STDIN));

    if (!esNum($opcion) || $opcion < 1 || $opcion > 7) {
        echo "Opción inválida, intente de nuevo.\n";
    }

    switch ($opcion) {
        case 1: // Persona
            $opPersona = 0;
            while ($opPersona < 1 || $opPersona > 5) {
                echo "MENU PERSONA\n";
                echo "1) Insertar persona\n";
                echo "2) Modificar persona\n";
                echo "3) Eliminar persona\n";
                echo "4) Buscar persona\n";
                echo "Opción: ";
                $opPersona = trim(fgets(STDIN));
                if (!esNum($opPersona) || $opPersona < 1 || $opPersona > 5) {
                    echo "Opción inválida, intente de nuevo.\n";
                }
            }
            switch ($opPersona) {
                case 1: // Insertar
                    $persona = new Persona();

                    do {
                        echo "Ingrese documento (solo números, max 15, sin 0 al inicio): ";
                        $documento = trim(fgets(STDIN));
                    } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                    do {
                        echo "Ingrese nombre (solo letras, max 150): ";
                        $nombre = strtolower(trim(fgets(STDIN)));
                    } while (strlen($nombre) > 150 || !ctype_alpha($nombre));

                    do {
                        echo "Ingrese apellido (solo letras, max 150): ";
                        $apellido = strtolower(trim(fgets(STDIN)));
                    } while (strlen($apellido) > 150 || !ctype_alpha($apellido));

                    $persona->setDocumento($documento);
                    $persona->setNombre($nombre);
                    $persona->setApellido($apellido);

                    if ($persona->insertar()) {
                        echo "Persona insertada correctamente.\n";
                    } else{
                        $mensaje = $persona->getMensajeDeOperacion();
                        if(str_contains($mensaje, '1062')){
                            echo "ERROR AL INSERTAR PERSONA: el documento ingresado ya se existe \n";
                        }else{
                            echo "ERROR: no se pudo insertar a la persona" . $persona->getMensajeDeOperacion() . "\n";
                        }
                    }
                break;

                case 2: // Modificar
                    $persona = new Persona();

                    do {
                        echo "Ingrese documento de persona a modificar: ";
                        $documento = trim(fgets(STDIN));
                    } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                    if ($persona->buscar($documento)) {
                        do {
                            echo "Ingrese nuevo nombre (solo letras, max 150): ";
                            $nombre = strtolower(trim(fgets(STDIN)));
                        } while (strlen($nombre) > 150 || !ctype_alpha($nombre));

                        do {
                            echo "Ingrese nuevo apellido (solo letras, max 150): ";
                            $apellido = strtolower(trim(fgets(STDIN)));
                        } while (strlen($apellido) > 150 || !ctype_alpha($apellido));

                        $persona->setNombre($nombre);
                        $persona->setApellido($apellido);

                        if ($persona->modificar()) {
                            echo "Persona modificada correctamente.\n";
                        } else {
                            echo "Error al modificar persona: " . $persona->getMensajeDeOperacion() . "\n";
                        }
                    } else {
                        echo "No existe la persona con ese documento.\n";
                    }
                break;

                case 3: // Eliminar
                    $persona = new Persona();

                    do {
                        echo "Ingrese documento de persona a eliminar: ";
                        $documento = trim(fgets(STDIN));
                    } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                    if ($persona->buscar($documento)) {
                        if ($persona->eliminar()) {
                            echo "Persona eliminada correctamente.\n";
                        } else {
                            $mensaje = $persona->getMensajeDeOperacion();
                            if(str_contains($mensaje, '1451')){
                                echo "ERROR AL eliminar PERSONA: no se pudo eliminar porque hay datos relacionados \n";
                            }else{
                                echo "ERROR: no se pudo eliminar a la persona" . $persona->getMensajeDeOperacion() .  "\n";
                            }
                        }
                    } else {
                        echo "No existe la persona con ese documento.\n";
                    }
                break;

                case 4: // Buscar
                    $persona = new Persona();

                    do {
                        echo "Ingrese documento de persona a buscar: ";
                        $documento = trim(fgets(STDIN));
                    } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                    if ($persona->buscar($documento)) {
                        echo $persona;
                    } else {
                        echo "Persona no encontrada.\n";
                    }
                break;

                case 5: // Mostrar todas
                    $persona = new Persona();
                    $lista = $persona->listar();

                    if (count($lista) > 0) {
                        foreach ($lista as $unaPersona) {
                            echo $unaPersona . "\n";
                            echo "---------------------\n";
                        }
                    } else {
                        echo "No hay personas cargadas.\n";
                    }
                break;
        }
    break;


    case 2: // ResponsableV
        $opResp = 0;
        while ($opResp < 1 || $opResp > 5) {
            echo "MENU RESPONSABLEV\n";
            echo "1) Insertar responsableV\n";
            echo "2) Modificar responsableV\n";
            echo "3) Eliminar responsableV\n";
            echo "4) Buscar responsableV\n";
            echo "5) Mostrar todos los responsables\n";
            echo "Opción: ";
            $opResp = trim(fgets(STDIN));
            if (!esNum($opResp) || $opResp < 1 || $opResp > 5) {
                echo "Opción inválida, intente de nuevo.\n";
            }
        }

        switch ($opResp) {
            case 1: // Insertar
                echo "Estoy dentro del insert de ResponsableV\n";
                $responsableV = new ResponsableV();

                do {
                    echo "Ingrese documento (solo números, max 15, sin 0 al inicio): ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");


                echo "Ingrese nombre: ";
                $nombre = trim(fgets(STDIN));

                echo "Ingrese apellido: ";
                $apellido = trim(fgets(STDIN));

                do {
                    echo "Ingrese número de licencia (solo números): ";
                    $licencia = trim(fgets(STDIN));
                } while (!ctype_digit($licencia));

                $responsableV->setDocumento($documento);
                $responsableV->setNombre($nombre);
                $responsableV->setApellido($apellido);
                $responsableV->setNumeroLicencia($licencia);

                if ($responsableV->insertar()) {
                    echo "ResponsableV insertado correctamente.\n";
                } else {
                    $mensaje = $responsableV->getMensajeDeOperacion();
                    if(str_contains($mensaje, '1062')){
                        echo "ERROR AL INSERTAR RESPONSABLEV: el documento ingresado ya se existe \n";
                    }else{
                        echo "Error al insertar responsableV: " . $responsableV->getMensajeDeOperacion() . "\n";
                    }
                }
            break;

            case 2: // Modificar
                $responsableV = new ResponsableV();

                do {
                    echo "Ingrese número empleado a modificar: ";
                    $numEmpleado = trim(fgets(STDIN));
                } while (!esNum($numEmpleado));

                if ($responsableV->buscar($numEmpleado)) {
                    do {
                        echo "Ingrese nuevo número de licencia (solo números): ";
                        $licencia = trim(fgets(STDIN));
                    } while (!ctype_digit($licencia));

                $responsableV->setNumeroLicencia($licencia);

                if ($responsableV->modificar()) {
                    echo "ResponsableV modificado correctamente.\n";
                } else {
                    echo "Error al modificar responsableV: " . $responsableV->getMensajeDeOperacion() . "\n";
                }
                } else {
                    echo "No existe responsableV con ese número empleado.\n";
                }
            break;

            case 3: // Eliminar
                $responsableV = new ResponsableV();

                do {
                    echo "Ingrese número empleado a eliminar: ";
                    $numEmpleado = trim(fgets(STDIN));
                } while (!esNum($numEmpleado));

                if ($responsableV->buscar($numEmpleado)) {
                    if ($responsableV->eliminar()) {
                        echo "ResponsableV eliminado correctamente.\n";
                    } else {
                        if (str_contains($mensaje, '1451')){
                            echo "ERROR AL eliminar RESPONSABLE: no se pudo eliminar porque hay datos relacionados \n";
                        }else{
                            echo "Error al eliminar responsable" . $responsableV->getMensajeDeOperacion() . "\n";
                        }
                    }
                } else {
                    echo "No existe responsableV con ese número empleado.\n";
                }
            break;

            case 4: // Buscar
                $responsableV = new ResponsableV();

                do {
                    echo "Ingrese número empleado a buscar: ";
                    $numEmpleado = trim(fgets(STDIN));
                } while (!esNum($numEmpleado));

                if ($responsableV->buscar($numEmpleado)) {
                    echo $responsableV;
                } else {
                    echo "ResponsableV no encontrado.\n";
                }
            break;

            case 5: // Mostrar todos
                $responsableV = new ResponsableV();
                $lista = $responsableV->listar();

                if (count($lista) > 0) {
                    foreach ($lista as $unResponsable) {
                        echo $unResponsable . "\n";
                        echo "-----------------------\n";
                    }
                } else {
                    echo "No hay responsables cargados.\n";
                }
            break;
        }
    break;

    case 3: // Pasajero
        $opPasajero = 0;
        while ($opPasajero < 1 || $opPasajero > 5) {
            echo "MENU PASAJERO\n";
            echo "1) Insertar pasajero\n";
            echo "2) Modificar pasajero\n";
            echo "3) Eliminar pasajero\n";
            echo "4) Buscar pasajero\n";
            echo "5) Mostrar viajes del pasajero\n";
            echo "Opción: ";
            $opPasajero = trim(fgets(STDIN));
            if (!esNum($opPasajero) || $opPasajero < 1 || $opPasajero > 5) {
                echo "Opción inválida, intente de nuevo.\n";
            }
        }

        switch ($opPasajero) {
            case 1: // Insertar
                do {
                    echo "Ingrese documento (solo números, max 15, sin 0 al inicio): ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                do {
                    echo "Ingrese nombre (solo letras, max 150): ";
                    $nombre = strtolower(trim(fgets(STDIN)));
                } while (strlen($nombre) > 150 || !ctype_alpha($nombre));

                do {
                    echo "Ingrese apellido (solo letras, max 150): ";
                    $apellido = strtolower(trim(fgets(STDIN)));
                } while (strlen($apellido) > 150 || !ctype_alpha($apellido));

                do {
                    echo "Ingrese teléfono (solo números, max 20): ";
                    $telefono = trim(fgets(STDIN));
                } while (!ctype_digit($telefono) || strlen($telefono) > 20);

                $pasajero = new Pasajero();
                $pasajero->setDocumento($documento);
                $pasajero->setNombre($nombre);
                $pasajero->setApellido($apellido);
                $pasajero->setTelefono($telefono);

                if ($pasajero->insertar()) {
                    echo "Pasajero insertado correctamente.\n";
                } else {
                    $mensaje = $pasajero->getMensajeDeOperacion();
                    if (str_contains($mensaje, '1062')){
                        echo "ERROR AL INSERTAR PASAJERO: el documento ingresado ya se existe \n";
                    }else{
                        echo "Error al insertar pasajero: " . $pasajero->getMensajeDeOperacion() . "\n";
                    }
                }
            break;

            case 2: // Modificar
                do {
                    echo "Ingrese documento de pasajero a modificar: ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                $pasajero = new Pasajero();
                if ($pasajero->buscar($documento)) {
                    do {
                        echo "Ingrese nuevo teléfono (solo números, max 20): ";
                        $telefono = trim(fgets(STDIN));
                    } while (!ctype_digit($telefono) || strlen($telefono) > 20);

                    $pasajero->setTelefono($telefono);

                    if ($pasajero->modificar()) {
                        echo "Pasajero modificado correctamente.\n";
                    } else {
                        echo "Error al modificar pasajero: " . $pasajero->getMensajeDeOperacion() . "\n";
                    }
                } else {
                    echo "No existe pasajero con ese documento.\n";
                }
            break;

            case 3: // Eliminar
                do {
                    echo "Ingrese documento de pasajero a eliminar: ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                $pasajero = new Pasajero();
                if ($pasajero->buscar($documento)) {
                    if ($pasajero->eliminar()) {
                        echo "Pasajero eliminado correctamente.\n";
                    } else {
                        if (str_contains($mensaje, '1451')){
                            echo "ERROR AL eliminar PASAJERO: no se pudo eliminar porque hay datos relacionados \n";
                        }else{
                            echo "Error al eliminar pasajero: " . $pasajero->getMensajeDeOperacion() . "\n";
                        }
                    }
                } else {
                    echo "No existe pasajero con ese documento.\n";
                }
            break;

            case 4: // Buscar
                do {
                    echo "Ingrese documento de pasajero a buscar: ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                $pasajero = new Pasajero();
                if ($pasajero->buscar($documento)) {
                    echo "Documento: " . $pasajero->getDocumento() . "\n";
                    echo "Nombre: " . $pasajero->getNombre() . "\n";
                    echo "Apellido: " . $pasajero->getApellido() . "\n";
                    echo "Teléfono: " . $pasajero->getTelefono() . "\n";
                } else {
                    echo "Pasajero no encontrado.\n";
                }
            break;

            case 5: // Mostrar viajes del pasajero
                do {
                    echo "Ingrese documento del pasajero: ";
                    $documento = trim(fgets(STDIN));
                } while (!ctype_digit($documento) || strlen($documento) > 15 || $documento[0] === "0");

                $pasajero = new Pasajero();
                if ($pasajero->buscar($documento)) {
                    $pasajero->cargarViajes(); 
                    $viajes = $pasajero->getColViajes(); 

                    if (!empty($viajes)) {
                        echo "Viajes del pasajero:\n";
                        foreach ($viajes as $viaje) {
                            echo "- ID: " . $viaje->getIdViaje() . ", Destino: " . $viaje->getVDestino() . ", Importe: " . $viaje->getVImporte() . "\n";
                        }
                    } else {
                        echo "El pasajero no tiene viajes.\n";
                    }
                } else {
                    echo "Pasajero no encontrado.\n";
                }
        break;
        }
    break;

    case 4: // Viaja (tabla intermedia)
    $opViaja = 0;
    while ($opViaja < 1 || $opViaja > 4) {
        echo "MENU VIAJA\n";
        echo "1) Insertar relación viaja\n";
        echo "2) Modificar relación viaja\n";
        echo "3) Eliminar relación viaja\n";
        echo "4) Buscar relación viaja\n";
        echo "Opción: ";
        $opViaja = trim(fgets(STDIN));
        if (!esNum($opViaja) || $opViaja < 1 || $opViaja > 4) {
            echo "Opción inválida, intente de nuevo.\n";
        }
    }

    switch ($opViaja) {
        case 1: // Insertar
             $viaja = new Viaja();

            do {
                echo "Ingrese id viaje: ";
                $idViajes = trim(fgets(STDIN));
            } while (!esNum($idViajes));

            $viaje = new Viaje();
            $viajeEncontrado = $viaje->buscar($idViajes);

            do {
                echo "Ingrese documento del pasajero: ";
                $pDocumento = trim(fgets(STDIN));
            } while (!ctype_digit($pDocumento) || strlen($pDocumento) > 15 || $pDocumento[0] === "0");

            $pasajero = new Pasajero();
            $pasajeroEncontrado = $pasajero->buscar($pDocumento);

            if ($viajeEncontrado && $pasajeroEncontrado) {
                $viaja->setObjViaje($viaje);
                $viaja->setObjPasajero($pasajero);

                if ($viaja->insertar()) {
                    echo "Relación insertada correctamente.\n";
                } else {
                    $mensaje = $viaja->getMensajeDeOperacion();
                    if (str_contains($mensaje, '1062')){
                        echo "ERROR AL INSERTAR VIAJA-PASAJERO: el documento y el idViaje ingresados ya existen \n";
                    }else{
                        echo "Error al insertar viaja-pasajero: " . $viaja->getMensajeDeOperacion() . "\n";
                    }
                }
            } else {
                if (!$viajeEncontrado) {
                    echo "No se encontró el viaje con ID $idViajes.\n";
                }
                if (!$pasajeroEncontrado) {
                    echo "No se encontró el pasajero con documento $pDocumento.\n";
                }
            }

        break;

        case 2:
            // Modificar relación viaja
            $viaja = new Viaja();

            do {
                echo "Ingrese ID del viaje a modificar: ";
                $idViaje = trim(fgets(STDIN));
            } while (!esNum($idViaje));

            do {
                echo "Ingrese documento del pasajero a modificar: ";
                $documentoPasajero = trim(fgets(STDIN));
            } while (!ctype_digit($documentoPasajero) || strlen($documentoPasajero) > 15 || $documentoPasajero[0] === "0");

            if ($viaja->buscar($idViaje, $documentoPasajero)) {
                do {
                    echo "Ingrese nuevo ID del viaje: ";
                    $nuevoIdViaje = trim(fgets(STDIN));
                } while (!esNum($nuevoIdViaje));

                do {
                    echo "Ingrese nuevo documento del pasajero: ";
                    $nuevoDocumento = trim(fgets(STDIN));
                } while (!ctype_digit($nuevoDocumento) || strlen($nuevoDocumento) > 15 || $nuevoDocumento[0] === "0");

                if ($viaja->modificar($nuevoIdViaje, $nuevoDocumento)) {
                    echo "Relación viaja modificada correctamente.\n";
                } else {
                    echo "Error al modificar relación viaja: " . $viaja->getMensajeDeOperacion() . "\n";
                }
            } else {
                echo "No existe esa relación.\n";
            }
        break;

        case 3: // Eliminar
            $viaja = new Viaja();

            do {
                echo "Ingrese id viaje a eliminar: ";
                $idViajes = trim(fgets(STDIN));
            } while (!esNum($idViajes));

            do {
                echo "Ingrese documento pasajero a eliminar: ";
                $pDocumento = trim(fgets(STDIN));
            } while (!ctype_digit($pDocumento) || strlen($pDocumento) > 15 || $pDocumento[0] === "0");

            if (!$viaja->buscar($idViajes, $pDocumento)) {
                echo "No existe esa relación.\n";
            }

            if ($viaja->buscar($idViajes, $pDocumento)){
                if ($viaja->eliminar()) {
                    echo "Relación eliminada correctamente.\n";
                } else {
                    if (str_contains($mensaje, '1451')){
                        echo "ERROR AL eliminar VIAJE-PASAJERO: no se pudo eliminar porque hay datos relacionados \n";
                    }else{
                        echo "Error al eliminar relación: " . $viaja->getMensajeDeOperacion() . "\n";   
                    }
                }
            }else{
                echo "relacion VIAJE-PASAJERO no encontrada";
            }
            
            break;

        case 4: // Buscar
            $viaja = new Viaja();
            $salir = false;

            do {
                echo "Ingrese id viaje: ";
                $idViaje = trim(fgets(STDIN));
            } while (!esNum($idViaje));

            do {
                echo "Ingrese documento pasajero: ";
                $pDocumento = trim(fgets(STDIN));
            } while (!ctype_digit($pDocumento) || strlen($pDocumento) > 15 || $pDocumento[0] === "0");

            if (!$salir) {
                $viajeObj = new Viaje();
                if (!$viajeObj->buscar($idViaje)) {
                    echo "No existe el viaje con ese ID.\n";
                    $salir = true;
                }
            }

            if (!$salir) {
                $pasajeroObj = new Pasajero();
                if (!$pasajeroObj->buscar($pDocumento)) {
                    echo "No existe el pasajero con ese documento.\n";
                    $salir = true;
                }
            }

            if (!$salir) {
                $viaja->setObjViaje($viajeObj);
                $viaja->setObjPasajero($pasajeroObj);

                if ($viaja->buscar($idViaje, $pDocumento)) {
                    echo "ID Viaje: " . $viaja->getObjViaje()->getIdViaje() . "\n";
                    echo "Documento Pasajero: " . $viaja->getObjPasajero()->getDocumento() . "\n";
                } else {
                    echo "Relación no encontrada.\n";
                }
            }
        break;
        }
    break;

    case 5: // Viaje
        $opViaje = 0;
        while ($opViaje < 1 || $opViaje > 4) {
            echo "MENU VIAJE\n";
            echo "1) Insertar viaje\n";
            echo "2) Modificar viaje\n";
            echo "3) Eliminar viaje\n";
            echo "4) Buscar viaje\n";
            echo "Opción: ";
            $opViaje = trim(fgets(STDIN));
            if (!esNum($opViaje) || $opViaje < 1 || $opViaje > 4) {
                echo "Opción inválida, intente de nuevo.\n";
            }
        }

        switch ($opViaje) {
           case 1:
                $viaje = new Viaje();

                do {
                    echo "Ingrese destino (max 100 letras): ";
                    $destino = strtolower(trim(fgets(STDIN)));
                } while (strlen($destino) > 100 || !ctype_alpha(str_replace(' ', '', $destino)));

                do {
                    echo "Ingrese cantidad máxima pasajeros (número): ";
                    $cantMax = trim(fgets(STDIN));
                } while (!esNum($cantMax));

                do {
                    echo "Ingrese documento del responsable: ";
                    $docResponsable = trim(fgets(STDIN));
                } while (empty($docResponsable));

                do {
                    echo "Ingrese importe (número decimal): ";
                    $importe = trim(fgets(STDIN));
                } while (!is_numeric($importe) || $importe < 0);

                do {
                    echo "Ingrese ID empresa (número): ";
                    $idEmpresa = trim(fgets(STDIN));
                } while (!esNum($idEmpresa));

                $empresa = new Empresa();
                $responsable = new ResponsableV();
                
                if ($empresa->buscar($idEmpresa) && $responsable->buscar($docResponsable)) {
                    $viaje->setVDestino($destino);
                    $viaje->setVCantMaxPasajeros($cantMax);
                    $viaje->setVImporte($importe);
                    $viaje->setObjEmpresa($empresa);
                    $viaje->setObjResponsable($responsable);
                    //No es necesario cargar el id ya que en insertar se asigna

                    if ($viaje->insertar()) {
                        echo "Viaje insertado correctamente\n";
                        $viaje->getObjResponsable()->cargarViajeResponsable($viaje);    //el parametro viaje ya tiene el id, ademas de los demas datos
                    } else {
                        $mensaje = $viaje->getMensajeDeOperacion();
                        if (str_contains($mensaje, '1062')){
                            echo "ERROR AL INSERTAR VIAJE: el idViaje ingresado ya existe \n";
                        }else{
                            echo "Error al insertar viaje: " . $viaje->getMensajeDeOperacion() . "\n";
                        }
                    }
                } else {
                    echo "Empresa o Responsable no encontrados.\n";
                }
            break;

            case 2:
                $viaje = new Viaje();

                do {
                    echo "Ingrese ID viaje a modificar: ";
                    $idViaje = trim(fgets(STDIN));
                } while (!esNum($idViaje));

                if ($viaje->buscar($idViaje)) {
                    do {
                        echo "Ingrese nuevo destino (max 100 letras): ";
                        $destino = strtolower(trim(fgets(STDIN)));
                    } while (strlen($destino) > 100 || !ctype_alpha(str_replace(' ', '', $destino)));

                    do {
                        echo "Ingrese nueva cantidad máxima pasajeros (número): ";
                        $cantMax = trim(fgets(STDIN));
                    } while (!esNum($cantMax));

                    do {
                        echo "Ingrese documento del responsable: ";
                        $docResponsable = trim(fgets(STDIN));
                    } while (empty($docResponsable));

                    do {
                        echo "Ingrese nuevo importe (número decimal): ";
                        $importe = trim(fgets(STDIN));
                    } while (!is_numeric($importe) || $importe < 0);

                    do {
                        echo "Ingrese nuevo ID empresa (número): ";
                        $idEmpresa = trim(fgets(STDIN));
                    } while (!esNum($idEmpresa));

                    $empresa = new Empresa();
                    $responsable = new ResponsableV();

                    if ($empresa->buscar($idEmpresa) && $responsable->buscar($docResponsable)) {
                        $viaje->setVDestino($destino);
                        $viaje->setVCantMaxPasajeros($cantMax);
                        $viaje->setVImporte($importe);
                        $viaje->setObjEmpresa($empresa);
                        $viaje->setObjResponsable($responsable);

                        if ($viaje->modificar()) {
                            echo "Viaje modificado correctamente.\n";
                        } else {
                            echo "Error al modificar viaje: " . $viaje->getMensajeDeOperacion() . "\n";
                        }
                    } else {
                        echo "Empresa o ResponsableV no encontrados.\n";
                    }
                } else {
                    echo "No existe viaje con ese ID.\n";
                }
            break;

            case 3:
                $viaje = new Viaje();

                do {
                    echo "Ingrese ID viaje a eliminar: ";
                    $idViaje = trim(fgets(STDIN));
                } while (!esNum($idViaje));

                if ($viaje->buscar($idViaje)) {
                    if ($viaje->eliminar()) {
                        echo "Viaje eliminado correctamente.\n";
                    } else {
                        if (str_contains($mensaje, '1451')){
                            echo "ERROR AL eliminar VIAJE: no se pudo eliminar porque hay datos relacionados \n";
                        }else{
                            echo "Error al eliminar viaje: " . $viaje->getMensajeDeOperacion() . "\n";   
                        }
                    }
                } else {
                    echo "No existe viaje con ese ID.\n";
                }
                break;

            case 4:
                $viaje = new Viaje();

                do {
                    echo "Ingrese ID viaje a buscar: ";
                    $idViaje = trim(fgets(STDIN));
                } while (!esNum($idViaje));

                if ($viaje->buscar($idViaje)) {
                    echo "ID: " . $viaje->getIdViaje() . "\n";
                    echo "Destino: " . $viaje->getVDestino() . "\n";
                    echo "Cantidad máxima pasajeros: " . $viaje->getVCantMaxPasajeros() . "\n";
                    echo "Importe: " . $viaje->getVImporte() . "\n";
                    if ($viaje->getObjEmpresa() !== null) {
                        echo "Empresa: " . $viaje->getObjEmpresa()->getENombre() . "\n";
                    } else {
                        echo "Empresa: No asignada\n";
                    }

                    if ($viaje->getObjResponsable() !== null) {
                        echo "ResponsableV: " . $viaje->getObjResponsable()->getNombre() . " " . $viaje->getObjResponsable()->getApellido() . "\n";
                    } else {
                        echo "ResponsableV: No asignado\n";
                    }
                } else {
                    echo "Viaje no encontrado.\n";
                }
            break;
        }
    break;


    case 6: // Empresa
        $opEmpresa = 0;
        while ($opEmpresa < 1 || $opEmpresa > 4) {
            echo "MENU EMPRESA\n";
            echo "1) Insertar empresa\n";
            echo "2) Modificar empresa\n";
            echo "3) Eliminar empresa\n";
            echo "4) Buscar empresa\n";
            echo "Opción: ";
            $opEmpresa = trim(fgets(STDIN));
            if (!esNum($opEmpresa) || $opEmpresa < 1 || $opEmpresa > 4) {
                echo "Opción inválida, intente de nuevo.\n";
            }
        }

        switch ($opEmpresa) {
            case 1:
                $empresa = new Empresa();

                do {
                    echo "Ingrese nombre empresa (max 150 letras): ";
                    $nombre = strtolower(trim(fgets(STDIN)));
                } while (strlen($nombre) > 150 || !ctype_alpha(str_replace(' ', '', $nombre)));

                do {
                    echo "Ingrese dirección empresa (max 150 letras): ";
                    $direccion = strtolower(trim(fgets(STDIN)));
                } while (strlen($direccion) > 150 || !ctype_alnum(str_replace(' ', '', $direccion)));

                $empresa->setNombre($nombre);
                $empresa->setDireccion($direccion);

                if ($empresa->insertar()) {
                    echo "Empresa insertada correctamente.\n";
                } else{
                    $mensaje = $empresa->getMensajeDeOperacion();
                    if (str_contains($mensaje, '1062')){
                        echo "ERROR AL INSERTAR EMPRESA: el idEmpresa ingresado ya existe \n";
                    }else{
                        echo "Error al insertar empresa: " . $empresa->getMensajeDeOperacion() . "\n";
                    }
                }
            break;

            case 2:
                $empresa = new Empresa();

                do {
                    echo "Ingrese ID empresa a modificar: ";
                    $idEmpresa = trim(fgets(STDIN));
                } while (!esNum($idEmpresa));

                if (!$empresa->buscar($idEmpresa)) {
                    echo "No existe empresa con ese ID.\n";
                } else {
                    do {
                        echo "Ingrese nuevo nombre empresa (max 150 letras): ";
                        $nombre = strtolower(trim(fgets(STDIN)));
                    } while (strlen($nombre) > 150 || !ctype_alpha(str_replace(' ', '', $nombre)));

                    do {
                        echo "Ingrese nueva dirección empresa (max 150 letras): ";
                        $direccion = strtolower(trim(fgets(STDIN)));
                    } while (strlen($direccion) > 150 || !ctype_alnum(str_replace(' ', '', $direccion)));

                    $empresa->setNombre($nombre);
                    $empresa->setDireccion($direccion);

                    if ($empresa->modificar()) {
                        echo "Empresa modificada correctamente.\n";
                    } else {
                        echo "Error al modificar empresa: " . $empresa->getMensajeDeOperacion() . "\n";
                    }
                }
            break;

            case 3:
                $empresa = new Empresa();

                do {
                    echo "Ingrese ID empresa a eliminar: ";
                    $idEmpresa = trim(fgets(STDIN));
                } while (!esNum($idEmpresa));

                if (!$empresa->buscar($idEmpresa)) {
                    echo "No existe empresa con ese ID.\n";
                } else {
                    if ($empresa->eliminar()) {
                        echo "Empresa eliminada correctamente.\n";
                    } else {
                        if (str_contains($mensaje, '1451')){
                            echo "ERROR AL eliminar EMPRESA: no se pudo eliminar porque hay datos relacionados \n";
                        }else{
                            echo "Error al eliminar empresa: " . $empresa->getMensajeDeOperacion() . "\n";
                        }
                    }
                }
            break;

            case 4:
                $empresa = new Empresa();

                do {
                    echo "Ingrese ID empresa a buscar: ";
                    $idEmpresa = trim(fgets(STDIN));
                } while (!esNum($idEmpresa));

                if ($empresa->buscar($idEmpresa)) {
                    echo "ID Empresa: " . $empresa->getIdEmpresa() . "\n";
                    echo "Nombre: " . $empresa->getNombre() . "\n";
                    echo "Dirección: " . $empresa->getDireccion() . "\n";
                } else {
                    echo "Empresa no encontrada.\n";
                }
            break;
        }
    break;

    case 7:
        $salir = true;
        echo "Saliendo del programa...\n";
    break;
    }
}
