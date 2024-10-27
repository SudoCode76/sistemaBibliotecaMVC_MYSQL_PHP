<?php
include '../../config/conexion.php';
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';

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

    // Insertar los datos del nuevo libro en la base de datos
    $sqlInsert = "INSERT INTO LIBROS (titulo, ISBN, fechaPublicacion, cantidadDisponible, urlPortada, AUTORES_codAutores, CATEGORIAS_codCategoria)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conexion->prepare($sqlInsert);
    $stmtInsert->bind_param("sssissi", $titulo, $isbn, $fechaPublicacion, $cantidadDisponible, $portadaUrl, $autor, $categoria);

    if ($stmtInsert->execute()) {
        echo "Libro agregado correctamente.";
        header("Location: ../../views/viewsEmpleado/verLibros.php");
        exit();
    } else {
        echo "Error al agregar el libro: " . $stmtInsert->error;
    }
    $stmtInsert->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Libro</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css"/>
</head>
<body class="bg-base-100 text-base-content min-h-screen">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Agregar Nuevo Libro</h1>

    <div class="card lg:card-side bg-base-200 shadow-xl p-6">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control col-span-1 md:col-span-2">
                    <label for="titulo" class="label">Título:</label>
                    <input type="text" id="titulo" name="titulo" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label for="isbn" class="label">ISBN:</label>
                    <input type="text" id="isbn" name="isbn" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label for="fechaPublicacion" class="label">Fecha de Publicación:</label>
                    <input type="date" id="fechaPublicacion" name="fechaPublicacion" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label for="cantidadDisponible" class="label">Cantidad Disponible:</label>
                    <input type="number" id="cantidadDisponible" name="cantidadDisponible" class="input input-bordered" required>
                </div>

                <div class="form-control">
                    <label for="autor" class="label">Autor:</label>
                    <select id="autor" name="autor" class="select select-bordered" required>
                        <option value="" disabled selected>Seleccione un autor</option>
                        <?php
                        while ($autor = $autoresResult->fetch_assoc()) {
                            echo "<option value='" . $autor['codAutores'] . "'>" . htmlspecialchars($autor['nombreAutor']) . " " . htmlspecialchars($autor['apellidoAutor']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-control">
                    <label for="categoria" class="label">Categoría:</label>
                    <select id="categoria" name="categoria" class="select select-bordered" required>
                        <option value="" disabled selected>Seleccione una categoría</option>
                        <?php
                        while ($categoria = $categoriasResult->fetch_assoc()) {
                            echo "<option value='" . $categoria['codCategoria'] . "'>" . htmlspecialchars($categoria['nombreCategoria']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-control col-span-1 md:col-span-2">
                    <label for="portadaUrl" class="label">Portada (URL):</label>
                    <input type="url" id="portadaUrl" name="portadaUrl" class="input input-bordered">
                </div>

                <div class="form-control col-span-1 md:col-span-2">
                    <label for="portadaImagen" class="label">O subir nueva imagen:</label>
                    <input type="file" id="portadaImagen" name="portadaImagen" class="file-input file-input-bordered">
                </div>

                <div class="form-control col-span-1 md:col-span-2 flex justify-end">
                    <button type="submit" class="btn btn-primary">Agregar Libro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
</body>
</html>
