<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../views/loginViews/loginEmpleado.php");
    exit();
}

// Verificar que el rol sea 'administrador' o 'empleado'
if ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'empleado') {
    header("Location: ../../views/loginViews/loginEmpleado.php");
    exit();
}

// Verificar que la información del empleado esté presente en la sesión
if (!isset($_SESSION['codUSUARIOS_ADMIN'])) {
    echo "Error: No se ha encontrado la información del empleado en la sesión.";
    exit();
}
?>
