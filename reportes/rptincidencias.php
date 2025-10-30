<?php
ob_start();
if (strlen(session_id()) < 1)
    session_start();
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else {
    if ($_SESSION['gestion_incidencias'] == 1) {
        require('PDF_MC_Table.php');
        $pdf = new PDF_MC_Table();
        $pdf->AddPage();
        $y_axis_initial = 25;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, '', 0, 0, 'C');
        $pdf->Cell(100, 6, utf8_decode('REPORTE DE INCIDENCIAS'), 1, 0, 'C');
        $pdf->Ln(10);
        $pdf->SetFillColor(232, 232, 232);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Estudiante', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, utf8_decode('Código Est.'), 1, 0, 'C', 1);
        $pdf->Cell(35, 6, 'Tipo Incidencia', 1, 0, 'C', 1);
        $pdf->Cell(30, 6, 'Fecha Registro', 1, 0, 'C', 1);
        $pdf->Cell(20, 6, 'Estado', 1, 0, 'C', 1);
        $pdf->Cell(34, 6, 'Registrado por', 1, 0, 'C', 1);
        $pdf->Ln(10);
        require_once "../modelos/Incidencia.php";
        $incidencia = new Incidencia();
        $cargo = $_SESSION['cargo'];
        $idusuario = $_SESSION['idusuario'];
        if ($cargo == 'estudiante') {
            $rspta = $incidencia->listarPorEstudiante($idusuario);
        } else {
            $rspta = $incidencia->listar();
        }
        $pdf->SetWidths(array(40, 30, 35, 30, 20, 34));
        while ($reg = $rspta->fetch_object()) {
            $estudiante = substr($reg->estudiante, 0, 25);
            $codigo_estudiante = $reg->codigo_estudiante;
            $tipo_incidencia = $reg->tipo_incidencia;
            $fecha_registro = date('d-m-Y', strtotime($reg->fecha_registro));
            $estado = '';
            switch ($reg->estado) {
                case 'pendiente':
                    $estado = 'Pendiente';
                    break;
                case 'resuelta':
                    $estado = 'Resuelta';
                    break;
                case 'cerrada':
                    $estado = 'Cerrada';
                    break;
                default:
                    break;
            }
            $usuario_registro = substr($reg->usuario_registro, 0, 20);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Row(array(utf8_decode($estudiante), utf8_decode($codigo_estudiante), utf8_decode($tipo_incidencia), $fecha_registro, utf8_decode($estado), utf8_decode($usuario_registro)));
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