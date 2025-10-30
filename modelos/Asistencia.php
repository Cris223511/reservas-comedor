<?php
require "../config/Conexion.php";

class Asistencia
{
    public function __construct() {}

    public function registrarAsistencia($idreserva, $idusuario_asistencia, $fecha_asistencia, $observaciones)
    {
        $sql = "UPDATE reserva SET asistio=1,fecha_asistencia='$fecha_asistencia',idusuario_asistencia='$idusuario_asistencia',observaciones='$observaciones' WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function verificarAsistenciaRegistrada($idreserva)
    {
        $sql = "SELECT * FROM reserva WHERE idreserva='$idreserva' AND asistio=1";
        $resultado = ejecutarConsulta($sql);
        if (mysqli_num_rows($resultado) > 0) {
            return true;
        }
        return false;
    }

    public function buscarReservaPorCodigo($codigo_reserva)
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.estado_reserva,r.estado_pago,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.codigo_reserva='$codigo_reserva' AND r.estado_reserva='confirmada'";
        $resultado = ejecutarConsultaSimpleFila($sql);
        if ($resultado) {
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

            $resultado['fecha_reserva'] = date('d-m-Y', strtotime($resultado['fecha_reserva']));
            $resultado['estado_reserva_html'] = $estado_reserva_html;
            $resultado['estado_pago_html'] = $estado_pago_html;

            return $resultado;
        }
        return false;
    }

    public function mostrar($idreserva)
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.estado_reserva,r.estado_pago,r.observaciones,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.idreserva='$idreserva'";
        $resultado = ejecutarConsultaSimpleFila($sql);
        if ($resultado) {
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

            $resultado['fecha_reserva'] = date('d-m-Y', strtotime($resultado['fecha_reserva']));
            $resultado['estado_reserva_html'] = $estado_reserva_html;
            $resultado['estado_pago_html'] = $estado_pago_html;

            return $resultado;
        }
        return array();
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $asistenciaBuscar = "")
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.asistio,r.fecha_asistencia,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu,u.nombre as usuario_asistencia FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu LEFT JOIN usuario u ON r.idusuario_asistencia = u.idusuario WHERE r.estado_reserva='confirmada'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($asistenciaBuscar != "") {
            $sql .= " AND r.asistio = '$asistenciaBuscar'";
        }

        $sql .= " ORDER BY r.fecha_reserva DESC, r.idreserva DESC";
        return ejecutarConsulta($sql);
    }

    public function listarPorEstudiante($idusuario, $fecha_inicio = "", $fecha_fin = "", $asistenciaBuscar = "")
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.asistio,r.fecha_asistencia,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu,u.nombre as usuario_asistencia FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu LEFT JOIN usuario u ON r.idusuario_asistencia = u.idusuario WHERE r.estado_reserva='confirmada' AND e.idusuario='$idusuario'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($asistenciaBuscar != "") {
            $sql .= " AND r.asistio = '$asistenciaBuscar'";
        }

        $sql .= " ORDER BY r.fecha_reserva DESC, r.idreserva DESC";
        return ejecutarConsulta($sql);
    }
}
