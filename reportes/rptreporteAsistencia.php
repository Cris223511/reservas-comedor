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
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE TASA DE ASISTENCIA'), 1, 0, 'C');
        $pdf->Ln(10);
        require_once "../modelos/Reporte.php";
        $reporte = new Reporte();
        $estadisticas = $reporte->estadisticasAsistencia();
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Total Reservas Confirmadas:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $estadisticas['total_confirmadas'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Total Asistencias:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $estadisticas['total_asistencias'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Total No Asistieron:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $estadisticas['total_no_asistencias'], 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 6, 'Tasa de Asistencia:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(30, 6, $estadisticas['tasa_asistencia'] . '%', 0, 1, 'L');
        $pdf->Ln(5);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Fecha', 1, 0, 'C', 1);
        $pdf->Cell(40, 6, 'Reservas Confirmadas', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Asistencias', 1, 0, 'C', 1);
        $pdf->Cell(35, 6, 'No Asistieron', 1, 0, 'C', 1);
        $pdf->Cell(40, 6, 'Tasa Asistencia', 1, 0, 'C', 1);
        $pdf->Ln(10);
        $rspta = $reporte->listarTasaAsistencia();
        $pdf->SetWidths(array(40, 40, 30, 35, 40));
        while ($reg = $rspta->fetch_object()) {
            $fecha = date('d-m-Y', strtotime($reg->fecha));
            $total_confirmadas = $reg->total_confirmadas;
            $total_asistencias = $reg->total_asistencias;
            $total_no_asistencias = $reg->total_no_asistencias;
            $tasa = 0;
            if ($total_confirmadas > 0) {
                $tasa = round(($total_asistencias / $total_confirmadas) * 100, 2);
            }
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array($fecha, $total_confirmadas, $total_asistencias, $total_no_asistencias, $tasa . '%'));
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