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
        $pdf->Cell(100, 6, utf8_decode('MENÚS MÁS SOLICITADOS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(15, 6, 'Pos.', 1, 0, 'C', 1);
        $pdf->Cell(60, 6, utf8_decode('Menú'), 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Tipo', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Total Reservas', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Ingresos', 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/Reporte.php";
        $reporte = new Reporte();
        $rspta = $reporte->listarMenusMasSolicitados();
        $pdf->SetWidths(array(15, 60, 25, 25, 30, 30));
        $posicion = 1;
        while ($reg = $rspta->fetch_object()) {
            $menu = substr($reg->menu, 0, 40);
            $tipo_menu = '';
            switch ($reg->tipo_menu) {
                case 'almuerzo':
                    $tipo_menu = 'Almuerzo';
                    break;
                case 'cena':
                    $tipo_menu = 'Cena';
                    break;
                default:
                    break;
            }
            $precio = 'S/. ' . number_format($reg->precio, 2);
            $total_reservas = $reg->total_reservas;
            $ingresos = 'S/. ' . number_format($reg->ingresos_generados, 2);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array($posicion, utf8_decode($menu), utf8_decode($tipo_menu), $precio, $total_reservas, $ingresos));
            $posicion++;
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