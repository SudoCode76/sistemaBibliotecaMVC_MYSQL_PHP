<?php
require_once __DIR__ . '/../../../config/checkSessionCliente.php';
require_once __DIR__ . '/../../../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$codUsuario = $_SESSION['codUsuarios'] ?? null;

// Verificar que el usuario está correctamente autenticado
if (!$codUsuario) {
    die("Error: Usuario no autenticado.");
}

// Consulta para obtener los préstamos del cliente
$query = "SELECT P.codPrestamos, L.titulo, P.fechaPrestamo, P.fechaDevolucion, P.estado
          FROM PRESTAMOS P
          JOIN LIBROS L ON P.LIBROS_codLibros = L.codLibros
          WHERE P.USUARIOS_codUsuarios = ?
          ORDER BY P.fechaPrestamo DESC";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $codUsuario);
$stmt->execute();
$result = $stmt->get_result();

$prestamos = [];
while ($row = $result->fetch_assoc()) {
    $prestamos[] = $row;
}
$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Préstamos</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="container mx-auto p-6">
        <?php include "../menuCliente.php"; ?>

        <h1 class="text-4xl font-bold text-center text-indigo-700 dark:text-indigo-300 mb-8">Mis Préstamos</h1>

        <?php if (!empty($prestamos)): ?>
            <div class="overflow-x-auto">
                <table class="table w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-indigo-900">
                            <th class="p-4 text-left text-gray-700 dark:text-indigo-300">Título</th>
                            <th class="p-4 text-left text-gray-700 dark:text-indigo-300">Fecha de Reserva</th>
                            <th class="p-4 text-left text-gray-700 dark:text-indigo-300">Fecha de Devolución</th>
                            <th class="p-4 text-left text-gray-700 dark:text-indigo-300">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="p-4 border-t border-gray-300 dark:border-gray-700"><?php echo htmlspecialchars($prestamo['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4 border-t border-gray-300 dark:border-gray-700"><?php echo htmlspecialchars($prestamo['fechaPrestamo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4 border-t border-gray-300 dark:border-gray-700"><?php echo htmlspecialchars($prestamo['fechaDevolucion'] ?? 'No disponible', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4 border-t border-gray-300 dark:border-gray-700">
                                    <span class="<?php echo getStatusBadge($prestamo['estado']); ?>">
                                        <?php echo htmlspecialchars($prestamo['estado'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-lg text-center text-gray-600 dark:text-gray-400">No tienes préstamos registrados.</p>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="verLibros.php" class="btn btn-secondary bg-gray-300 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-800 font-semibold px-4 py-2 rounded-full shadow-md">
                Seguir Explorando Libros
            </a>
        </div>
    </div>

    <script>
        // Función para alternar entre modo claro y oscuro
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute("data-theme");
            const newTheme = currentTheme === "light" ? "dark" : "light";
            html.setAttribute("data-theme", newTheme);
            localStorage.setItem("theme", newTheme);
        }

        // Aplicar el tema guardado en localStorage
        document.addEventListener("DOMContentLoaded", () => {
            const savedTheme = localStorage.getItem("theme") || "light";
            document.documentElement.setAttribute("data-theme", savedTheme);
        });
    </script>
</body>
</html>

<?php
// Función para asignar colores según el estado del préstamo
function getStatusBadge($estado)
{
    switch ($estado) {
        case 'reservado':
            return 'bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full';
        case 'pendiente':
            return 'bg-yellow-200 dark:bg-yellow-700 text-yellow-800 dark:text-yellow-300 px-2 py-1 rounded-full';
        case 'devuelto':
            return 'bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-300 px-2 py-1 rounded-full';
        case 'sancionado':
            return 'bg-red-200 dark:bg-red-700 text-red-800 dark:text-red-300 px-2 py-1 rounded-full';
        default:
            return 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded-full';
    }
}
?>
