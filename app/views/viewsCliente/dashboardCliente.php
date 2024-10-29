<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionCliente.php';

$clienteId = $_SESSION['codUsuarios'];

// Consultar métricas rápidas específicas del cliente actual
$prestamosActivos = $conexion->query("SELECT COUNT(*) as total FROM PRESTAMOS WHERE USUARIOS_codUsuarios = $clienteId AND estado IN ('pendiente', 'reservado')")->fetch_assoc()['total'];
$cuartosReservados = $conexion->query("SELECT COUNT(*) as total FROM reservaCuarto WHERE USUARIOS_codUsuarios = $clienteId AND estado IN ('pendiente', 'confirmada', 'en uso')")->fetch_assoc()['total'];
$sancionesActivas = $conexion->query("SELECT COUNT(*) as total FROM SANCIONES WHERE codSancion IN (SELECT SANCIONES_codSancion FROM PRESTAMOS WHERE USUARIOS_codUsuarios = $clienteId AND estado = 'sancionado')")->fetch_assoc()['total'];

// Consultar historial de préstamos
$historialPrestamos = $conexion->query("SELECT fechaPrestamo, fechaDevolucion, estado FROM PRESTAMOS WHERE USUARIOS_codUsuarios = $clienteId ORDER BY fechaPrestamo DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Consultar historial de reservas de cuartos de estudio
$historialReservas = $conexion->query("SELECT fechaReserva, horaReserva, estado FROM reservaCuarto WHERE USUARIOS_codUsuarios = $clienteId ORDER BY fechaReserva DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
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

<body class="bg-base-100 text-base-content min-h-scree">



    <div class="container mx-auto p-6">
        <!-- Menú de navegación -->
        <?php include "../viewsCliente/menuCliente.php"; ?>


        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card bg-gradient-to-r from-blue-500 to-purple-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-book fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Préstamos Activos</h2>
                        <p class="text-3xl font-bold"><?php echo $prestamosActivos; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-green-500 to-teal-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-door-open fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Cuartos Reservados</h2>
                        <p class="text-3xl font-bold"><?php echo $cuartosReservados; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-red-500 to-orange-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Sanciones Activas</h2>
                        <p class="text-3xl font-bold"><?php echo $sancionesActivas; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Préstamos -->
        <div class="card bg-base-300 p-6 mb-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-primary">Historial de Préstamos</h2>
            <table class="table-auto w-full bg-white rounded-lg overflow-hidden shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="px-4 py-2">Fecha Préstamo</th>
                        <th class="px-4 py-2">Fecha Devolución</th>
                        <th class="px-4 py-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historialPrestamos as $prestamo): ?>
                        <tr class="text-center border-b border-gray-200">
                            <td class="px-4 py-3"><?php echo $prestamo['fechaPrestamo']; ?></td>
                            <td class="px-4 py-3"><?php echo $prestamo['fechaDevolucion']; ?></td>
                            <td class="px-4 py-3"><?php echo ucfirst($prestamo['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Historial de Reservas de Cuartos -->
        <div class="card bg-base-300 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-primary">Historial de Reservas de Cuartos</h2>
            <table class="table-auto w-full bg-white rounded-lg overflow-hidden shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="px-4 py-2">Fecha Reserva</th>
                        <th class="px-4 py-2">Hora Reserva</th>
                        <th class="px-4 py-2">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historialReservas as $reserva): ?>
                        <tr class="text-center border-b border-gray-200">
                            <td class="px-4 py-3"><?php echo $reserva['fechaReserva']; ?></td>
                            <td class="px-4 py-3"><?php echo $reserva['horaReserva']; ?></td>
                            <td class="px-4 py-3"><?php echo ucfirst($reserva['estado']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>

    <script src="https://cdn.tailwindcss.com"></script>
</body>

</html>