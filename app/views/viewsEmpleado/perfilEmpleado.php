<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';

// Obtener el ID del empleado desde la sesión
$codEmpleado = $_SESSION['codUSUARIOS_ADMIN'];
$mensaje = "";

// Consultar la información actual del empleado
$sql = "SELECT usuario, password, rol FROM EMPLEADOS WHERE codUSUARIOS_ADMIN = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $codEmpleado);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $empleado = $result->fetch_assoc();
} else {
    echo "Error: No se ha encontrado la información del empleado.";
    exit();
}

// Procesar el formulario de actualización si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $nuevaPassword = $_POST['password'];

    // Validar los datos y actualizar la información
    if (!empty($usuario)) {
        // Actualizar contraseña solo si se ha proporcionado una nueva
        if (!empty($nuevaPassword)) {
            $hashedPassword = password_hash($nuevaPassword, PASSWORD_BCRYPT);
            $sqlUpdate = "UPDATE EMPLEADOS SET usuario = ?, password = ? WHERE codUSUARIOS_ADMIN = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssi", $usuario, $hashedPassword, $codEmpleado);
        } else {
            $sqlUpdate = "UPDATE EMPLEADOS SET usuario = ? WHERE codUSUARIOS_ADMIN = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $usuario, $codEmpleado);
        }

        if ($stmtUpdate->execute()) {
            $mensaje = "Datos actualizados correctamente.";
            // Actualizar la información de la sesión si se cambió el usuario
            $_SESSION['usuario'] = $usuario;
        } else {
            $mensaje = "Error al actualizar los datos: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    } else {
        $mensaje = "Por favor, complete todos los campos obligatorios.";
    }
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-6">
    <div class="bg-base-200 p-6 rounded-box shadow-lg">
        <h1 class="text-3xl font-bold mb-6">Perfil del Empleado</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert <?php echo strpos($mensaje, 'Error') !== false ? 'alert-error' : 'alert-success'; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
            <div class="form-control">
                <label for="usuario" class="label">Usuario:</label>
                <input type="text" id="usuario" name="usuario" class="input input-bordered" value="<?php echo htmlspecialchars($empleado['usuario']); ?>" required>
            </div>

            <div class="form-control">
                <label for="password" class="label">Nueva Contraseña (opcional):</label>
                <input type="password" id="password" name="password" class="input input-bordered">
                <small class="text-gray-500">Déjelo en blanco si no desea cambiar la contraseña.</small>
            </div>

            <button type="submit" class="btn btn-primary w-full">Guardar Cambios</button>
        </form>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
