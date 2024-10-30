<?php
include '../../config/conexion.php';

// Verificar si se ha enviado el ID de la reserva por el método GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener los detalles de la reserva específica
    $sql = "SELECT 
                R.codreservaCuarto,
                R.fechaReserva,
                R.horaReserva,
                R.estado,
                C.nombre AS nombreCliente,
                C.apellido AS apellidoCliente,
                C.correo,
                C.telefono,
                Q.nombreCuarto
            FROM 
                reservaCuarto R
            JOIN 
                CLIENTES C ON R.USUARIOS_codUsuarios = C.codUsuarios
            JOIN 
                cuartoEstudio Q ON R.cuartoEstudio_codCuartoEstudio = Q.codCuartoEstudio
            WHERE 
                R.codreservaCuarto = ?";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $fechaReserva = $row['fechaReserva'];
        $horaReserva = $row['horaReserva'];
        $estadoReserva = $row['estado'];
        $nombreCliente = $row['nombreCliente'];
        $apellidoCliente = $row['apellidoCliente'];
        $correoCliente = $row['correo'];
        $telefonoCliente = $row['telefono'];
        $nombreCuarto = $row['nombreCuarto'];
    } else {
        $error = "Reserva no encontrada.";
    }

    $stmt->close();
    $conexion->close();
} else {
    $error = "ID de reserva no especificado.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['estado'])) {
    include '../../config/conexion.php';
    $nuevoEstado = $_POST['estado'];
    $id = $_POST['codreservaCuarto'];

    // Actualizar el estado de la reserva
    $sql = "UPDATE reservaCuarto SET estado = ? WHERE codreservaCuarto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("si", $nuevoEstado, $id);

    if ($stmt->execute()) {
        $success = "Estado de la reserva actualizado correctamente.";
        header("refresh:2;url=../../views/viewsEmpleado/gestionCuartosEstudio.php");
    } else {
        $error = "Error al actualizar el estado: " . $stmt->error;
    }

    $stmt->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de la Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="min-h-screen bg-base-100 text-base-content">
    <div class="container mx-auto p-4">

        <div class="bg-base-200 p-6 rounded-box shadow-lg">
            <h1 class="text-3xl font-bold mb-6">Detalle de la Reserva</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert-error mb-4"><?php echo $error; ?></div>
            <?php elseif (isset($success)): ?>
                <div class="alert alert-success mb-4"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (!isset($error)): ?>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">Cliente:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($nombreCliente . ' ' . $apellidoCliente); ?>" readonly>
                        </div>

                        <div class="form-control">
                            <label class="label">Correo:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($correoCliente); ?>" readonly>
                        </div>

                        <div class="form-control">
                            <label class="label">Teléfono:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($telefonoCliente); ?>" readonly>
                        </div>

                        <div class="form-control">
                            <label class="label">Cuarto de Estudio:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($nombreCuarto); ?>" readonly>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="form-control">
                            <label class="label">Fecha de Reserva:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($fechaReserva); ?>" readonly>
                        </div>

                        <div class="form-control">
                            <label class="label">Hora de Reserva:</label>
                            <input type="text" class="input input-bordered" value="<?php echo htmlspecialchars($horaReserva); ?>" readonly>
                        </div>

                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" class="space-y-4">
                            <input type="hidden" name="codreservaCuarto" value="<?php echo htmlspecialchars($id); ?>">

                            <div class="form-control">
                                <label for="estado" class="label">Estado de la Reserva:</label>
                                <select class="select select-bordered" id="estado" name="estado" required>
                                    <option value="pendiente" <?php if ($estadoReserva == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                                    <option value="confirmada" <?php if ($estadoReserva == 'confirmada') echo 'selected'; ?>>Confirmada</option>
                                    <option value="cancelada" <?php if ($estadoReserva == 'cancelada') echo 'selected'; ?>>Cancelada</option>
                                    <option value="en uso" <?php if ($estadoReserva == 'en uso') echo 'selected'; ?>>En Uso</option>
                                    <option value="finalizada" <?php if ($estadoReserva == 'finalizada') echo 'selected'; ?>>Finalizada</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-full">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
