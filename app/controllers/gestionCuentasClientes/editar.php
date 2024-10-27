<?php
include '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $ci = $_POST['ci'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $codUsuarios = $_POST['codUsuarios'];

    if (empty($nombre) || empty($apellido) || empty($ci) || empty($usuario)) {
        $error = "Por favor, complete todos los campos obligatorios correctamente.";
    } else {
        // Check if the password is not empty, update it; otherwise, keep the old password.
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);  // Hash the new password
            $sql = "UPDATE CLIENTES SET nombre=?, apellido=?, ci=?, direccion=?, telefono=?, correo=?, usuario=?, password=? WHERE codUsuarios=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssssssssi", $nombre, $apellido, $ci, $direccion, $telefono, $correo, $usuario, $hashedPassword, $codUsuarios);
        } else {
            $sql = "UPDATE CLIENTES SET nombre=?, apellido=?, ci=?, direccion=?, telefono=?, correo=?, usuario=? WHERE codUsuarios=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sssssssi", $nombre, $apellido, $ci, $direccion, $telefono, $correo, $usuario, $codUsuarios);
        }

        if ($stmt->execute()) {
            $success = "Cliente actualizado correctamente.";
            header("refresh:2;url=../../views/viewsEmpleado/gestionClientes.php");
        } else {
            $error = "Error al actualizar: " . $stmt->error;
        }

        $stmt->close();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM CLIENTES WHERE codUsuarios=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $apellido = $row['apellido'];
        $ci = $row['ci'];
        $direccion = $row['direccion'];
        $telefono = $row['telefono'];
        $correo = $row['correo'];
        $usuario = $row['usuario'];
    } else {
        $error = "Cliente no encontrado";
    }

    $stmt->close();
} else {
    $error = "ID de cliente no especificado";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-5">Editar Cliente</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" class="space-y-4">
        <input type="hidden" name="codUsuarios" value="<?php echo htmlspecialchars($id); ?>">

        <div class="form-control">
            <label for="nombre" class="label">Nombre:</label>
            <input type="text" class="input input-bordered" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
        </div>

        <div class="form-control">
            <label for="apellido" class="label">Apellido:</label>
            <input type="text" class="input input-bordered" id="apellido" name="apellido" value="<?php echo htmlspecialchars($apellido); ?>" required>
        </div>

        <div class="form-control">
            <label for="ci" class="label">CI:</label>
            <input type="text" class="input input-bordered" id="ci" name="ci" value="<?php echo htmlspecialchars($ci); ?>" required>
        </div>

        <div class="form-control">
            <label for="direccion" class="label">Dirección:</label>
            <input type="text" class="input input-bordered" id="direccion" name="direccion" value="<?php echo htmlspecialchars($direccion); ?>">
        </div>

        <div class="form-control">
            <label for="telefono" class="label">Teléfono:</label>
            <input type="text" class="input input-bordered" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
        </div>

        <div class="form-control">
            <label for="correo" class="label">Correo:</label>
            <input type="email" class="input input-bordered" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" required>
        </div>

        <div class="form-control">
            <label for="usuario" class="label">Nombre de Usuario:</label>
            <input type="text" class="input input-bordered" id="usuario" name="usuario" value="<?php echo htmlspecialchars($usuario); ?>" required>
        </div>

        <div class="form-control">
            <label for="password" class="label">Nueva Contraseña (opcional):</label>
            <input type="password" class="input input-bordered" id="password" name="password">
        </div>

        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
    </form>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
