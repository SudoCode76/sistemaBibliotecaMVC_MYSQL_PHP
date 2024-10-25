<?php
include '../../config/conexion.php';

// Variable para almacenar el estado de eliminación
$successMessage = '';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar y ejecutar la consulta SQL para eliminar el préstamo
    $sql = "DELETE FROM PRESTAMOS WHERE codPrestamos=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Almacenamos el mensaje de éxito en una variable
        $successMessage = "Préstamo eliminado con éxito.";
    } else {
        // Mostrar mensaje de error en caso de fallo
        echo "Error al intentar eliminar el préstamo: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "ID no especificado";
    exit();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script>
        // Función para ocultar el mensaje de éxito después de 3 segundos
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.getElementById('success-alert');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.display = 'none';
                }, 3000); // 3 segundos
            }
        });
    </script>
</head>
<body>
    <div class="container mx-auto mt-5">
        <?php if (!empty($successMessage)): ?>
            <!-- Alerta de éxito que desaparece automáticamente -->
            <div id="success-alert" class="alert alert-success shadow-lg mb-5">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span><?php echo $successMessage; ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botón para regresar a la gestión de préstamos -->
        <a href="../../views/viewsEmpleado/gestionPrestamos.php" class="btn btn-primary">Volver a Gestión de Préstamos</a>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
