<?php
include '../../config/conexion.php';

// Verificar si se ha enviado el ID del usuario a eliminar por el método GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar y ejecutar la consulta SQL para eliminar el usuario
    $sql = "DELETE FROM EMPLEADOS WHERE codUSUARIOS_ADMIN=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        header("Location: ../../views/viewsEmpleado/gestionEmpleados.php");
        exit();
    } else {

        echo "Error al intentar eliminar: " . $stmt->error;
    }

    $stmt->close();
} else {

    echo "ID no especificado";
    exit();
}

$conexion->close();
?>
