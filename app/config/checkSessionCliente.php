<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../views/loginViews/loginCliente.php");
    exit();
}

// Verificar si el rol es cliente
if ($_SESSION['rol'] != 'cliente') {
    header("Location: ../../views/loginViews/loginCliente.php");
    exit();
}

// Verificar que la información del cliente esté presente en la sesión
if (!isset($_SESSION['codUsuarios'])) {
    echo "Error: No se ha encontrado la información del cliente en la sesión.";
    exit();
}
?>
