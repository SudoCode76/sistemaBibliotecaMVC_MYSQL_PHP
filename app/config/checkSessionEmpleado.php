<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../views/loginViews/loginEmpleado.php");
    exit();
}

// Verificar si el rol es cliente
if ($_SESSION['rol'] != 'administrador') {
    header("Location: ../../views/loginViews/loginEmpleado.php");
    exit();
}

// Verificar que la información del cliente esté presente en la sesión
if (!isset($_SESSION['codUSUARIOS_ADMIN'])) {
    echo "Error: No se ha encontrado la información del empleado en la sesión.";
    exit();
}
?>
