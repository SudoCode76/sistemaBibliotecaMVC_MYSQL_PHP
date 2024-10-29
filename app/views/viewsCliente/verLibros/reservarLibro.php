<?php
require_once __DIR__ . '/../../../config/checkSessionCliente.php';
require_once __DIR__ . '/../../../config/conexion.php';

// Iniciar la sesión solo si aún no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar un libro al carrito si se recibe un ID de libro
if (isset($_GET['id'])) {
    $libroId = $_GET['id'];
    if (!in_array($libroId, $_SESSION['carrito'])) {
        $_SESSION['carrito'][] = $libroId;
    }
    header("Location: reservarLibro.php"); 
    exit();
}

// Eliminar un libro del carrito si se recibe un parámetro 'remove'
if (isset($_GET['remove'])) {
    $libroId = $_GET['remove'];
    if (($key = array_search($libroId, $_SESSION['carrito'])) !== false) {
        unset($_SESSION['carrito'][$key]);
    }
    header("Location: reservarLibro.php"); 
    exit();
}

// Confirmar reserva de todos los libros en el carrito
if (isset($_POST['confirmarReserva'])) {
    $fechaDevolucion = $_POST['fechaDevolucion'] ?? null;

    // Primero, crear la reserva en la tabla PRESTAMOS
    $query = "INSERT INTO PRESTAMOS (fechaPrestamo, fechaDevolucion, estado, USUARIOS_codUsuarios)
              VALUES (CURDATE(), ?, 'reservado', ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("si", $fechaDevolucion, $_SESSION['codUsuarios']);
    $stmt->execute();
    
    // Obtener el ID de la reserva recién creada
    $codPrestamos = $stmt->insert_id;
    $stmt->close();

    // Asociar cada libro del carrito a la reserva en LIBROS_has_PRESTAMOS
    foreach ($_SESSION['carrito'] as $libroId) {
        $query = "INSERT INTO LIBROS_has_PRESTAMOS (LIBROS_codLibros, PRESTAMOS_codPrestamos)
                  VALUES (?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ii", $libroId, $codPrestamos);
        $stmt->execute();
        $stmt->close();
    }

    $_SESSION['carrito'] = [];
    $mensaje = "Reserva confirmada exitosamente con fecha de devolución: " . htmlspecialchars($fechaDevolucion);
}

// Obtener detalles de los libros en el carrito
$librosCarrito = [];
if (!empty($_SESSION['carrito'])) {
    $ids = implode(',', array_fill(0, count($_SESSION['carrito']), '?'));
    $query = "SELECT codLibros, titulo, urlPortada FROM LIBROS WHERE codLibros IN ($ids)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param(str_repeat("i", count($_SESSION['carrito'])), ...$_SESSION['carrito']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $librosCarrito[] = $row;
    }
    $stmt->close();
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Reservas</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold text-center text-indigo-600 mb-6">Carrito de Reservas</h1>

    <!-- Mensaje de confirmación -->
    <?php if (isset($mensaje)): ?>
        <div class="alert alert-success mb-4 text-center text-lg font-semibold text-green-700 bg-green-100 rounded-lg p-4 shadow-lg"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <!-- Lista de libros en el carrito -->
    <?php if (!empty($librosCarrito)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <?php foreach ($librosCarrito as $libro): ?>
                <div class="card bg-white shadow-lg rounded-lg overflow-hidden transition-shadow duration-300 hover:shadow-2xl">
                    <img src="<?php echo htmlspecialchars($libro['urlPortada'], ENT_QUOTES, 'UTF-8'); ?>" alt="Portada" class="w-full h-48 object-cover mb-4 rounded-t-lg">
                    <div class="card-body p-6 text-center">
                        <h2 class="text-2xl font-semibold text-gray-700 mb-2"><?php echo htmlspecialchars($libro['titulo'], ENT_QUOTES, 'UTF-8'); ?></h2>
                        <div class="flex justify-center space-x-2 mt-4">
                            <a href="?remove=<?php echo $libro['codLibros']; ?>" class="btn btn-error btn-sm text-white font-semibold px-4 py-2 rounded hover:bg-red-600">
                                Eliminar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulario para confirmar reserva con fecha de devolución -->
        <form method="POST" class="text-center">
            <label for="fechaDevolucion" class="block text-lg font-semibold mb-2 text-gray-600">Fecha de Devolución</label>
            <input type="date" name="fechaDevolucion" id="fechaDevolucion" required class="input input-bordered w-full max-w-xs mb-4">
            <button type="submit" name="confirmarReserva" class="btn btn-primary bg-indigo-600 hover:bg-indigo-500 font-semibold px-6 py-3 rounded-full text-white text-lg shadow-md hover:shadow-lg transition-all duration-300">
                Confirmar Reserva
            </button>
        </form>
    <?php else: ?>
        <p class="text-lg text-center text-gray-500">No hay libros en el carrito.</p>
    <?php endif; ?>

    <div class="text-center mt-6">
        <a href="verLibros.php" class="btn btn-secondary bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded-full shadow-md">
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

    // Al cargar la página, aplica el tema guardado en localStorage
    document.addEventListener("DOMContentLoaded", () => {
        const savedTheme = localStorage.getItem("theme") || "light";
        document.documentElement.setAttribute("data-theme", savedTheme);
    });
</script>
</body>
</html>
