-- Active: 1729102473371@@127.0.0.1@3306
CREATE DATABASE Biblioteca;
USE Biblioteca;

-- Tabla: CATEGORIAS
CREATE TABLE CATEGORIAS (
    codCategoria INT AUTO_INCREMENT PRIMARY KEY,
    nombreCategoria VARCHAR(45)
);

-- Tabla: AUTORES
CREATE TABLE AUTORES (
    codAutores INT AUTO_INCREMENT PRIMARY KEY,
    nombreAutor VARCHAR(45),
    apellidoAutor VARCHAR(45)
);

-- Tabla: LIBROS
CREATE TABLE LIBROS (
    codLibros INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(45),
    ISBN VARCHAR(45),
    fechaPublicacion DATE,
    cantidadDisponible INT,
    urlPortada VARCHAR(45),
    CATEGORIAS_codCategoria INT,
    AUTORES_codAutores INT,
    FOREIGN KEY (CATEGORIAS_codCategoria) REFERENCES CATEGORIAS(codCategoria),
    FOREIGN KEY (AUTORES_codAutores) REFERENCES AUTORES(codAutores)
);

-- Tabla: CLIENTES
CREATE TABLE CLIENTES (
    codUsuarios INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(45),
    apellido VARCHAR(45),
    ci VARCHAR(45),
    direccion VARCHAR(45),
    telefono VARCHAR(45),
    correo VARCHAR(45),
    usuario VARCHAR(45),
    password VARCHAR(45),
    rol VARCHAR(45)
);

-- Tabla: EMPLEADOS
CREATE TABLE EMPLEADOS (
    codUSUARIOS_ADMIN INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(45),
    password VARCHAR(45),
    rol VARCHAR(45)
);

-- Tabla: SANCIONES
CREATE TABLE SANCIONES (
    codSancion INT AUTO_INCREMENT PRIMARY KEY,
    multa VARCHAR(45),
    fechaSancion VARCHAR(45)
);

-- Tabla: PRESTAMOS
CREATE TABLE PRESTAMOS (
    codPrestamos INT AUTO_INCREMENT PRIMARY KEY,
    fechaPrestamo DATE,
    fechaDevolucion DATE,
    estado VARCHAR(45),
    LIBROS_codLibros INT,
    USUARIOS_codUsuarios INT,
    SANCIONES_codSancion INT,
    FOREIGN KEY (LIBROS_codLibros) REFERENCES LIBROS(codLibros),
    FOREIGN KEY (USUARIOS_codUsuarios) REFERENCES CLIENTES(codUsuarios),
    FOREIGN KEY (SANCIONES_codSancion) REFERENCES SANCIONES(codSancion)
);

-- Tabla: cuartoEstudio
CREATE TABLE cuartoEstudio (
    codCuartoEstudio INT AUTO_INCREMENT PRIMARY KEY,
    nombreCuarto VARCHAR(45)
);

-- Tabla: reservaCuarto
CREATE TABLE reservaCuarto (
    codreservaCuarto INT AUTO_INCREMENT PRIMARY KEY,
    fechaReserva VARCHAR(45),
    horaReserva VARCHAR(45),
    cuartoEstudio_codCuartoEstudio INT,
    USUARIOS_codUsuarios INT,
    FOREIGN KEY (cuartoEstudio_codCuartoEstudio) REFERENCES cuartoEstudio(codCuartoEstudio),
    FOREIGN KEY (USUARIOS_codUsuarios) REFERENCES CLIENTES(codUsuarios)
);



-- Insertar 10 clientes
INSERT INTO CLIENTES (nombre, apellido, ci, direccion, telefono, correo, usuario, password, rol) VALUES
('Carlos', 'Pérez', '12345678', 'Calle 1', '123456789', 'carlos.perez@example.com', 'cperez', 'password123', 'cliente'),
('Ana', 'Gómez', '87654321', 'Calle 2', '987654321', 'ana.gomez@example.com', 'agomez', 'password123', 'cliente'),
('Luis', 'Martínez', '13579123', 'Calle 3', '112233445', 'luis.martinez@example.com', 'lmartinez', 'password123', 'cliente'),
('María', 'López', '24680246', 'Calle 4', '223344556', 'maria.lopez@example.com', 'mlopez', 'password123', 'cliente'),
('Juan', 'Rodríguez', '11112222', 'Calle 5', '334455667', 'juan.rodriguez@example.com', 'jrodriguez', 'password123', 'cliente'),
('Carmen', 'Torres', '33334444', 'Calle 6', '445566778', 'carmen.torres@example.com', 'ctorres', 'password123', 'cliente'),
('Pedro', 'Fernández', '55556666', 'Calle 7', '556677889', 'pedro.fernandez@example.com', 'pfernandez', 'password123', 'cliente'),
('Laura', 'Sánchez', '77778888', 'Calle 8', '667788990', 'laura.sanchez@example.com', 'lsanchez', 'password123', 'cliente'),
('Miguel', 'Ramírez', '99990000', 'Calle 9', '778899001', 'miguel.ramirez@example.com', 'mramirez', 'password123', 'cliente'),
('Sofía', 'Díaz', '10101010', 'Calle 10', '889900112', 'sofia.diaz@example.com', 'sdiaz', 'password123', 'cliente');


-- Insertar 3 empleados (1 administrador y 2 empleados)
INSERT INTO EMPLEADOS (usuario, password, rol) VALUES
('admin', 'adminpass', 'administrador'),
('empleado1', 'empleadopass1', 'empleado'),
('empleado2', 'empleadopass2', 'empleado');




SELECT c.*, c.rol AS nombreRol
FROM CLIENTES c
WHERE c.usuario = ? AND c.password = ?;



SELECT e.*, e.rol AS nombreRol
FROM EMPLEADOS e
WHERE e.usuario = ? AND e.password = ?;


SELECT * FROM CLIENTES WHERE usuario = 'cperez' AND password = 'password123';



use biblioteca;
SELECT * FROM autores;
SELECT * FROM categorias;

SELECT * FROM libros;



SELECT l.*, a.nombreAutor, a.apellidoAutor, c.nombreCategoria 
                  FROM LIBROS l
                  JOIN AUTORES a ON l.AUTORES_codAutores = a.codAutores
                  JOIN CATEGORIAS c ON l.CATEGORIAS_codCategoria = c.codCategoria