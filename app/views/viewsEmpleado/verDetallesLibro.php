<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';

// Verificar si se ha proporcionado un ID de libro
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Error: ID de libro no especificado.";
    exit();
}

// Obtener el ID del libro desde la URL
$codLibro = $_GET['id'];

// Consultar detalles del libro
$sql = "SELECT L.codLibros, L.titulo, L.ISBN, L.fechaPublicacion, L.cantidadDisponible, L.urlPortada, L.fechaPublicacion, 
               A.nombreAutor, A.apellidoAutor, C.nombreCategoria
        FROM LIBROS L
        JOIN AUTORES A ON L.AUTORES_codAutores = A.codAutores
        JOIN CATEGORIAS C ON L.CATEGORIAS_codCategoria = C.codCategoria
        WHERE L.codLibros = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $codLibro);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $libro = $result->fetch_assoc();
} else {
    echo "Error: No se ha encontrado información del libro.";
    exit();
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-base-100 text-base-content min-h-screen">



<div class="container mx-auto p-6">
    <!-- Menú de navegación -->
<?php include "../../views/viewsEmpleado/menuEmpleado.php"; ?>
    <h1 class="text-3xl font-bold mb-6">Detalles del Libro</h1>

    <div class="card lg:card-side bg-base-200 shadow-xl">
        <figure class="p-6">
            <img src="<?php echo htmlspecialchars($libro['urlPortada']); ?>" alt="Portada de <?php echo htmlspecialchars($libro['titulo']); ?>" class="rounded-xl w-64 h-96 object-cover">
        </figure>
        <div class="card-body">
            <h2 class="card-title"><?php echo htmlspecialchars($libro['titulo']); ?></h2>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($libro['nombreAutor']) . " " . htmlspecialchars($libro['apellidoAutor']); ?></p>
            <p><strong>ISBN:</strong> <?php echo htmlspecialchars($libro['ISBN']); ?></p>
            <p><strong>Fecha de Publicación:</strong> <?php echo htmlspecialchars($libro['fechaPublicacion']); ?></p>
            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($libro['nombreCategoria']); ?></p>
            <p><strong>Cantidad Disponible:</strong> <?php echo htmlspecialchars($libro['cantidadDisponible']); ?></p>

            <div class="card-actions justify-end mt-4">
                <a href="../../controllers/gestionLibros/editarLibro.php?id=<?php echo urlencode($libro['codLibros']); ?>" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
