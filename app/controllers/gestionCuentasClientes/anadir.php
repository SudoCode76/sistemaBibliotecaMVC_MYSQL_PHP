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
    $contrasenia = $_POST['password'];
    $rol = 'cliente'; // El rol es fijo para clientes

    $sql = "INSERT INTO CLIENTES (nombre, apellido, ci, direccion, telefono, correo, usuario, password, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssss", $nombre, $apellido, $ci, $direccion, $telefono, $correo, $usuario, $contrasenia, $rol);

    if ($stmt->execute()) {
        echo "Nuevo cliente agregado";
        header("Location: ../../views/viewsEmpleado/gestionClientes.php");
        exit();
    } else {
        echo "Error al registrar: " . $stmt->error;
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
    <title>Añadir Nuevo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-5">Añadir nuevo cliente</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">

        <div class="form-control">
            <label for="nombre" class="label">Nombre:</label>
            <input type="text" class="input input-bordered" id="nombre" name="nombre" required>
        </div>

        <div class="form-control">
            <label for="apellido" class="label">Apellido:</label>
            <input type="text" class="input input-bordered" id="apellido" name="apellido" required>
        </div>

        <div class="form-control">
            <label for="ci" class="label">CI:</label>
            <input type="text" class="input input-bordered" id="ci" name="ci" required>
        </div>

        <div class="form-control">
            <label for="direccion" class="label">Dirección:</label>
            <input type="text" class="input input-bordered" id="direccion" name="direccion" required>
        </div>

        <div class="form-control">
            <label for="telefono" class="label">Teléfono:</label>
            <input type="text" class="input input-bordered" id="telefono" name="telefono" required>
        </div>

        <div class="form-control">
            <label for="correo" class="label">Correo:</label>
            <input type="email" class="input input-bordered" id="correo" name="correo" required>
        </div>

        <div class="form-control">
            <label for="usuario" class="label">Nombre de Usuario:</label>
            <input type="text" class="input input-bordered" id="usuario" name="usuario" required>
        </div>

        <div class="form-control">
            <label for="password" class="label">Contraseña:</label>
            <input type="password" class="input input-bordered" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Añadir Cliente</button>
    </form>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
