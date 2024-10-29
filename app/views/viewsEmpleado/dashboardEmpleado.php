<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';

// Consultar métricas rápidas
$totalClientes = $conexion->query("SELECT COUNT(*) as total FROM CLIENTES")->fetch_assoc()['total'];
$totalEmpleados = $conexion->query("SELECT COUNT(*) as total FROM EMPLEADOS")->fetch_assoc()['total'];
$prestamosPendientes = $conexion->query("SELECT COUNT(*) as total FROM PRESTAMOS WHERE estado = 'pendiente'")->fetch_assoc()['total'];
$devolucionesPendientes = $conexion->query("SELECT COUNT(*) as total FROM PRESTAMOS WHERE estado = 'devuelto'")->fetch_assoc()['total'];
$totalReservas = $conexion->query("SELECT COUNT(*) as total FROM reservaCuarto")->fetch_assoc()['total'];

// Consultar datos para gráficos
$prestamosPorMes = $conexion->query("SELECT MONTH(fechaPrestamo) as mes, COUNT(*) as total FROM PRESTAMOS GROUP BY mes")->fetch_all(MYSQLI_ASSOC);
$devolucionesPorMes = $conexion->query("SELECT MONTH(fechaDevolucion) as mes, COUNT(*) as total FROM PRESTAMOS WHERE estado = 'devuelto' GROUP BY mes")->fetch_all(MYSQLI_ASSOC);

// Datos para ocupación de cuartos de estudio
$reservasPorEstado = $conexion->query("SELECT estado, COUNT(*) as total FROM reservaCuarto GROUP BY estado")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Empleado</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-base-100 text-base-content min-h-scree">



    <div class="container mx-auto p-6">
        <!-- Menú de navegación -->
        <?php include "../viewsEmpleado/menuEmpleado.php"; ?>



        <!-- Métricas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card bg-gradient-to-r from-blue-500 to-indigo-600 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-users fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Clientes Registrados</h2>
                        <p class="text-3xl font-bold"><?php echo $totalClientes; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-teal-500 to-green-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-user-tie fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Empleados Registrados</h2>
                        <p class="text-3xl font-bold"><?php echo $totalEmpleados; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-book-open fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Préstamos Pendientes</h2>
                        <p class="text-3xl font-bold"><?php echo $prestamosPendientes; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-pink-500 to-red-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-undo fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Devoluciones Pendientes</h2>
                        <p class="text-3xl font-bold"><?php echo $devolucionesPendientes; ?></p>
                    </div>
                </div>
            </div>
            <div class="card bg-gradient-to-r from-purple-500 to-indigo-500 text-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-door-open fa-2x"></i>
                    <div>
                        <h2 class="text-lg font-semibold">Reservas de Cuartos</h2>
                        <p class="text-3xl font-bold"><?php echo $totalReservas; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Préstamos y Devoluciones -->
        <div class="card bg-base-300 p-6 mb-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-indigo-600 mb-4">Préstamos y Devoluciones por Mes</h2>
            <div class="flex justify-center">
                <canvas id="prestamosDevolucionesChart" class="w-full max-w-4xl h-64"></canvas>
            </div>
        </div>

        <!-- Gráfico de Ocupación de Cuartos de Estudio -->
        <div class="card bg-base-300 p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-indigo-600 mb-4">Ocupación de Cuartos de Estudio</h2>
            <div class="flex justify-center">
                <canvas id="ocupacionCuartosChart" class="w-full max-w-md h-64"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Datos para el gráfico de Préstamos y Devoluciones
        const prestamosData = <?php echo json_encode($prestamosPorMes); ?>;
        const devolucionesData = <?php echo json_encode($devolucionesPorMes); ?>;

        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const prestamosPorMes = Array(12).fill(0);
        const devolucionesPorMes = Array(12).fill(0);

        prestamosData.forEach(item => {
            prestamosPorMes[item.mes - 1] = item.total;
        });

        devolucionesData.forEach(item => {
            devolucionesPorMes[item.mes - 1] = item.total;
        });

        const ctx1 = document.getElementById('prestamosDevolucionesChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                        label: 'Préstamos',
                        data: prestamosPorMes,
                        borderColor: 'rgb(75, 192, 192)',
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Devoluciones',
                        data: devolucionesPorMes,
                        borderColor: 'rgb(255, 99, 132)',
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Datos para el gráfico de Ocupación de Cuartos de Estudio
        const reservasData = <?php echo json_encode($reservasPorEstado); ?>;
        const estados = reservasData.map(item => item.estado);
        const totalPorEstado = reservasData.map(item => item.total);

        const ctx2 = document.getElementById('ocupacionCuartosChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: estados,
                datasets: [{
                    data: totalPorEstado,
                    backgroundColor: ['#1E3A8A', '#10B981', '#F59E0B', '#EF4444', '#6366F1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>