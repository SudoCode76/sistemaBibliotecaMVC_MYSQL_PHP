<?php
include '../../config/conexion.php';

$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '';
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '';

// Consulta para obtener las devoluciones según el rango de fechas
$sql = "SELECT P.codPrestamos, C.nombre AS nombreCliente, C.apellido AS apellidoCliente, L.titulo AS tituloLibro, P.fechaDevolucion
        FROM PRESTAMOS P
        JOIN CLIENTES C ON P.USUARIOS_codUsuarios = C.codUsuarios
        JOIN LIBROS L ON P.LIBROS_codLibros = L.codLibros
        WHERE P.estado = 'devuelto'";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $sql .= " AND P.fechaDevolucion BETWEEN ? AND ?";
}

$stmt = $conexion->prepare($sql);
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $stmt->bind_param("ss", $fechaInicio, $fechaFin);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devoluciones</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="min-h-screen bg-base-100 text-base-content">
    <div class="container mx-auto p-4">

        <?php include "../viewsEmpleado/menuEmpleado.php"; ?>

        <div class="bg-base-200 p-6 rounded-box shadow-lg">
            <h1 class="text-3xl font-bold mb-6">Devoluciones</h1>

            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col sm:flex-row gap-4 mb-6">
                <div class="form-control flex-grow">
                    <label for="fechaInicio" class="label">Fecha de Inicio:</label>
                    <input type="date" class="input input-bordered" id="fechaInicio" name="fechaInicio" value="<?php echo htmlspecialchars($fechaInicio); ?>" required>
                </div>

                <div class="form-control flex-grow">
                    <label for="fechaFin" class="label">Fecha de Fin:</label>
                    <input type="date" class="input input-bordered" id="fechaFin" name="fechaFin" value="<?php echo htmlspecialchars($fechaFin); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary mt-6 sm:mt-0">Filtrar</button>
                <a href="exportarPDF.php?fechaInicio=<?php echo urlencode($fechaInicio); ?>&fechaFin=<?php echo urlencode($fechaFin); ?>" target="_blank" class="btn btn-secondary mt-6 sm:mt-0">Exportar a PDF</a>
            </form>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Código Préstamo</th>
                            <th>Cliente</th>
                            <th>Título del Libro</th>
                            <th>Fecha de Devolución</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["codPrestamos"]) . "</td>
                                        <td>" . htmlspecialchars($row["nombreCliente"]) . " " . htmlspecialchars($row["apellidoCliente"]) . "</td>
                                        <td>" . htmlspecialchars($row["tituloLibro"]) . "</td>
                                        <td>" . htmlspecialchars($row["fechaDevolucion"]) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No hay devoluciones registradas en este rango de fechas.</td></tr>";
                        }
                        $stmt->close();
                        $conexion->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
