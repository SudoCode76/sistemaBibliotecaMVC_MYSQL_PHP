<?php
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';
?>
<!DOCTYPE html>
<html lang="es"> <!-- Cambiado a 'es' para español, ajusta según el idioma de tu sitio -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Libros - Biblioteca</title> <!-- Título más descriptivo -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script> <!-- Agregado Tailwind CSS -->
</head>
<body>
    <div class="min-h-screen bg-base-100 text-base-content">
        <div class="container mx-auto p-4">
            <?php include "../viewsEmpleado/menuEmpleado.php"; ?>

            <h1 class="text-3xl font-bold mb-4">Libros Disponibles</h1>
            
            <!-- Mostrar los libros -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php if (!empty($libros) && is_array($libros)): ?>
                    <?php foreach ($libros as $libro): ?>
                        <div class="card bg-base-100 shadow-xl">
                            <figure class="px-10 pt-10">
                                <img src="<?php echo htmlspecialchars($libro['urlPortada'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="Portada de <?php echo htmlspecialchars($libro['titulo'] ?? 'Libro', ENT_QUOTES, 'UTF-8'); ?>"
                                     class="rounded-xl" />
                            </figure>
                            <div class="card-body items-center text-center">
                                <h2 class="card-title"><?php echo htmlspecialchars($libro['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                                <p>Autor: <?php echo htmlspecialchars(($libro['nombreAutor'] ?? '') . ' ' . ($libro['apellidoAutor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                                <p>Categoría: <?php echo htmlspecialchars($libro['nombreCategoria'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p>Cantidad disponible: <?php echo intval($libro['cantidadDisponible'] ?? 0); ?></p>
                                <div class="card-actions">
                                    <a href="detalleLibro.php?id=<?php echo urlencode($libro['codLibro'] ?? ''); ?>" class="btn btn-primary">Ver Detalles</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-lg">No hay libros disponibles en este momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>