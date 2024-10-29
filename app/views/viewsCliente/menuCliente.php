<?php
require_once __DIR__ . '/../../config/checkSessionCliente.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body class="bg-gray-100">
<div class="navbar bg-base-200 rounded-box shadow-lg mb-4">
    <div class="flex-1">
        <!-- Icono de biblioteca -->
        <a href="/app/views/viewsCliente/dashboardCliente.php" class="btn btn-ghost normal-case text-xl">
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
            <li><a href="/app/views/viewsCliente/verLibros/verLibros.php"><i class="fas fa-book mr-2"></i>Ver Libros</a></li>
            <li><a href="/app/views/viewsCliente/misPrestamos/misPrestamos.php"><i class="fas fa-book-reader mr-2"></i>Mis Préstamos</a></li>
            <li><a href="/app/views/viewsCliente/misSanciones/misSanciones.php"><i class="fas fa-exclamation-circle mr-2"></i>Mis Sanciones</a></li>
            <li><a href="/app/views/viewsCliente/misReservasCuarto/misReservas.php"><i class="fas fa-door-open mr-2"></i>Mis Reservas de Cuartos</a></li>
        </ul>

        <!-- Ícono de carrito de compras -->
        <a class="btn btn-ghost btn-circle" href="../verLibros/reservarLibro.php">
            <i class="fas fa-shopping-cart"></i>
        </a>

        <!-- Ícono de perfil -->
        <a class="btn btn-ghost btn-circle" href="/app/views/viewsCliente/perfilCliente.php">
            <i class="fas fa-user"></i>
        </a>

        <!-- Botón para cambiar el tema -->
        <button class="btn btn-ghost" onclick="toggleTheme()">
            <i class="fas fa-adjust"></i>
        </button>

        <!-- Botón para cerrar sesión -->
        <a class="btn btn-ghost" href="../../controllers/loginControllers/loginCliente/logout.php">Cerrar sesión</a>
    </div>
</div>

<!-- Menú desplegable para pantallas pequeñas -->
<div id="mobileMenu" class="lg:hidden hidden">
    <ul class="menu p-4 bg-base-100 rounded-box">
        <li><a href="/app/views/viewsCliente/verLibros/verLibros.php"><i class="fas fa-book mr-2"></i>Ver Libros</a></li>
        <li><a href="/app/views/viewsCliente/misPrestamos/misPrestamos.php"><i class="fas fa-book-reader mr-2"></i>Mis Préstamos</a></li>
        <li><a href="/app/views/viewsCliente/misSanciones/misSanciones.php"><i class="fas fa-exclamation-circle mr-2"></i>Mis Sanciones</a></li>
        <li><a href="/app/views/viewsCliente/misReservasCuarto/misReservas.php"><i class="fas fa-door-open mr-2"></i>Mis Reservas de Cuartos</a></li>
        <!-- Ícono de carrito de compras en menú móvil -->
        <li><a href="/app/views/viewsCliente/reservarLibro.php"><i class="fas fa-shopping-cart mr-2"></i>Carrito</a></li>
        <!-- Ícono de perfil en menú móvil -->
        <li><a href="/app/views/viewsCliente/perfilCliente.php"><i class="fas fa-user mr-2"></i>Perfil</a></li>
        <li><a href="../../controllers/loginControllers/loginCliente/logout.php">Cerrar sesión</a></li>
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
