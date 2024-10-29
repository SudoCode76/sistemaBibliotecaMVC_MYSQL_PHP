<?php
include '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $estado = $_POST['estado'];
    $codPrestamos = $_POST['codPrestamos'];
    $montoSancion = isset($_POST['montoSancion']) ? $_POST['montoSancion'] : null;

    // Verificar si se está sancionando
    if ($estado == "sancionado" && !empty($montoSancion)) {
        $fechaSancion = date("Y-m-d");
        $sqlSancion = "INSERT INTO SANCIONES (multa, fechaSancion) VALUES (?, ?)";
        $stmtSancion = $conexion->prepare($sqlSancion);
        $stmtSancion->bind_param("ss", $montoSancion, $fechaSancion);
        $stmtSancion->execute();
        $stmtSancion->close();
    }

    // Actualizar el estado del préstamo
    $sql = "UPDATE PRESTAMOS SET estado=? WHERE codPrestamos=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $estado, $codPrestamos);

    if ($stmt->execute()) {
        $success = "Estado del préstamo actualizado correctamente.";
        header("refresh:2;url=../../views/viewsEmpleado/gestionPrestamos.php");
    } else {
        $error = "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener el préstamo a editar
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT 
                P.codPrestamos,
                P.estado,
                GROUP_CONCAT(L.titulo SEPARATOR ', ') AS titulo,
                L.urlPortada,
                C.nombre AS nombreCliente,
                C.apellido AS apellidoCliente,
                P.fechaPrestamo,
                P.fechaDevolucion
            FROM 
                PRESTAMOS P
            JOIN 
                CLIENTES C ON P.USUARIOS_codUsuarios = C.codUsuarios
            JOIN 
                LIBROS_has_PRESTAMOS LP ON P.codPrestamos = LP.PRESTAMOS_codPrestamos
            JOIN 
                LIBROS L ON LP.LIBROS_codLibros = L.codLibros
            WHERE 
                P.codPrestamos=?
            GROUP BY 
                P.codPrestamos;";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $estado = $row['estado'];
        $tituloLibro = $row['titulo'];
        $urlPortada = $row['urlPortada'];
        $nombreCliente = $row['nombreCliente'];
        $apellidoCliente = $row['apellidoCliente'];
        $fechaPrestamo = $row['fechaPrestamo'];
        $fechaDevolucion = $row['fechaDevolucion'];
    } else {
        $error = "Préstamo no encontrado.";
    }

    $stmt->close();
} else {
    $error = "ID de préstamo no especificado.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Estado del Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="container mx-auto mt-5">
        <h1 class="text-2xl font-bold mb-5">Editar Estado del Préstamo</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" class="grid grid-cols-1 gap-4 lg:grid-cols-2 lg:gap-8">
            <input type="hidden" name="codPrestamos" value="<?php echo htmlspecialchars($id); ?>">

            <!-- Columna izquierda -->
            <div class="space-y-4">
                <div class="form-control">
                    <label class="label">Cliente:</label>
                    <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($nombreCliente . ' ' . $apellidoCliente); ?>" readonly>
                </div>

                <div class="form-control">
                    <label class="label">Libros:</label>
                    <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($tituloLibro); ?>" readonly>
                </div>

                <div class="form-control">
                    <label class="label">Fecha Préstamo:</label>
                    <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($fechaPrestamo); ?>" readonly>
                </div>

                <div class="form-control">
                    <label class="label">Fecha Devolución:</label>
                    <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($fechaDevolucion); ?>" readonly>
                </div>
            </div>

            <!-- Columna derecha -->
            <div class="space-y-4">
                <div class="form-control">
                    <label for="urlPortada" class="label">Portada del Libro:</label>
                    <img src="<?php echo htmlspecialchars($urlPortada); ?>" alt="Portada del Libro" class="w-32 h-32 object-cover rounded">
                </div>

                <div class="form-control">
                    <label for="estado" class="label">Estado:</label>
                    <select class="select select-bordered" id="estado" name="estado" required onchange="toggleSancionField()">
                        <option value="reservado" <?php if ($estado == 'reservado') echo 'selected'; ?>>Reservado</option>
                        <option value="pendiente" <?php if ($estado == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="prestado" <?php if ($estado == 'prestado') echo 'selected'; ?>>Prestado</option>
                        <option value="devuelto" <?php if ($estado == 'devuelto') echo 'selected'; ?>>Devuelto</option>
                        <option value="sancionado" <?php if ($estado == 'sancionado') echo 'selected'; ?>>Sancionado</option>
                        <option value="cancelado" <?php if ($estado == 'cancelado') echo 'selected'; ?>>Cancelado</option>
                    </select>
                </div>

                <div class="form-control" id="sancionField" style="display: none;">
                    <label for="montoSancion" class="label">Monto de la Sanción:</label>
                    <input type="number" class="input input-bordered" id="montoSancion" name="montoSancion" step="0.01" min="0">
                </div>

                <div class="form-control">
                    <button type="submit" class="btn btn-primary w-full">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleSancionField() {
            const estado = document.getElementById('estado').value;
            const sancionField = document.getElementById('sancionField');

            if (estado === 'sancionado') {
                sancionField.style.display = 'block';
            } else {
                sancionField.style.display = 'none';
            }
        }

        // Llamamos a la función al cargar la página por si el estado ya es "sancionado"
        document.addEventListener('DOMContentLoaded', toggleSancionField);
    </script>
</body>

</html>
