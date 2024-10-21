<?php

require_once __DIR__ . '/../config/conexion.php';

class LibroModel {
    private $conexion;

    public function __construct(mysqli $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function obtenerLibros(): array {
        $query = "SELECT l.*, a.nombreAutor, a.apellidoAutor, c.nombreCategoria 
                  FROM LIBROS l
                  JOIN AUTORES a ON l.AUTORES_codAutores = a.codAutores
                  JOIN CATEGORIAS c ON l.CATEGORIAS_codCategoria = c.codCategoria";
        
        try {
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $libros = [];
            while ($row = $resultado->fetch_assoc()) {
                $libros[] = $row;
            }
            
            $stmt->close();
            
            return $libros;
        } catch (mysqli_sql_exception $e) {
            throw new Exception("Error al obtener libros: " . $e->getMessage());
        }
    }
}