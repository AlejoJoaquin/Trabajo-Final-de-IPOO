CREATE TABLE persona (
    documento VARCHAR(15) PRIMARY KEY,
    nombre VARCHAR(150),
    apellido VARCHAR(150),
    estadoPersona BOOLEAN DEFAULT TRUE
);

CREATE TABLE pasajero (
    pdocumento VARCHAR(15) PRIMARY KEY,
    ptelefono VARCHAR(20),
    estadoPasajero BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (pdocumento) REFERENCES persona(documento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE responsable (
    rnumeroempleado BIGINT AUTO_INCREMENT UNIQUE,
    rnumerolicencia BIGINT UNIQUE,
    rdocumento VARCHAR(15) PRIMARY KEY,
    estadoResponsable BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (rdocumento) REFERENCES persona(documento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE empresa (
    idempresa BIGINT AUTO_INCREMENT PRIMARY KEY,
    enombre VARCHAR(150),
    edireccion VARCHAR(150),
    estadoE BOOLEAN DEFAULT TRUE
);

CREATE TABLE viaje (
    idviaje BIGINT AUTO_INCREMENT PRIMARY KEY,
    vdestino VARCHAR(150),
    vcantmaxpasajeros INT,
    idempresa BIGINT,
    rdocumento VARCHAR(15),
    vimporte FLOAT,
    estadoViaje BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (idempresa) REFERENCES empresa(idempresa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (rdocumento) REFERENCES responsable(rdocumento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE viaja (
    pdocumento VARCHAR(15),
    idviaje BIGINT,
    estadoViajePasajero BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (pdocumento, idviaje),
    FOREIGN KEY (pdocumento) REFERENCES pasajero(pdocumento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (idviaje) REFERENCES viaje(idviaje)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
