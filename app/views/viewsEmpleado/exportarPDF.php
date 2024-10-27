<?php
require '../../fpdf/fpdf.php';
include '../../config/conexion.php';

$fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : '';
$fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : '';

// Crear el objeto FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Reporte de Devoluciones', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Rango de fechas: ' . $fechaInicio . ' a ' . $fechaFin, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(40, 10, 'Cod. Prestamo', 1);
$pdf->Cell(60, 10, 'Cliente', 1);
$pdf->Cell(60, 10, 'Libro', 1);
$pdf->Cell(30, 10, 'Fecha Devolucion', 1);
$pdf->Ln();

$sql = "SELECT P.codPrestamos, C.nombre AS nombreCliente, C.apellido AS apellidoCliente, L.titulo AS tituloLibro, P.fechaDevolucion
        FROM PRESTAMOS P
        JOIN CLIENTES C ON P.USUARIOS_codUsuarios = C.codUsuarios
        JOIN LIBROS L ON P.LIBROS_codLibros = L.codLibros
        WHERE P.estado = 'devuelto'";

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $sql .= " AND P.fechaDevolucion BETWEEN ? AND ?";
}

$stmt = $conexion->prepare($sql);
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $stmt->bind_param("ss", $fechaInicio, $fechaFin);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(40, 10, $row['codPrestamos'], 1);
        $pdf->Cell(60, 10, utf8_decode($row['nombreCliente'] . ' ' . $row['apellidoCliente']), 1);
        $pdf->Cell(60, 10, utf8_decode($row['tituloLibro']), 1);
        $pdf->Cell(30, 10, $row['fechaDevolucion'], 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(0, 10, 'No hay devoluciones registradas en este rango de fechas.', 0, 1, 'C');
}

$stmt->close();
$conexion->close();

// Generar el archivo PDF
$pdf->Output('I', 'Devoluciones.pdf');
?>
