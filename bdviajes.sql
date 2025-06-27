CREATE TABLE persona (
    documento varchar(15) PRIMARY KEY,
    nombre varchar(150),
    apellido varchar(150)
);

/*hola*/

CREATE TABLE pasajero (
    pdocumento varchar(15) PRIMARY KEY,
    ptelefono varchar(20),
    FOREIGN KEY (pdocumento) REFERENCES persona(documento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE responsable (
    rnumeroempleado BIGINT AUTO_INCREMENT UNIQUE,
    rnumerolicencia BIGINT UNIQUE,
    rdocumento varchar(15) PRIMARY KEY,
    FOREIGN KEY (rdocumento) REFERENCES persona(documento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);


CREATE TABLE empresa (
    idempresa bigint AUTO_INCREMENT PRIMARY KEY,
    enombre varchar(150),
    edireccion varchar(150)
);

CREATE TABLE viaje (
    idviaje bigint AUTO_INCREMENT PRIMARY KEY,
    vdestino varchar(150),
    vcantmaxpasajeros int,
    idempresa bigint,
    rdocumento varchar(15),
    vimporte float,
    FOREIGN KEY (idempresa) REFERENCES empresa(idempresa)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (rdocumento) REFERENCES responsable(rdocumento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);

CREATE TABLE viaja (
    pdocumento varchar(15),
    idviaje bigint,
    PRIMARY KEY (pdocumento, idviaje),
    FOREIGN KEY (pdocumento) REFERENCES pasajero(pdocumento)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    FOREIGN KEY (idviaje) REFERENCES viaje(idviaje)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);