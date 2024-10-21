<?php

require_once __DIR__ . '/../models/LibroModel.php';

class LibroController {
    private $libroModel;

    public function __construct() {
        global $conexion; // Asumiendo que $conexion está disponible globalmente
        $this->libroModel = new LibroModel($conexion);
    }

    public function mostrarLibros() {
        try {
            $libros = $this->libroModel->obtenerLibros();
            
            // Depuración: Imprimir los libros en el log de errores
            error_log("Libros obtenidos: " . print_r($libros, true));
            
            if (empty($libros)) {
                error_log("No se encontraron libros en la base de datos.");
            }

            // Pasar los libros a la vista
            $this->renderView('verLibros', ['libros' => $libros]);
        } catch (Exception $e) {
            error_log("Error al obtener libros: " . $e->getMessage());
            $this->renderView('error', ['mensaje' => 'Hubo un problema al obtener los libros.']);
        }
    }

    private function renderView(string $vista, array $datos = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($datos);
        $rutaVista = __DIR__ . "/../views/viewsEmpleado/$vista.php";
        if (file_exists($rutaVista)) {
            include $rutaVista;
        } else {
            error_log("Vista no encontrada: $rutaVista");
            echo "Error: Vista no encontrada.";
        }
    }
}