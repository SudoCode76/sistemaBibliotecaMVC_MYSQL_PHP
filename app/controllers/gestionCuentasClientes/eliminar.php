<?php
include '../../config/conexion.php';

// Verificar si se ha enviado el ID del cliente a eliminar por el método GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Iniciar una transacción para asegurar que todas las eliminaciones se realicen correctamente
    $conexion->begin_transaction();

    try {
        // 1. Eliminar reservas de cuartos de estudio asociados al cliente
        $sqlReservasCuarto = "DELETE FROM reservaCuarto WHERE USUARIOS_codUsuarios = ?";
        $stmtReservasCuarto = $conexion->prepare($sqlReservasCuarto);
        $stmtReservasCuarto->bind_param("i", $id);
        $stmtReservasCuarto->execute();
        $reservasEliminadas = $stmtReservasCuarto->affected_rows;
        $stmtReservasCuarto->close();

        // 2. Eliminar préstamos asociados al cliente
        $sqlPrestamos = "DELETE FROM PRESTAMOS WHERE USUARIOS_codUsuarios = ?";
        $stmtPrestamos = $conexion->prepare($sqlPrestamos);
        $stmtPrestamos->bind_param("i", $id);
        $stmtPrestamos->execute();
        $prestamosEliminados = $stmtPrestamos->affected_rows;
        $stmtPrestamos->close();

        // 3. Eliminar el cliente de la tabla CLIENTES
        $sqlCliente = "DELETE FROM CLIENTES WHERE codUsuarios = ?";
        $stmtCliente = $conexion->prepare($sqlCliente);
        $stmtCliente->bind_param("i", $id);
        if ($stmtCliente->execute()) {
            $stmtCliente->close();

            // Confirmar la transacción
            $conexion->commit();

            // Mostrar los datos eliminados en un popup usando JavaScript
            echo "<script>
                alert('Cliente eliminado con éxito.\\nReservas eliminadas: $reservasEliminadas\\nPréstamos eliminados: $prestamosEliminados');
                window.location.href = '../../views/viewsEmpleado/gestionClientes.php';
            </script>";
        } else {
            throw new Exception("Error al intentar eliminar al cliente: " . $stmtCliente->error);
        }
    } catch (Exception $e) {
        // Si ocurre un error, revertir la transacción
        $conexion->rollback();
        echo "<script>
            alert('Error: " . $e->getMessage() . "');
            window.location.href = '../../views/viewsCliente/gestionClientes.php';
        </script>";
    }
} else {
    echo "<script>
        alert('ID no especificado');
        window.location.href = '../../views/viewsCliente/gestionClientes.php';
    </script>";
    exit();
}

$conexion->close();
?>
