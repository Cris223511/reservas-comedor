<?php
require "../config/Conexion.php";

class Reserva
{
    public function __construct() {}

    public function insertar($idestudiante, $idmenu, $codigo_reserva, $fecha_reserva, $fecha_registro, $precio)
    {
        $sql = "INSERT INTO reserva (idestudiante,idmenu,codigo_reserva,fecha_reserva,fecha_registro,precio,estado_pago,estado_reserva,asistio) VALUES ('$idestudiante','$idmenu','$codigo_reserva','$fecha_reserva','$fecha_registro','$precio','pendiente','pendiente',0)";
        return ejecutarConsulta($sql);
    }

    public function actualizar($idreserva, $estado_pago, $estado_reserva, $metodo_pago, $comprobante_pago, $idusuario_confirma, $fecha_confirmacion, $observaciones)
    {
        $sql = "UPDATE reserva SET estado_pago='$estado_pago',estado_reserva='$estado_reserva',metodo_pago='$metodo_pago',comprobante_pago='$comprobante_pago',idusuario_confirma=" . ($idusuario_confirma ? "'$idusuario_confirma'" : "NULL") . ",fecha_confirmacion=" . ($fecha_confirmacion ? "'$fecha_confirmacion'" : "NULL") . ",observaciones='$observaciones' WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function cancelar($idreserva, $fecha_cancelacion, $motivo_cancelacion)
    {
        $sql = "UPDATE reserva SET estado_reserva='cancelada',fecha_cancelacion='$fecha_cancelacion',motivo_cancelacion='$motivo_cancelacion' WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($idreserva)
    {
        $sql = "DELETE FROM reserva WHERE idreserva='$idreserva'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idreserva)
    {
        $sql = "SELECT idreserva,idestudiante,idmenu,idusuario_confirma,codigo_reserva,fecha_reserva,fecha_registro,fecha_confirmacion,fecha_cancelacion,motivo_cancelacion,precio,metodo_pago,comprobante_pago,estado_pago,estado_reserva,asistio,fecha_asistencia,idusuario_asistencia,observaciones FROM reserva WHERE idreserva='$idreserva'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $estadoPagoBuscar = "", $estadoReservaBuscar = "")
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

    public function listarPorEstudiante($idusuario, $fecha_inicio = "", $fecha_fin = "", $estadoPagoBuscar = "", $estadoReservaBuscar = "")
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

    public function listarMenusDisponibles($fecha_minima)
    {
        $sql = "SELECT idmenu,titulo,descripcion,precio,imagen,fecha_disponible,tipo_menu FROM menu WHERE estado='activado' AND fecha_disponible >= '$fecha_minima' ORDER BY fecha_disponible ASC";
        return ejecutarConsulta($sql);
    }

    public function obtenerEstudiantePorUsuario($idusuario)
    {
        $sql = "SELECT idestudiante FROM estudiante WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function obtenerMenuInfo($idmenu)
    {
        $sql = "SELECT fecha_disponible,precio FROM menu WHERE idmenu='$idmenu'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function obtenerConfiguracionAforo()
    {
        $sql = "SELECT aforo_maximo,dias_anticipacion,horas_limite_cancelacion,hora_inicio_almuerzo,hora_fin_almuerzo,whatsapp_contacto,mensaje_whatsapp FROM configuracion_aforo WHERE idconfiguracion=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function verificarReservaActivaEstudiante($idestudiante, $fecha_reserva)
    {
        $sql = "SELECT * FROM reserva WHERE idestudiante='$idestudiante' AND fecha_reserva='$fecha_reserva' AND estado_reserva IN ('pendiente','confirmada')";
        $resultado = ejecutarConsulta($sql);
        if (mysqli_num_rows($resultado) > 0) {
            return true;
        }
        return false;
    }

    public function verificarAforoDisponible($fecha_reserva)
    {
        $config = $this->obtenerConfiguracionAforo();
        $aforo_maximo = $config['aforo_maximo'];

        $sql = "SELECT cupos_disponibles FROM control_aforo_diario WHERE fecha='$fecha_reserva'";
        $resultado = ejecutarConsultaSimpleFila($sql);

        if ($resultado) {
            if ($resultado['cupos_disponibles'] > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function actualizarControlAforo($fecha_reserva)
    {
        $config = $this->obtenerConfiguracionAforo();
        $aforo_maximo = $config['aforo_maximo'];

        $sql_verificar = "SELECT * FROM control_aforo_diario WHERE fecha='$fecha_reserva'";
        $resultado = ejecutarConsultaSimpleFila($sql_verificar);

        if ($resultado) {
            $sql = "UPDATE control_aforo_diario SET reservas_confirmadas = reservas_confirmadas + 1, cupos_disponibles = cupos_disponibles - 1, ultima_actualizacion = NOW() WHERE fecha='$fecha_reserva'";
        } else {
            $cupos_disponibles = $aforo_maximo - 1;
            $sql = "INSERT INTO control_aforo_diario (fecha,aforo_maximo,reservas_confirmadas,cupos_disponibles,ultima_actualizacion) VALUES ('$fecha_reserva','$aforo_maximo',1,'$cupos_disponibles',NOW())";
        }
        return ejecutarConsulta($sql);
    }

    public function generarCodigoReserva()
    {
        $fecha = date('Ymd');
        $sql = "SELECT MAX(CAST(SUBSTRING(codigo_reserva, 13) AS UNSIGNED)) as ultimo FROM reserva WHERE codigo_reserva LIKE 'RES-$fecha-%'";
        $resultado = ejecutarConsultaSimpleFila($sql);
        $siguiente = 1;
        if ($resultado && $resultado['ultimo']) {
            $siguiente = $resultado['ultimo'] + 1;
        }
        return 'RES-' . $fecha . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    public function obtenerReservaInfo($idreserva)
    {
        $sql = "SELECT fecha_reserva FROM reserva WHERE idreserva='$idreserva'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function obtenerDatosTicket($idreserva)
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,r.fecha_registro,r.precio,r.estado_pago,r.estado_reserva,r.metodo_pago,r.observaciones,e.nombre as estudiante,e.codigo_estudiante,e.email as estudiante_email,e.telefono as estudiante_telefono,e.facultad,e.carrera,m.titulo as menu,m.descripcion as menu_descripcion,m.tipo_menu FROM reserva r INNER JOIN estudiante e ON r.idestudiante = e.idestudiante INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.idreserva='$idreserva'";
        return ejecutarConsultaSimpleFila($sql);
    }
}
