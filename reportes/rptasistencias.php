<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['registro_asistencia'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE ASISTENCIAS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, utf8_decode('Código Reserva'), 1, 0, 'C', 1);
        $pdf->Cell(45, 6, 'Estudiante', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, utf8_decode('Código Est.'), 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Fecha Reserva', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, utf8_decode('Asistió'), 1, 0, 'C', 1);
        $pdf->Cell(34, 6, 'Fecha Asistencia', 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/Asistencia.php";
        $asistencia = new Asistencia();
        $cargo = $_SESSION['cargo'];
        $idusuario = $_SESSION['idusuario'];
        if ($cargo == 'estudiante') {
            $rspta = $asistencia->listarPorEstudiante($idusuario);
        } else {
            $rspta = $asistencia->listar();
        }
        $pdf->SetWidths(array(30, 45, 30, 30, 20, 34));
        while ($reg = $rspta->fetch_object()) {
            $codigo_reserva = $reg->codigo_reserva;
            $estudiante = substr($reg->estudiante, 0, 30);
            $codigo_estudiante = $reg->codigo_estudiante;
            $fecha_reserva = date('d-m-Y', strtotime($reg->fecha_reserva));
            $asistio = ($reg->asistio == 1) ? 'SI' : 'NO';
            $fecha_asistencia = ($reg->fecha_asistencia != null) ? date('d-m-Y H:i', strtotime($reg->fecha_asistencia)) : '-';
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array(utf8_decode($codigo_reserva), utf8_decode($estudiante), utf8_decode($codigo_estudiante), $fecha_reserva, utf8_decode($asistio), $fecha_asistencia));
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