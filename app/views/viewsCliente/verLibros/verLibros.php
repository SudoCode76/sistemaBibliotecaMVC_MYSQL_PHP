<?php
require_once __DIR__ . '/../../../config/checkSessionCliente.php';
require_once __DIR__ . '/../../../config/conexion.php';

// Verificar que la conexión se estableció correctamente
if (!isset($conexion)) {
    die("Error: No se pudo establecer la conexión con la base de datos.");
}

// Obtener el parámetro de búsqueda y categoría
$search = $_GET['search'] ?? '';
$categoriaSeleccionada = $_GET['categoria'] ?? '';

// Obtener las categorías para el filtro
$categorias = [];
$queryCategorias = "SELECT codCategoria, nombreCategoria FROM CATEGORIAS";
$resultCategorias = $conexion->query($queryCategorias);
while ($row = $resultCategorias->fetch_assoc()) {
    $categorias[] = $row;
}
$resultCategorias->free();

// Consulta a la base de datos para obtener los libros
$libros = [];
$query = "SELECT L.codLibros, L.titulo, L.urlPortada, L.cantidadDisponible, 
                 A.nombreAutor, A.apellidoAutor, C.nombreCategoria
          FROM LIBROS L
          JOIN AUTORES A ON L.AUTORES_codAutores = A.codAutores
          JOIN CATEGORIAS C ON L.CATEGORIAS_codCategoria = C.codCategoria
          WHERE (L.titulo LIKE ? OR A.nombreAutor LIKE ?)";
if ($categoriaSeleccionada) {
    $query .= " AND C.codCategoria = ?";
}
$stmt = $conexion->prepare($query);
$searchParam = '%' . $search . '%';

if ($categoriaSeleccionada) {
    $stmt->bind_param("ssi", $searchParam, $searchParam, $categoriaSeleccionada);
} else {
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
    $result->free();
} else {
    echo "Error en la consulta: " . $conexion->error;
}
$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros Disponibles - Biblioteca</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-100 text-base-content transition-colors duration-300">
    <div class="min-h-screen text-base-content">
        <div class="container mx-auto p-6">
            <?php include "../menuCliente.php"; ?>

            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-primary dark:text-primary-content">Libros Disponibles</h1>
            </div>
            
            <!-- Barra de búsqueda y filtro de categorías -->
            <form method="GET" action="verLibros.php" class="flex flex-col sm:flex-row justify-center items-center mb-8 space-y-4 sm:space-y-0 sm:space-x-4">
                <input type="text" name="search" placeholder="Buscar libros..." value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" class="input input-bordered w-full max-w-xs bg-base-200 text-base-content">
                
                <!-- Filtro de categorías -->
                <select name="categoria" class="select select-bordered w-full max-w-xs bg-base-200 text-base-content">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['codCategoria']; ?>" <?php echo ($categoriaSeleccionada == $categoria['codCategoria']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombreCategoria'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">Buscar</button>
            </form>

            <!-- Mostrar los libros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php if (!empty($libros) && is_array($libros)): ?>
                    <?php foreach ($libros as $libro): ?>
                        <div class="card bg-base-100 shadow-lg rounded-lg overflow-hidden">
                            <figure class="px-6 pt-6">
                                <img src="<?php echo htmlspecialchars($libro['urlPortada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="Portada de <?php echo htmlspecialchars($libro['titulo'] ?? 'Libro', ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="rounded-lg shadow-md max-h-48 object-cover w-full" />
                            </figure>
                            <div class="card-body text-center p-6">
                                <h2 class="card-title text-2xl font-bold text-primary dark:text-primary-content mb-2"><?php echo htmlspecialchars($libro['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p class="text-base-content opacity-80">Autor: <?php echo htmlspecialchars(($libro['nombreAutor'] ?? '') . ' ' . ($libro['apellidoAutor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-base-content opacity-80">Categoría: <?php echo htmlspecialchars($libro['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="font-semibold mt-2 text-base-content">Disponibles: <?php echo intval($libro['cantidadDisponible'] ?? 0); ?></p>
                                <div class="card-actions mt-4">
                                    <a href="detalleLibro.php?id=<?php echo urlencode($libro['codLibros'] ?? ''); ?>" class="btn btn-primary w-full">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-lg text-base-content opacity-60">No hay libros disponibles en este momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
