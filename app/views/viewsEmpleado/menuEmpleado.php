<?php
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-100">
<div class="navbar bg-base-200 rounded-box shadow-lg mb-4">
    <div class="flex-1">
        <!-- Icono de biblioteca -->
        <a href="../viewsEmpleado/dashboardEmpleado.php" class="btn btn-ghost normal-case text-xl">
            <i class="fas fa-book"></i>
        </a>
    </div>

    <!-- Botón hamburguesa para pantallas pequeñas -->
    <div class="flex-none lg:hidden">
        <button class="btn btn-square btn-ghost" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Menú completo para pantallas grandes -->
    <div id="menuContent" class="hidden lg:flex flex-none">
        <ul class="menu menu-horizontal px-1">
            <?php
            if ($_SESSION['rol'] == 'administrador') {
                echo '<li><a href="../viewsEmpleado/verLibros.php"><i class="fas fa-book mr-2"></i>Gestion Libros</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionClientes.php"><i class="fas fa-users mr-2"></i>Cuentas Clientes</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionEmpleados.php"><i class="fas fa-user-tie mr-2"></i>Cuentas Empleados</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionPrestamos.php"><i class="fas fa-book-reader mr-2"></i>Prestamos</a></li>';
                echo '<li><a href="../viewsEmpleado/devoluciones.php"><i class="fas fa-undo mr-2"></i>Devoluciones</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionCuartosEstudio.php"><i class="fas fa-door-open mr-2"></i>Cuartos de estudio</a></li>';
            } 
            elseif ($_SESSION['rol'] == 'empleado') {
                echo '<li><a href="../viewsEmpleado/verLibros.php"><i class="fas fa-book mr-2"></i>Gestion Libros</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionClientes.php"><i class="fas fa-users mr-2"></i>Cuentas Clientes</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionPrestamos.php"><i class="fas fa-book-reader mr-2"></i>Prestamos</a></li>';
                echo '<li><a href="../viewsEmpleado/devoluciones.php"><i class="fas fa-undo mr-2"></i>Devoluciones</a></li>';
                echo '<li><a href="../viewsEmpleado/gestionCuartosEstudio.php"><i class="fas fa-door-open mr-2"></i>Cuartos de estudio</a></li>';

            } 
            ?>
        </ul>

        <!-- Ícono de perfil -->
        <a class="btn btn-ghost btn-circle" href="../viewsEmpleado/perfilEmpleado.php">
            <i class="fas fa-user"></i>
        </a>

        <!-- Botón para cambiar el tema -->
        <button class="btn btn-ghost" onclick="toggleTheme()">
            <i class="fas fa-adjust"></i>
        </button>

        <!-- Botón para cerrar sesión -->
        <a class="btn btn-ghost" href="../../controllers/loginControllers/loginEmpleado/logout.php">Cerrar sesión</a>
    </div>
</div>

<!-- Menú desplegable para pantallas pequeñas -->
<div id="mobileMenu" class="lg:hidden hidden">
    <ul class="menu p-4 bg-base-100 rounded-box">
        <?php
        if ($_SESSION['rol'] == 'administrador') {
            echo '<li><a href="GestionLibros.php"><i class="fas fa-book mr-2"></i>Gestionar Libros</a></li>';
            echo '<li><a href="gestionClientes.php"><i class="fas fa-users mr-2"></i>Cuentas Clientes</a></li>';
            echo '<li><a href="gestionEmpleados.php"><i class="fas fa-user-tie mr-2"></i>Cuentas Empleados</a></li>';
            echo '<li><a href="prestamos.php"><i class="fas fa-book-reader mr-2"></i>Prestamos</a></li>';
            echo '<li><a href="devoluciones.php"><i class="fas fa-undo mr-2"></i>Devoluciones</a></li>';
            echo '<li><a href="gestionCuartosEstudio.php"><i class="fas fa-door-open mr-2"></i>Cuartos de estudio</a></li>';
        } 
        elseif ($_SESSION['rol'] == 'empleado') {
            echo '<li><a href="gestionClientes.php"><i class="fas fa-users mr-2"></i>Cuentas Clientes</a></li>';
            echo '<li><a href="prestamos.php"><i class="fas fa-book-reader mr-2"></i>Prestamos</a></li>';
            echo '<li><a href="devoluciones.php"><i class="fas fa-undo mr-2"></i>Devoluciones</a></li>';
        } 
        ?>
        <!-- Ícono de perfil en menú móvil -->
        <li><a href="../viewsEmpleado/perfilEmpleado.php"><i class="fas fa-user mr-2"></i>Perfil</a></li>
        <li><a href="../../controllers/loginControllers/loginEmpleado/logout.php">Cerrar sesión</a></li>
    </ul>
</div>

<script src="https://cdn.tailwindcss.com"></script>

<script>
    // Función para cambiar el tema
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    // Aplicar el tema guardado o el preferido del sistema
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    });

    // Función para desplegar el menú en dispositivos móviles
    function toggleMenu() {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    }
</script>

</body>
</html>
