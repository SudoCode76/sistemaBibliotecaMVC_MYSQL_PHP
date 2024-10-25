<?php
require_once __DIR__ . '/../../config/checkSessionEmpleado.php';
require_once __DIR__ . '/../../config/conexion.php';

// Obtener parámetros de búsqueda y filtro de estado
$search = isset($_GET['search']) ? $_GET['search'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'pendiente'; // Por defecto, mostrar los "pendiente"

?>

<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>GESTIÓN DE PRÉSTAMOS</title>
</head>

<body>
    <div class="min-h-screen bg-base-100 text-base-content">
        <div class="container mx-auto p-4">

            <?php include "../viewsEmpleado/menuEmpleado.php"; ?>

            <div class="bg-base-200 p-6 rounded-box shadow-lg">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Gestión de Préstamos</h1>
                </div>

                <!-- Formulario de búsqueda y filtro por estado -->
                <form method="GET" class="flex flex-col sm:flex-row gap-4 mb-6">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Buscar por nombre de cliente" class="input input-bordered flex-grow">
                    
                    <select name="estado" class="select select-bordered">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" <?php echo $estado === 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="devuelto" <?php echo $estado === 'devuelto' ? 'selected' : ''; ?>>Devuelto</option>
                        <option value="reservado" <?php echo $estado === 'reservado' ? 'selected' : ''; ?>>Reservado</option>
                        <option value="prestado" <?php echo $estado === 'prestado' ? 'selected' : ''; ?>>Prestado</option>
                        <option value="sancionado" <?php echo $estado === 'sancionado' ? 'selected' : ''; ?>>Sancionado</option>
                    </select>

                    <button type="submit" class="btn btn-primary">Buscar</button>
                </form>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Libro</th>
                                <th>Fecha Préstamo</th>
                                <th>Fecha Devolución</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL con el filtro de búsqueda, estado y título del libro
                            $sql = "SELECT 
                                        C.nombre AS nombreCliente,
                                        C.apellido AS apellidoCliente,
                                        L.titulo AS tituloLibro,
                                        P.codPrestamos,
                                        P.fechaPrestamo,
                                        P.fechaDevolucion,
                                        P.estado
                                    FROM 
                                        PRESTAMOS P
                                    JOIN 
                                        CLIENTES C ON P.USUARIOS_codUsuarios = C.codUsuarios
                                    JOIN 
                                        LIBROS L ON P.LIBROS_codLibros = L.codLibros
                                    WHERE 
                                        C.nombre LIKE ?
                                        AND (? = '' OR P.estado = ?);";
                            
                            $stmt = $conexion->prepare($sql);
                            $searchParam = "%$search%";
                            $stmt->bind_param("sss", $searchParam, $estado, $estado);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                    <td>" . htmlspecialchars($row["nombreCliente"]) . "</td>
                                    <td>" . htmlspecialchars($row["apellidoCliente"]) . "</td>
                                    <td>" . htmlspecialchars($row["tituloLibro"]) . "</td>
                                    <td>" . htmlspecialchars($row["fechaPrestamo"]) . "</td>
                                    <td>" . htmlspecialchars($row["fechaDevolucion"]) . "</td>
                                    <td>" . htmlspecialchars($row["estado"]) . "</td>
                                    <td>
                                        <a href='../../controllers/gestionPrestamos/cambiarEstado.php?id=" . urlencode($row["codPrestamos"]) . "' class='btn btn-accent btn-md mx-2'>Cambiar Estado</a>
                                        <a href='../../controllers/gestionPrestamos/eliminarPrestamo.php?id=" . urlencode($row["codPrestamos"]) . "' class='btn btn-error btn-md mx-2'>Eliminar</a>
                                    </td>
                                  </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay préstamos</td></tr>";
                            }
                            $stmt->close();
                            $conexion->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
