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
    FOREIGN KEY (CATEGORIAS_codCategoria) REFERENCES CATEGORIAS (codCategoria),
    FOREIGN KEY (AUTORES_codAutores) REFERENCES AUTORES (codAutores)
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
    FOREIGN KEY (LIBROS_codLibros) REFERENCES LIBROS (codLibros),
    FOREIGN KEY (USUARIOS_codUsuarios) REFERENCES CLIENTES (codUsuarios),
    FOREIGN KEY (SANCIONES_codSancion) REFERENCES SANCIONES (codSancion)
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
    FOREIGN KEY (cuartoEstudio_codCuartoEstudio) REFERENCES cuartoEstudio (codCuartoEstudio),
    FOREIGN KEY (USUARIOS_codUsuarios) REFERENCES CLIENTES (codUsuarios)
);

-- Insertar 10 clientes
INSERT INTO
    CLIENTES (
        nombre,
        apellido,
        ci,
        direccion,
        telefono,
        correo,
        usuario,
        password,
        rol
    )
VALUES
    (
        'Carlos',
        'Pérez',
        '12345678',
        'Calle 1',
        '123456789',
        'carlos.perez@example.com',
        'cperez',
        'password123',
        'cliente'
    ),
    (
        'Ana',
        'Gómez',
        '87654321',
        'Calle 2',
        '987654321',
        'ana.gomez@example.com',
        'agomez',
        'password123',
        'cliente'
    ),
    (
        'Luis',
        'Martínez',
        '13579123',
        'Calle 3',
        '112233445',
        'luis.martinez@example.com',
        'lmartinez',
        'password123',
        'cliente'
    ),
    (
        'María',
        'López',
        '24680246',
        'Calle 4',
        '223344556',
        'maria.lopez@example.com',
        'mlopez',
        'password123',
        'cliente'
    ),
    (
        'Juan',
        'Rodríguez',
        '11112222',
        'Calle 5',
        '334455667',
        'juan.rodriguez@example.com',
        'jrodriguez',
        'password123',
        'cliente'
    ),
    (
        'Carmen',
        'Torres',
        '33334444',
        'Calle 6',
        '445566778',
        'carmen.torres@example.com',
        'ctorres',
        'password123',
        'cliente'
    ),
    (
        'Pedro',
        'Fernández',
        '55556666',
        'Calle 7',
        '556677889',
        'pedro.fernandez@example.com',
        'pfernandez',
        'password123',
        'cliente'
    ),
    (
        'Laura',
        'Sánchez',
        '77778888',
        'Calle 8',
        '667788990',
        'laura.sanchez@example.com',
        'lsanchez',
        'password123',
        'cliente'
    ),
    (
        'Miguel',
        'Ramírez',
        '99990000',
        'Calle 9',
        '778899001',
        'miguel.ramirez@example.com',
        'mramirez',
        'password123',
        'cliente'
    ),
    (
        'Sofía',
        'Díaz',
        '10101010',
        'Calle 10',
        '889900112',
        'sofia.diaz@example.com',
        'sdiaz',
        'password123',
        'cliente'
    );

-- Insertar 3 empleados (1 administrador y 2 empleados)
INSERT INTO
    EMPLEADOS (usuario, password, rol)
VALUES
    (
        'admin',
        'adminpass',
        'administrador'
    ),
    (
        'empleado1',
        'empleadopass1',
        'empleado'
    ),
    (
        'empleado2',
        'empleadopass2',
        'empleado'
    );


SELECT * FROM EMPLEADOS;
INSERT INTO SANCIONES (multa, fechaSancion)
VALUES
    ('50.00', '2024-01-28'),
    ('30.00', '2024-02-07'),
    ('40.00', '2024-02-15'),
    ('25.00', '2024-02-22'),
    ('60.00', '2024-03-05'),
    ('35.00', '2024-03-12');


INSERT INTO PRESTAMOS (fechaPrestamo, fechaDevolucion, estado, LIBROS_codLibros, USUARIOS_codUsuarios, SANCIONES_codSancion)
VALUES
    ('2024-01-05', '2024-01-12', 'devuelto', 1, 1, NULL),
    ('2024-01-10', '2024-01-17', 'devuelto', 2, 2, NULL),
    ('2024-01-12', '2024-01-19', 'pendiente', 3, 3, NULL),
    ('2024-01-15', '2024-01-22', 'devuelto', 4, 4, NULL),
    ('2024-01-17', '2024-01-24', 'reservado', 5, 5, NULL),
    ('2024-01-20', '2024-01-27', 'sancionado', 6, 6, 1),
    ('2024-01-22', '2024-01-29', 'devuelto', 7, 7, NULL),
    ('2024-01-25', '2024-02-01', 'pendiente', 8, 8, NULL),
    ('2024-01-27', '2024-02-03', 'devuelto', 9, 9, NULL),
    ('2024-01-30', '2024-02-06', 'sancionado', 10, 10, 2),
    ('2024-02-01', '2024-02-08', 'reservado', 11, 1, NULL),
    ('2024-02-03', '2024-02-10', 'pendiente', 12, 2, NULL),
    ('2024-02-05', '2024-02-12', 'devuelto', 13, 3, NULL),
    ('2024-02-07', '2024-02-14', 'sancionado', 14, 4, 3),
    ('2024-02-09', '2024-02-16', 'reservado', 15, 5, NULL),
    ('2024-02-11', '2024-02-18', 'devuelto', 16, 6, NULL),
    ('2024-02-13', '2024-02-20', 'pendiente', 17, 7, NULL),
    ('2024-02-15', '2024-02-22', 'devuelto', 18, 8, NULL),
    ('2024-02-17', '2024-02-24', 'sancionado', 19, 9, 4),
    ('2024-02-19', '2024-02-26', 'reservado', 20, 10, NULL),
    ('2024-02-21', '2024-02-28', 'devuelto', 21, 1, NULL),
    ('2024-02-23', '2024-03-01', 'pendiente', 22, 2, NULL),
    ('2024-02-25', '2024-03-04', 'devuelto', 23, 3, NULL),
    ('2024-02-27', '2024-03-06', 'sancionado', 24, 4, 5),
    ('2024-02-29', '2024-03-07', 'reservado', 25, 5, NULL),
    ('2024-03-01', '2024-03-08', 'pendiente', 26, 6, NULL),
    ('2024-03-03', '2024-03-10', 'devuelto', 27, 7, NULL),
    ('2024-03-05', '2024-03-12', 'sancionado', 28, 8, 6),
    ('2024-03-07', '2024-03-14', 'reservado', 29, 9, NULL),
    ('2024-03-09', '2024-03-16', 'pendiente', 30, 10, NULL);


INSERT INTO PRESTAMOS (fechaPrestamo, fechaDevolucion, estado, LIBROS_codLibros, USUARIOS_codUsuarios, SANCIONES_codSancion)
VALUES
    ('2024-03-10', '2024-03-17', 'prestado', 31, 1, NULL),
    ('2024-03-12', '2024-03-19', 'prestado', 32, 2, NULL),
    ('2024-03-14', '2024-03-21', 'prestado', 33, 3, NULL);

USE biblioteca;
SELECT 
    C.nombre AS nombreCliente,
    C.apellido AS apellidoCliente,
    P.codPrestamos,
    P.fechaPrestamo,
    P.fechaDevolucion,
    P.estado
FROM 
    PRESTAMOS P
JOIN 
    CLIENTES C 
ON 
    P.USUARIOS_codUsuarios = C.codUsuarios
    WHERE c.nombre LIKE ?;
