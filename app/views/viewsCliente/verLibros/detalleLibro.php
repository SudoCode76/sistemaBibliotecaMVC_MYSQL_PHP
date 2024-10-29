<?php
require_once __DIR__ . '/../../../config/checkSessionCliente.php';
require_once __DIR__ . '/../../../config/conexion.php';

// Verificar que la conexión se estableció correctamente
if (!$conexion) {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}

// Obtener el ID del libro
$libroId = $_GET['id'] ?? '';

if (empty($libroId)) {
    die("Error: No se proporcionó un ID de libro válido.");
}

// Consulta a la base de datos para obtener los detalles del libro
$query = "SELECT L.codLibros, L.titulo, L.ISBN, L.urlPortada, L.cantidadDisponible, L.fechaPublicacion,
                 A.nombreAutor, A.apellidoAutor, C.nombreCategoria, L.CATEGORIAS_codCategoria
          FROM LIBROS L
          JOIN AUTORES A ON L.AUTORES_codAutores = A.codAutores
          JOIN CATEGORIAS C ON L.CATEGORIAS_codCategoria = C.codCategoria
          WHERE L.codLibros = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $libroId);

$libro = null;
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $libro = $result->fetch_assoc();
    $result->free();
} else {
    echo "Error en la consulta: " . $conexion->error;
}
$stmt->close();

// Obtener libros relacionados basados en la misma categoría que el libro actual
$librosRelacionados = [];
if ($libro) {
    $categoriaId = $libro['CATEGORIAS_codCategoria'];

    // Consulta para obtener libros relacionados en la misma categoría que están disponibles
    $queryRelacionados = "SELECT L.codLibros, L.titulo, L.urlPortada, L.cantidadDisponible, 
                                 A.nombreAutor, A.apellidoAutor
                          FROM LIBROS L
                          JOIN AUTORES A ON L.AUTORES_codAutores = A.codAutores
                          WHERE L.CATEGORIAS_codCategoria = ? AND L.codLibros != ? AND L.cantidadDisponible > 0";
    $stmtRelacionados = $conexion->prepare($queryRelacionados);
    $stmtRelacionados->bind_param("ii", $categoriaId, $libroId);

    if ($stmtRelacionados->execute()) {
        $resultRelacionados = $stmtRelacionados->get_result();
        while ($row = $resultRelacionados->fetch_assoc()) {
            $librosRelacionados[] = $row;
        }
        $resultRelacionados->free();
    } else {
        echo "Error en la consulta de libros relacionados: " . $conexion->error;
    }
    $stmtRelacionados->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Libro - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-base-100 text-base-content transition-colors duration-300">
    <div class="min-h-screen">
        <div class="container mx-auto p-6">

            <!-- Botón para alternar entre modo claro y oscuro -->
            <div class="flex justify-end mb-4">
                <button onclick="toggleTheme()" class="btn btn-outline">Cambiar Modo</button>
            </div>

            <!-- Detalle del libro -->
            <?php if ($libro): ?>
                <div class="card lg:card-side bg-base-100 shadow-xl rounded-lg overflow-hidden my-8">
                    <figure class="p-6 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <img src="<?php echo htmlspecialchars($libro['urlPortada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            alt="Portada de <?php echo htmlspecialchars($libro['titulo'] ?? 'Libro', ENT_QUOTES, 'UTF-8'); ?>"
                            class="rounded-lg shadow-md w-48 h-64 object-cover" />
                    </figure>
                    <div class="card-body p-6">
                        <h2 class="card-title text-4xl font-bold text-primary dark:text-primary-content mb-4"><?php echo htmlspecialchars($libro['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                        <p class="text-lg"><strong>Autor:</strong> <?php echo htmlspecialchars(($libro['nombreAutor'] ?? '') . ' ' . ($libro['apellidoAutor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-lg"><strong>Categoría:</strong> <?php echo htmlspecialchars($libro['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-lg"><strong>ISBN:</strong> <?php echo htmlspecialchars($libro['ISBN'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-lg"><strong>Fecha de Publicación:</strong> <?php echo htmlspecialchars($libro['fechaPublicacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-lg"><strong>Cantidad Disponible:</strong> <?php echo intval($libro['cantidadDisponible'] ?? 0); ?></p>

                        <!-- Botones de acción -->
                        <div class="card-actions mt-6 flex space-x-4">
                            <!-- Enlace para agregar el libro al carrito de reservas -->
                            <a href="../verLibros/reservarLibro.php?id=<?php echo urlencode($libro['codLibros']); ?>" class="btn btn-primary">Reservar Libro</a>
                            <a href="verLibros.php" class="btn btn-secondary">Volver a la lista de libros</a>
                        </div>
                    </div>
                </div>

                <!-- Libros relacionados -->
                <?php if (!empty($librosRelacionados)): ?>
                    <h2 class="text-3xl font-bold text-primary dark:text-primary-content mt-12 mb-6">Libros Relacionados Disponibles</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($librosRelacionados as $libroRelacionado): ?>
                            <div class="card bg-base-100 shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                                <figure class="px-6 pt-6">
                                    <img src="<?php echo htmlspecialchars($libroRelacionado['urlPortada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        alt="Portada de <?php echo htmlspecialchars($libroRelacionado['titulo'] ?? 'Libro', ENT_QUOTES, 'UTF-8'); ?>"
                                        class="rounded-lg shadow-md w-full max-h-48 object-cover" />
                                </figure>
                                <div class="card-body text-center p-6">
                                    <h3 class="card-title text-xl font-semibold"><?php echo htmlspecialchars($libroRelacionado['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <p>Autor: <?php echo htmlspecialchars(($libroRelacionado['nombreAutor'] ?? '') . ' ' . ($libroRelacionado['apellidoAutor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                    <div class="card-actions mt-4">
                                        <a href="detalleLibro.php?id=<?php echo urlencode($libroRelacionado['codLibros'] ?? ''); ?>" class="btn btn-primary">Ver Detalles</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-center text-lg">No se encontraron detalles para este libro.</p>
            <?php endif; ?>
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