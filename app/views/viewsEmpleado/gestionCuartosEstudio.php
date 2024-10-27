<?php
include '../../config/conexion.php';

// Obtener los valores del estado y el nombre del cliente desde el formulario (si existen)
$estadoFiltro = isset($_GET['estado']) ? $_GET['estado'] : '';
$busquedaCliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';

// Consulta base para obtener las reservas
$sql = "SELECT 
            R.codreservaCuarto,
            R.fechaReserva,
            R.horaReserva,
            C.nombre AS nombreCliente,
            C.apellido AS apellidoCliente,
            Q.nombreCuarto,
            R.estado
        FROM 
            reservaCuarto R
        JOIN 
            CLIENTES C ON R.USUARIOS_codUsuarios = C.codUsuarios
        JOIN 
            cuartoEstudio Q ON R.cuartoEstudio_codCuartoEstudio = Q.codCuartoEstudio
        WHERE 1=1";

// Agregar condición para el estado si se seleccionó
if (!empty($estadoFiltro)) {
    $sql .= " AND R.estado = ?";
}

// Agregar condición para la búsqueda por cliente si se ingresó un nombre
if (!empty($busquedaCliente)) {
    $sql .= " AND (C.nombre LIKE ? OR C.apellido LIKE ?)";
}

$sql .= " ORDER BY R.fechaReserva DESC, R.horaReserva DESC";

$stmt = $conexion->prepare($sql);

// Configurar los parámetros dinámicos para la consulta
if (!empty($estadoFiltro) && !empty($busquedaCliente)) {
    $busquedaClienteParam = "%" . $busquedaCliente . "%";
    $stmt->bind_param("sss", $estadoFiltro, $busquedaClienteParam, $busquedaClienteParam);
} elseif (!empty($estadoFiltro)) {
    $stmt->bind_param("s", $estadoFiltro);
} elseif (!empty($busquedaCliente)) {
    $busquedaClienteParam = "%" . $busquedaCliente . "%";
    $stmt->bind_param("ss", $busquedaClienteParam, $busquedaClienteParam);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Cuartos de Estudio</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="min-h-screen bg-base-100 text-base-content">
    <div class="container mx-auto p-4">

        <?php include "../viewsEmpleado/menuEmpleado.php"; ?>

        <div class="bg-base-200 p-6 rounded-box shadow-lg">
            <h1 class="text-3xl font-bold mb-6">Gestión de Reservas de Cuartos de Estudio</h1>

            <!-- Formulario de Filtro y Búsqueda -->
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="flex flex-col gap-4 sm:flex-row mb-6">
                <div class="form-control flex-grow">
                    <label for="estado" class="label">Filtrar por Estado:</label>
                    <select name="estado" id="estado" class="select select-bordered w-full">
                        <option value="">Todos</option>
                        <option value="pendiente" <?php if ($estadoFiltro == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="confirmada" <?php if ($estadoFiltro == 'confirmada') echo 'selected'; ?>>Confirmada</option>
                        <option value="cancelada" <?php if ($estadoFiltro == 'cancelada') echo 'selected'; ?>>Cancelada</option>
                        <option value="en uso" <?php if ($estadoFiltro == 'en uso') echo 'selected'; ?>>En Uso</option>
                        <option value="finalizada" <?php if ($estadoFiltro == 'finalizada') echo 'selected'; ?>>Finalizada</option>
                    </select>
                </div>

                <div class="form-control flex-grow">
                    <label for="cliente" class="label">Buscar por Cliente:</label>
                    <input type="text" name="cliente" id="cliente" class="input input-bordered w-full" placeholder="Nombre o apellido" value="<?php echo htmlspecialchars($busquedaCliente); ?>">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full sm:w-auto">Aplicar Filtro</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Fecha Reserva</th>
                            <th>Hora Reserva</th>
                            <th>Cliente</th>
                            <th>Cuarto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["fechaReserva"]) . "</td>
                                        <td>" . htmlspecialchars($row["horaReserva"]) . "</td>
                                        <td>" . htmlspecialchars($row["nombreCliente"]) . " " . htmlspecialchars($row["apellidoCliente"]) . "</td>
                                        <td>" . htmlspecialchars($row["nombreCuarto"]) . "</td>
                                        <td>" . htmlspecialchars($row["estado"]) . "</td>
                                        <td>
                                            <a href='../../controllers/gestionReservas/verReserva.php?id=" . urlencode($row["codreservaCuarto"]) . "' class='btn btn-info btn-sm'>Ver Reserva</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>No hay reservas registradas.</td></tr>";
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
