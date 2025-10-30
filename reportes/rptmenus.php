<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['gestion_menus'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('LISTA DE MENÚS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(50, 6, utf8_decode('Título'), 1, 0, 'C', 1);
        $pdf->Cell(35, 6, utf8_decode('Descripción'), 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Precio', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Fecha Disponible', 1, 0, 'C', 1);
        $pdf->Cell(25, 6, 'Tipo', 1, 0, 'C', 1);
        $pdf->Cell(29, 6, 'Estado', 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/Menu.php";
        $menu = new Menu();
        $rspta = $menu->listar();
        $pdf->SetWidths(array(50, 35, 20, 30, 25, 29));
        while ($reg = $rspta->fetch_object()) {
            $titulo = $reg->titulo;
            $descripcion = substr($reg->descripcion, 0, 50);
            $precio = 'S/. ' . number_format($reg->precio, 2);
            $fecha_disponible = date('d-m-Y', strtotime($reg->fecha_disponible));

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

            $estado = '';
            switch ($reg->estado) {
                case 'activado':
                    $estado = 'Activado';
                    break;
                case 'desactivado':
                    $estado = 'Desactivado';
                    break;
                default:
                    break;
            }

            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array(utf8_decode($titulo), utf8_decode($descripcion), $precio, $fecha_disponible, utf8_decode($tipo_menu), utf8_decode($estado)));
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