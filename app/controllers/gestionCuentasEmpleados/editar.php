<?php
include '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $codUSUARIOS_ADMIN = $_POST['codUSUARIOS_ADMIN'];

    if (empty($usuario)) {
        $error = "Por favor, complete todos los campos correctamente.";
    } else {
        // Check if the password is not empty, update it; otherwise, keep the old password.
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);  // Hash the new password
            $sql = "UPDATE empleados SET usuario=?, password=?, rol=? WHERE codUSUARIOS_ADMIN=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssi", $usuario, $hashedPassword, $rol, $codUSUARIOS_ADMIN);
        } else {
            $sql = "UPDATE empleados SET usuario=?, rol=? WHERE codUSUARIOS_ADMIN=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssi", $usuario, $rol, $codUSUARIOS_ADMIN);
        }

        if ($stmt->execute()) {
            $success = "Dato actualizado correctamente.";
            header("refresh:2;url=../../views/viewsEmpleado/gestionEmpleados.php");
        } else {
            $error = "Error al actualizar: " . $stmt->error;
        }

        $stmt->close();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM EMPLEADOS WHERE codUSUARIOS_ADMIN=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $usuario = $row['usuario'];
        $rol = $row['rol'];
    } else {
        $error = "Dato no encontrado";
    }

    $stmt->close();
} else {
    $error = "ID de dato no especificado";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Dato</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-5">Editar dato</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" class="space-y-4">
        <input type="hidden" name="codUSUARIOS_ADMIN" value="<?php echo htmlspecialchars($id); ?>">

        <div class="form-control">
            <label for="usuario" class="label">Nombre de Usuario:</label>
            <input type="text" class="input input-bordered" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>
        </div>

        <div class="form-control">
            <label for="password" class="label">Nueva Contraseña (opcional):</label>
            <input type="password" class="input input-bordered" id="password" name="password">
        </div>

        <div class="form-control">
            <label for="rol" class="label">Rol:</label>
            <select class="select select-bordered" id="rol" name="rol" required>
                <option value="Administrador" <?php if ($rol == 'Administrador') echo 'selected'; ?>>Administrador</option>
                <option value="Empleado" <?php if ($rol == 'Empleado') echo 'selected'; ?>>Empleado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
