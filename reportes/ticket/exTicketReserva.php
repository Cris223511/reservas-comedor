<?php
ob_start();
if (strlen(session_id()) < 1) {
    session_start();
}
if (!isset($_SESSION["nombre"])) {
    echo 'Debe ingresar al sistema correctamente para visualizar el ticket';
} else {
    if ($_SESSION['gestion_reservas'] == 1) {
        require('../modelos/Configuracion.php');
        $configuracion = new Configuracion();
        $rspta = $configuracion->mostrarSistema();
        $nombre_comedor = $rspta["nombre_comedor"];
        $universidad = $rspta["universidad"];
        $direccion = ($rspta["direccion"] == '') ? 'Sin registrar.' : $rspta["direccion"];
        $telefono = ($rspta["telefono"] == '') ? 'Sin registrar.' : number_format($rspta["telefono"], 0, '', ' ');
        $email = ($rspta["email"] == '') ? 'Sin registrar.' : $rspta["email"];

        require('../modelos/Reserva.php');
        $reserva = new Reserva();
        $rspta = $reserva->obtenerDatosTicket($_GET["id"]);
        $reg = (object) $rspta;

        require('ticket/code128.php');

        $pdf_temp = new PDF_Code128('P', 'mm', array(70, 300));
        $pdf_temp->SetFont('hypermarket', '', 7.5);

        $alturaBase = 120;
        $alturaExtra = 0;
        $saltoLinea = 2.5;

        $campos = [
            $reg->estudiante ?? '',
            $reg->codigo_estudiante ?? '',
            $reg->estudiante_email ?? '',
            $reg->estudiante_telefono ?? '',
            $reg->facultad ?? '',
            $reg->carrera ?? '',
            $reg->menu ?? '',
            $reg->menu_descripcion ?? '',
            $reg->metodo_pago ?? '',
            $reg->observaciones ?? ''
        ];

        foreach ($campos as $texto) {
            if ($texto && trim($texto) !== '') {
                $lineas = ceil($pdf_temp->GetStringWidth(mb_strtoupper($texto)) / 58);
                $alturaExtra += ($lineas * 3.5) + $saltoLinea;
            }
        }

        $alturaQR = 35;
        $espacioCreditos = 25;
        $altoTotal = $alturaBase + $alturaExtra + $espacioCreditos + $alturaQR + 10;

        if ($altoTotal < 180) $altoTotal = 180;

        $logo = $rspta["logo"];
        $ext_logo = strtolower(pathinfo($logo, PATHINFO_EXTENSION));

        $pdf = new PDF_Code128('P', 'mm', array(70, $altoTotal));
        $pdf->SetAutoPageBreak(false);
        $pdf->SetMargins(4, 10, 4);
        $pdf->AddPage();

        $estadoReservaMostrar = '';
        switch ($reg->estado_reserva) {
            case 'pendiente':
                $estadoReservaMostrar = 'pendiente';
                break;
            case 'confirmada':
                $estadoReservaMostrar = 'confirmada';
                break;
            case 'cancelada':
                $estadoReservaMostrar = 'cancelada';
                break;
            default:
                break;
        }

        $estadoPagoMostrar = '';
        switch ($reg->estado_pago) {
            case 'pendiente':
                $estadoPagoMostrar = 'pendiente';
                break;
            case 'confirmado':
                $estadoPagoMostrar = 'confirmado';
                break;
            case 'rechazado':
                $estadoPagoMostrar = 'rechazado';
                break;
            default:
                break;
        }

        $tipo_menu_mostrar = '';
        switch ($reg->tipo_menu) {
            case 'almuerzo':
                $tipo_menu_mostrar = 'Almuerzo';
                break;
            case 'cena':
                $tipo_menu_mostrar = 'Cena';
                break;
            default:
                break;
        }

        $y = $pdf->cuerpoReserva(
            4,
            $nombre_comedor,
            "TICKET DE RESERVA",
            '../files/sistema/' . $logo,
            $ext_logo,
            date('d-m-Y H:i:s'),
            $reg->codigo_reserva ?? '',
            date('d-m-Y', strtotime($reg->fecha_reserva)) ?? '',
            date('d-m-Y H:i:s', strtotime($reg->fecha_registro)) ?? '',
            $reg->estudiante ?? '',
            $reg->codigo_estudiante ?? '',
            $reg->estudiante_email ?? '',
            $reg->estudiante_telefono ?? '',
            $reg->facultad ?? '',
            $reg->carrera ?? '',
            $reg->menu ?? '',
            $reg->menu_descripcion ?? '',
            $tipo_menu_mostrar,
            'S/. ' . number_format($reg->precio, 2),
            $reg->metodo_pago ?? '',
            $estadoPagoMostrar,
            $estadoReservaMostrar,
            $reg->observaciones ?? ''
        );

        $pdf->creditos(
            $y,
            $nombre_comedor . "\n" .
                $universidad . "\n" .
                "Dirección: " . $direccion . "\n" .
                "Teléfono: " . $telefono . "\n" .
                "Email: " . $email . "\n\n"
        );

        require './ticket/phpqrcode/qrlib.php';
        $serverUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $redirectUrl = $serverUrl . $_SERVER['REQUEST_URI'];
        $filePath = './ticket/qrcode.png';
        QRcode::png($redirectUrl, $filePath, 'H', 12);
        $pdf->Image($filePath, 20, null, 30);
        unlink($filePath);

        $pdf->Output("I", "ticket_reserva_" . $reg->codigo_reserva . ".pdf", true);
    } else {
        require 'noacceso.php';
    }
}
ob_end_flush();
