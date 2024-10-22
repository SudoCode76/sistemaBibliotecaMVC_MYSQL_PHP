<?php
include '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreUsuario = $_POST['usuario'];
    $contrasenia = $_POST['password'];
    $rol = $_POST['rol'];

    $sql = "INSERT INTO EMPLEADOS (usuario, password, rol) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sss", $nombreUsuario, $contrasenia, $rol);

    if ($stmt->execute()) {
        echo "Nuevo dato agregado";
        header("Location: ../../views/viewsEmpleado/gestionEmpleados.php");
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
    <title>Añadir Nueva Prenda</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container mx-auto mt-5">
    <h1 class="text-2xl font-bold mb-5">Añadir nuevo dato</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">

        <div class="form-control">
            <label for="usuario" class="label">Nombre Usuario:</label>
            <input type="text" class="input input-bordered" id="usuario" name="usuario" required>
        </div>

        <div class="form-control">
            <label for="password" class="label">Contraseña:</label>
            <input type="password" class="input input-bordered" id="password" name="password" required>
        </div>

        <div class="form-control">
            <label for="rol" class="label">Rol:</label>
            <select class="select select-bordered" id="rol" name="rol" required>
                <option value="Administrador">Administrador</option>
                <option value="Empleado">Empleado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Añadir</button>
    </form>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
