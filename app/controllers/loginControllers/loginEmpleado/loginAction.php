<?php
require_once __DIR__ . '/loginController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if ($usuario && $password) {
        $controller = new LoginController();
        $user = $controller->login($usuario, $password);

        if ($user) {
            session_start();
            $_SESSION['usuario'] = $user['usuario'];
            $_SESSION['rol'] = $user['nombreRol'];
            $_SESSION['codUSUARIOS_ADMIN'] = $user['codUSUARIOS_ADMIN'];
            header("Location: ../../../views/viewsEmpleado/dashboardEmpleado.php");
            exit();
        } else {
            header("Location: ../../../views/loginViews/loginEmpleado.php?error=1");
            exit();
        }
    } else {
        header("Location: ../../../views/loginViews/loginEmpleado.php?error=1");
        exit();
    }
}
?>
