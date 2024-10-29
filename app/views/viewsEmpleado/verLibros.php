<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';

// Variables para búsqueda, filtrado y ordenamiento
$searchISBN = isset($_GET['isbn']) ? $_GET['isbn'] : '';
$categoriaFiltro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$ordenStock = isset($_GET['ordenStock']) ? $_GET['ordenStock'] : '';

// Consultar las categorías para el filtro de categoría
$categoriasResult = $conexion->query("SELECT codCategoria, nombreCategoria FROM CATEGORIAS");

// Consulta base para obtener los libros
$sql = "SELECT L.codLibros, L.titulo, L.ISBN, L.fechaPublicacion, L.cantidadDisponible, L.urlPortada, A.nombreAutor, A.apellidoAutor, C.nombreCategoria
        FROM LIBROS L
        JOIN AUTORES A ON L.AUTORES_codAutores = A.codAutores
        JOIN CATEGORIAS C ON L.CATEGORIAS_codCategoria = C.codCategoria
        WHERE 1 = 1";

// Agregar filtros de búsqueda y categoría si se proporcionan
if (!empty($searchISBN)) {
    $sql .= " AND L.ISBN LIKE '%" . $conexion->real_escape_string($searchISBN) . "%'";
}

if (!empty($categoriaFiltro)) {
    $sql .= " AND C.codCategoria = " . $conexion->real_escape_string($categoriaFiltro);
}

// Agregar orden por stock si se proporciona
if (!empty($ordenStock)) {
    if ($ordenStock == 'asc') {
        $sql .= " ORDER BY L.cantidadDisponible ASC";
    } elseif ($ordenStock == 'desc') {
        $sql .= " ORDER BY L.cantidadDisponible DESC";
    }
} else {
    // Ordenar por fecha de publicación de forma descendente por defecto (más recientes primero)
    $sql .= " ORDER BY L.fechaPublicacion DESC";
}

$result = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 text-base-content min-h-screen">

<div class="container mx-auto p-6">
    <!-- Menú de navegación -->
    <?php include "../viewsEmpleado/menuEmpleado.php"; ?>
    <div class="flex justify-end mb-4">
        <a href="../../controllers/gestionLibros/agregarLibro.php" class="btn btn-success">Agregar Libro</a>
    </div>
    <!-- Formulario de Búsqueda y Filtros -->
    <form method="get" class="mb-6 flex flex-col sm:flex-row gap-4">
        <input type="text" name="isbn" value="<?php echo htmlspecialchars($searchISBN); ?>" placeholder="Buscar por ISBN" class="input input-bordered w-full sm:w-1/3">
        <select name="categoria" class="select select-bordered w-full sm:w-1/3">
            <option value="">Todas las categorías</option>
            <?php
            if ($categoriasResult->num_rows > 0) {
                while ($categoria = $categoriasResult->fetch_assoc()) {
                    $selected = ($categoriaFiltro == $categoria['codCategoria']) ? 'selected' : '';
                    echo "<option value='" . $categoria['codCategoria'] . "' $selected>" . htmlspecialchars($categoria['nombreCategoria']) . "</option>";
                }
            }
            ?>
        </select>
        <select name="ordenStock" class="select select-bordered w-full sm:w-1/3">
            <option value="">Ordenar por fecha (más recientes primero)</option>
            <option value="asc" <?php if ($ordenStock == 'asc') echo 'selected'; ?>>Menor a mayor stock</option>
            <option value="desc" <?php if ($ordenStock == 'desc') echo 'selected'; ?>>Mayor a menor stock</option>
        </select>
        <button type="submit" class="btn btn-primary w-full sm:w-auto">Filtrar</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='card bg-white shadow-xl rounded-lg transform transition hover:-translate-y-1 hover:shadow-2xl'>
                    <figure class='px-4 pt-4'>
                        <img src='" . htmlspecialchars($row['urlPortada']) . "' alt='Portada de " . htmlspecialchars($row['titulo']) . "' class='rounded-t-xl w-full h-48 object-cover'>
                    </figure>
                    <div class='card-body p-6'>
                        <h2 class='card-title text-lg font-semibold text-indigo-600'>" . htmlspecialchars($row['titulo']) . "</h2>
                        <p class='text-sm text-gray-600'>Autor: " . htmlspecialchars($row['nombreAutor']) . " " . htmlspecialchars($row['apellidoAutor']) . "</p>
                        <p class='text-sm text-gray-600'>ISBN: " . htmlspecialchars($row['ISBN']) . "</p>
                        <p class='text-sm text-gray-600'>Categoría: " . htmlspecialchars($row['nombreCategoria']) . "</p>
                        <p class='text-sm text-gray-600'>Stock: " . htmlspecialchars($row['cantidadDisponible']) . "</p>
                        <div class='card-actions justify-end mt-4'>
                            <a href='../viewsEmpleado/verDetallesLibro.php?id=" . urlencode($row['codLibros']) . "' class='btn btn-sm bg-gradient-to-r from-indigo-500 to-blue-500 text-white'>Ver Detalles</a>
                        </div>
                    </div>
                </div>";
            }
        } else {
            echo "<p class='text-center col-span-full text-gray-700'>No hay libros disponibles en el catálogo.</p>";
        }
        $conexion->close();
        ?>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
