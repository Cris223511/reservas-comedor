<?php
require "../config/Conexion.php";

class ConfirmacionPago
{
    public function __construct() {}

    public function confirmarPago($idreserva, $idusuario_confirma, $fecha_confirmacion, $metodo_pago, $estado_pago, $estado_reserva, $observaciones)
    {
        $sql = "UPDATE reserva SET idusuario_confirma='$idusuario_confirma',fecha_confirmacion='$fecha_confirmacion',metodo_pago='$metodo_pago',estado_pago='$estado_pago',estado_reserva='$estado_reserva',observaciones='$observaciones' WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function rechazarPago($idreserva, $idusuario_confirma, $fecha_confirmacion, $estado_pago, $estado_reserva, $fecha_cancelacion, $motivo_cancelacion, $observaciones)
    {
        $sql = "UPDATE reserva SET idusuario_confirma='$idusuario_confirma',fecha_confirmacion='$fecha_confirmacion',estado_pago='$estado_pago',estado_reserva='$estado_reserva',fecha_cancelacion='$fecha_cancelacion',motivo_cancelacion='$motivo_cancelacion',observaciones='$observaciones' WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idreserva)
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.precio,r.metodo_pago,r.estado_pago,r.estado_reserva,r.observaciones,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.idreserva='$idreserva'";
        $resultado = ejecutarConsultaSimpleFila($sql);
        if ($resultado) {
            $estado_pago_html = '';
            switch ($resultado['estado_pago']) {
                case 'pendiente':
                    $estado_pago_html = '<span class="label bg-orange">Pendiente</span>';
                    break;
                case 'confirmado':
                    $estado_pago_html = '<span class="label bg-green">Confirmado</span>';
                    break;
                case 'rechazado':
                    $estado_pago_html = '<span class="label bg-red">Rechazado</span>';
                    break;
                default:
                    break;
            }

            $estado_reserva_html = '';
            switch ($resultado['estado_reserva']) {
                case 'pendiente':
                    $estado_reserva_html = '<span class="label bg-orange">Pendiente</span>';
                    break;
                case 'confirmada':
                    $estado_reserva_html = '<span class="label bg-green">Confirmada</span>';
                    break;
                case 'cancelada':
                    $estado_reserva_html = '<span class="label bg-red">Cancelada</span>';
                    break;
                default:
                    break;
            }

            $resultado['fecha_reserva'] = date('d-m-Y', strtotime($resultado['fecha_reserva']));
            $resultado['precio'] = 'S/. ' . number_format($resultado['precio'], 2);
            $resultado['estado_pago_html'] = $estado_pago_html;
            $resultado['estado_reserva_html'] = $estado_reserva_html;

            return $resultado;
        }
        return array();
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $estadoPagoBuscar = "")
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.precio,r.estado_pago,r.estado_reserva,r.fecha_confirmacion,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE 1=1";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($estadoPagoBuscar != "") {
            $sql .= " AND r.estado_pago = '$estadoPagoBuscar'";
        }

        $sql .= " ORDER BY r.fecha_reserva DESC, r.idreserva DESC";
        return ejecutarConsulta($sql);
    }
}
