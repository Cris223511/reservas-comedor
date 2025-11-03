<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['gestion_reservas'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE RESERVAS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(28, 6, utf8_decode('Código Res.'), 1, 0, 'C', 1);
        $pdf->Cell(35, 6, 'Estudiante', 1, 0, 'C', 1);
        $pdf->Cell(22, 6, utf8_decode('Cód. Est.'), 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Fecha Reserva', 1, 0, 'C', 1);
        $pdf->Cell(15, 6, 'Hora', 1, 0, 'C', 1);
        $pdf->Cell(18, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Estado Pago', 1, 0, 'C', 1);
        $pdf->Cell(21, 6, 'Estado Res.', 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/Reserva.php";
        $reserva = new Reserva();
        $cargo = $_SESSION['cargo'];
        $idusuario = $_SESSION['idusuario'];
        if ($cargo == 'estudiante') {
            $rspta = $reserva->listarPorEstudiante($idusuario);
        } else {
            $rspta = $reserva->listar();
        }
        $pdf->SetWidths(array(28, 35, 22, 25, 15, 18, 25, 21));
        while ($reg = $rspta->fetch_object()) {
            $codigo_reserva = $reg->codigo_reserva;
            $estudiante = substr($reg->estudiante, 0, 20);
            $codigo_estudiante = $reg->codigo_estudiante;
            $fecha_reserva = date('d-m-Y', strtotime($reg->fecha_reserva));
            $hora_reserva = date('H:i', strtotime($reg->hora_reserva));
            $precio = 'S/. ' . number_format($reg->precio, 2);
            $estado_pago = '';
            switch ($reg->estado_pago) {
                case 'pendiente':
                    $estado_pago = 'Pendiente';
                    break;
                case 'confirmado':
                    $estado_pago = 'Confirmado';
                    break;
                case 'rechazado':
                    $estado_pago = 'Rechazado';
                    break;
                default:
                    break;
            }
            $estado_reserva = '';
            switch ($reg->estado_reserva) {
                case 'pendiente':
                    $estado_reserva = 'Pendiente';
                    break;
                case 'confirmada':
                    $estado_reserva = 'Confirmada';
                    break;
                case 'cancelada':
                    $estado_reserva = 'Cancelada';
                    break;
                default:
                    break;
            }
            $pdf->SetFont('Arial', '', 9);
            $pdf->Row(array(utf8_decode($codigo_reserva), utf8_decode($estudiante), utf8_decode($codigo_estudiante), $fecha_reserva, $hora_reserva, $precio, utf8_decode($estado_pago), utf8_decode($estado_reserva)));
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