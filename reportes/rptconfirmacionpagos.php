<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['confirmacion_pagos'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE CONFIRMACIÓN DE PAGOS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 6, utf8_decode('Código Reserva'), 1, 0, 'C', 1);
        $pdf->Cell(40, 6, 'Estudiante', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Fecha Reserva', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Estado Pago', 1, 0, 'C', 1);
        $pdf->Cell(45, 6, utf8_decode('Fecha Confirmación'), 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/ConfirmacionPago.php";
        $confirmacion = new ConfirmacionPago();
        $rspta = $confirmacion->listar();
        $pdf->SetWidths(array(30, 40, 30, 20, 25, 45));
        while ($reg = $rspta->fetch_object()) {
            $codigo_reserva = $reg->codigo_reserva;
            $estudiante = substr($reg->estudiante, 0, 30);
            $fecha_reserva = date('d-m-Y', strtotime($reg->fecha_reserva));
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
            $fecha_confirmacion = ($reg->fecha_confirmacion != null) ? date('d-m-Y H:i', strtotime($reg->fecha_confirmacion)) : '-';
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array(utf8_decode($codigo_reserva), utf8_decode($estudiante), $fecha_reserva, $precio, utf8_decode($estado_pago), $fecha_confirmacion));
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