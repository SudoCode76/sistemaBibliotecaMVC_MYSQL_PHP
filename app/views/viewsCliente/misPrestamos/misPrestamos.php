<?php
require_once __DIR__ . '/../../../config/checkSessionCliente.php';
require_once __DIR__ . '/../../../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$codUsuario = $_SESSION['codUsuarios'] ?? null;

if (!$codUsuario) {
    die("Error: Usuario no autenticado.");
}

// Consulta para obtener los préstamos del cliente usando la nueva relación
$query = "SELECT P.codPrestamos, L.titulo, P.fechaPrestamo, P.fechaDevolucion, P.estado
          FROM PRESTAMOS P
          JOIN LIBROS_has_PRESTAMOS LP ON P.codPrestamos = LP.PRESTAMOS_codPrestamos
          JOIN LIBROS L ON LP.LIBROS_codLibros = L.codLibros
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

<body class="bg-base-100 text-base-content">
    <div class="container mx-auto p-6">
        <?php include "../menuCliente.php"; ?>

        <?php if (!empty($prestamos)): ?>
            <div class="overflow-x-auto">
                <table class="table w-full shadow-lg rounded-lg">
                    <thead>
                        <tr class="bg-primary text-primary-content">
                            <th class="p-4 text-left">Título</th>
                            <th class="p-4 text-left">Fecha de Reserva</th>
                            <th class="p-4 text-left">Fecha de Devolución</th>
                            <th class="p-4 text-left">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestamos as $prestamo): ?>
                            <tr class="hover:bg-base-200">
                                <td class="p-4"><?php echo htmlspecialchars($prestamo['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($prestamo['fechaPrestamo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4"><?php echo htmlspecialchars($prestamo['fechaDevolucion'] ?? 'No disponible', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="p-4">
                                    <span class="badge <?php echo getStatusBadge($prestamo['estado']); ?>">
                                        <?php echo htmlspecialchars($prestamo['estado'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-lg text-center text-gray-500">No tienes préstamos registrados.</p>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="verLibros.php" class="btn btn-secondary">Seguir Explorando Libros</a>
        </div>
    </div>

    <script>
        // Alternar entre modo claro y oscuro
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
// Función para asignar colores de DaisyUI según el estado del préstamo
function getStatusBadge($estado)
{
    switch ($estado) {
        case 'reservado':
            return 'badge-info';
        case 'pendiente':
            return 'badge-warning';
        case 'devuelto':
            return 'badge-success';
        case 'sancionado':
            return 'badge-error';
        default:
            return 'badge-ghost';
    }
}
?>
