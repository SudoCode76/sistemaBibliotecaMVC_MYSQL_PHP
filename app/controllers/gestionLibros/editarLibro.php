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
$sql = "SELECT L.codLibros, L.titulo, L.ISBN, L.fechaPublicacion, L.cantidadDisponible, L.urlPortada, 
               A.codAutores, A.nombreAutor, A.apellidoAutor, C.codCategoria, C.nombreCategoria
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

// Consultar autores y categorías para llenar los select del formulario
$autoresResult = $conexion->query("SELECT codAutores, nombreAutor, apellidoAutor FROM AUTORES");
$categoriasResult = $conexion->query("SELECT codCategoria, nombreCategoria FROM CATEGORIAS");

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $isbn = $_POST['isbn'];
    $fechaPublicacion = $_POST['fechaPublicacion'];
    $cantidadDisponible = $_POST['cantidadDisponible'];
    $autor = $_POST['autor'];
    $categoria = $_POST['categoria'];
    $portadaUrl = $_POST['portadaUrl'];

    // Procesar subida de imagen si se ha cargado un archivo
    $portadaImagen = $_FILES['portadaImagen'];
    if ($portadaImagen['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $portadaImagen['tmp_name'];
        $fileName = $portadaImagen['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFileDir = '../../uploads/';
        $destPath = $uploadFileDir . $newFileName;

        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $portadaUrl = $destPath; // Usar la imagen subida en lugar de la URL proporcionada
        } else {
            echo "Error: No se pudo cargar la imagen.";
            exit();
        }
    }

    // Actualizar los datos del libro en la base de datos
    $sqlUpdate = "UPDATE LIBROS SET titulo = ?, ISBN = ?, fechaPublicacion = ?, cantidadDisponible = ?, urlPortada = ?, AUTORES_codAutores = ?, CATEGORIAS_codCategoria = ?
                  WHERE codLibros = ?";
    $stmtUpdate = $conexion->prepare($sqlUpdate);
    $stmtUpdate->bind_param("sssisiii", $titulo, $isbn, $fechaPublicacion, $cantidadDisponible, $portadaUrl, $autor, $categoria, $codLibro);

    if ($stmtUpdate->execute()) {
        header("Location: ../../views/viewsEmpleado/verLibros.php");
        exit();
    } else {
        echo "Error al actualizar el libro: " . $stmtUpdate->error;
    }
    $stmtUpdate->close();
}

$stmt->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body class="bg-base-100 text-base-content min-h-screen">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Editar Libro</h1>

    <div class="card lg:card-side bg-base-200 shadow-xl p-6">
        <figure class="p-6 flex-shrink-0">
            <img src="<?php echo htmlspecialchars($libro['urlPortada']); ?>" alt="Portada del Libro" class="w-64 h-auto object-cover rounded">
        </figure>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control col-span-1 md:col-span-2">
                    <label for="titulo" class="label">Título:</label>
                    <input type="text" id="titulo" name="titulo" class="input input-bordered" value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
                </div>

                <div class="form-control">
                    <label for="isbn" class="label">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" class="input input-bordered" value="<?php echo htmlspecialchars($libro['ISBN']); ?>" required>
                </div>

                <div class="form-control">
                    <label for="fechaPublicacion" class="label">Fecha de Publicación:</label>
                    <input type="date" id="fechaPublicacion" name="fechaPublicacion" class="input input-bordered" value="<?php echo htmlspecialchars($libro['fechaPublicacion']); ?>" required>
                </div>

                <div class="form-control">
                    <label for="cantidadDisponible" class="label">Cantidad Disponible:</label>
                    <input type="number" id="cantidadDisponible" name="cantidadDisponible" class="input input-bordered" value="<?php echo htmlspecialchars($libro['cantidadDisponible']); ?>" required>
                </div>

                <div class="form-control">
                    <label for="autor" class="label">Autor:</label>
                    <select id="autor" name="autor" class="select select-bordered" required>
                        <?php
                        while ($autor = $autoresResult->fetch_assoc()) {
                            $selected = ($libro['codAutores'] == $autor['codAutores']) ? 'selected' : '';
                            echo "<option value='" . $autor['codAutores'] . "' $selected>" . htmlspecialchars($autor['nombreAutor']) . " " . htmlspecialchars($autor['apellidoAutor']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-control">
                    <label for="categoria" class="label">Categoría:</label>
                    <select id="categoria" name="categoria" class="select select-bordered" required>
                        <?php
                        while ($categoria = $categoriasResult->fetch_assoc()) {
                            $selected = ($libro['codCategoria'] == $categoria['codCategoria']) ? 'selected' : '';
                            echo "<option value='" . $categoria['codCategoria'] . "' $selected>" . htmlspecialchars($categoria['nombreCategoria']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-control col-span-1 md:col-span-2">
                    <label for="portadaUrl" class="label">Portada (URL):</label>
                    <input type="url" id="portadaUrl" name="portadaUrl" class="input input-bordered" value="<?php echo htmlspecialchars($libro['urlPortada']); ?>">
                </div>

                <div class="form-control col-span-1 md:col-span-2">
                    <label for="portadaImagen" class="label">O subir nueva imagen:</label>
                    <input type="file" id="portadaImagen" name="portadaImagen" class="file-input file-input-bordered">
                </div>

                <div class="form-control col-span-1 md:col-span-2 flex justify-end">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
