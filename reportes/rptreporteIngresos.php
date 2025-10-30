<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['reportes'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE INGRESOS GENERADOS'), 1, 0, 'C');
        $pdf->Ln(10);
        require_once "../modelos/Reporte.php";
        $reporte = new Reporte();
        $estadisticas = $reporte->estadisticasIngresos();
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Total Ingresos:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, 'S/. ' . $estadisticas['total_ingresos'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Total Reservas Pagadas:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $estadisticas['total_reservas'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Promedio por Reserva:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, 'S/. ' . $estadisticas['promedio_reserva'], 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 6, 'Fecha', 1, 0, 'C', 1);
        $pdf->Cell(50, 6, 'Total Reservas', 1, 0, 'C', 1);
        $pdf->Cell(65, 6, utf8_decode('Ingresos del Día'), 1, 0, 'C', 1);
        $pdf->Ln(10);
        $rspta = $reporte->listarIngresosPorFecha();
        $pdf->SetWidths(array(60, 50, 65));
        while ($reg = $rspta->fetch_object()) {
            $fecha = date('d-m-Y', strtotime($reg->fecha));
            $total_reservas = $reg->total_reservas;
            $ingresos = 'S/. ' . number_format($reg->ingresos, 2);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array($fecha, $total_reservas, $ingresos));
        }
        $pdf->Output();
?>
<?php
    } else {
        echo 'No tiene permiso para visualizar el reporte';
    }
}
ob_end_flush();
?>