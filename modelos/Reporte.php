<?php
require "../config/Conexion.php";

class Reporte
{
    public function __construct() {}

    public function listarReservas($fecha_inicio = "", $fecha_fin = "", $estadoPagoBuscar = "", $estadoReservaBuscar = "")
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.fecha_registro,r.precio,r.estado_pago,r.estado_reserva,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE 1=1";

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

        if ($estadoReservaBuscar != "") {
            $sql .= " AND r.estado_reserva = '$estadoReservaBuscar'";
        }

        $sql .= " ORDER BY r.fecha_reserva DESC, r.idreserva DESC";
        return ejecutarConsulta($sql);
    }

    public function listarReservasPorEstudiante($idusuario, $fecha_inicio = "", $fecha_fin = "", $estadoPagoBuscar = "", $estadoReservaBuscar = "")
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.fecha_registro,r.precio,r.estado_pago,r.estado_reserva,e.nombre as estudiante,e.codigo_estudiante,m.titulo as menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE e.idusuario='$idusuario'";

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

        if ($estadoReservaBuscar != "") {
            $sql .= " AND r.estado_reserva = '$estadoReservaBuscar'";
        }

        $sql .= " ORDER BY r.fecha_reserva DESC, r.idreserva DESC";
        return ejecutarConsulta($sql);
    }

    public function listarMenusMasSolicitados($fecha_inicio = "", $fecha_fin = "", $tipoMenuBuscar = "")
    {
        $sql = "SELECT m.idmenu,m.titulo as menu,m.tipo_menu,m.precio,COUNT(r.idreserva) as total_reservas,SUM(r.precio) as ingresos_generados FROM reserva r INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.estado_reserva='confirmada'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($tipoMenuBuscar != "") {
            $sql .= " AND m.tipo_menu = '$tipoMenuBuscar'";
        }

        $sql .= " GROUP BY m.idmenu ORDER BY total_reservas DESC, ingresos_generados DESC";
        return ejecutarConsulta($sql);
    }

    public function listarTasaAsistencia($fecha_inicio = "", $fecha_fin = "")
    {
        $sql = "SELECT r.fecha_reserva as fecha,COUNT(r.idreserva) as total_confirmadas,SUM(CASE WHEN r.asistio = 1 THEN 1 ELSE 0 END) as total_asistencias,SUM(CASE WHEN r.asistio = 0 THEN 1 ELSE 0 END) as total_no_asistencias FROM reserva r WHERE r.estado_reserva='confirmada'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        $sql .= " GROUP BY r.fecha_reserva ORDER BY r.fecha_reserva DESC";
        return ejecutarConsulta($sql);
    }

    public function estadisticasAsistencia($fecha_inicio = "", $fecha_fin = "")
    {
        $sql = "SELECT COUNT(r.idreserva) as total_confirmadas,SUM(CASE WHEN r.asistio = 1 THEN 1 ELSE 0 END) as total_asistencias,SUM(CASE WHEN r.asistio = 0 THEN 1 ELSE 0 END) as total_no_asistencias FROM reserva r WHERE r.estado_reserva='confirmada'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        $resultado = ejecutarConsultaSimpleFila($sql);
        $tasa = 0;
        if ($resultado['total_confirmadas'] > 0) {
            $tasa = round(($resultado['total_asistencias'] / $resultado['total_confirmadas']) * 100, 2);
        }

        return array(
            'total_confirmadas' => $resultado['total_confirmadas'],
            'total_asistencias' => $resultado['total_asistencias'],
            'total_no_asistencias' => $resultado['total_no_asistencias'],
            'tasa_asistencia' => $tasa
        );
    }

    public function listarIngresosPorFecha($fecha_inicio = "", $fecha_fin = "", $metodoPagoBuscar = "")
    {
        $sql = "SELECT r.fecha_reserva as fecha,COUNT(r.idreserva) as total_reservas,SUM(r.precio) as ingresos FROM reserva r WHERE r.estado_pago='confirmado'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($metodoPagoBuscar != "") {
            $sql .= " AND r.metodo_pago = '$metodoPagoBuscar'";
        }

        $sql .= " GROUP BY r.fecha_reserva ORDER BY r.fecha_reserva DESC";
        return ejecutarConsulta($sql);
    }

    public function estadisticasIngresos($fecha_inicio = "", $fecha_fin = "", $metodoPagoBuscar = "")
    {
        $sql = "SELECT COUNT(r.idreserva) as total_reservas,IFNULL(SUM(r.precio), 0) as total_ingresos FROM reserva r WHERE r.estado_pago='confirmado'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND r.fecha_reserva BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND r.fecha_reserva >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND r.fecha_reserva <= '$fecha_fin'";
        }

        if ($metodoPagoBuscar != "") {
            $sql .= " AND r.metodo_pago = '$metodoPagoBuscar'";
        }

        $resultado = ejecutarConsultaSimpleFila($sql);
        $promedio = 0;
        if ($resultado['total_reservas'] > 0) {
            $promedio = round($resultado['total_ingresos'] / $resultado['total_reservas'], 2);
        }

        return array(
            'total_reservas' => $resultado['total_reservas'],
            'total_ingresos' => number_format($resultado['total_ingresos'], 2),
            'promedio_reserva' => number_format($promedio, 2)
        );
    }
}
