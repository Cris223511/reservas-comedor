<?php
require "../config/Conexion.php";

class Incidencia
{
    public function __construct() {}

    public function insertar($idestudiante, $idreserva, $idusuario_registro, $tipo_incidencia, $descripcion, $fecha_registro, $fecha_resolucion, $idusuario_resolucion, $estado, $observaciones)
    {
        $sql = "INSERT INTO incidencia (idestudiante,idreserva,idusuario_registro,tipo_incidencia,descripcion,fecha_registro,fecha_resolucion,idusuario_resolucion,estado,observaciones) VALUES ('$idestudiante'," . ($idreserva ? "'$idreserva'" : "NULL") . ",'$idusuario_registro','$tipo_incidencia','$descripcion','$fecha_registro'," . ($fecha_resolucion ? "'$fecha_resolucion'" : "NULL") . "," . ($idusuario_resolucion ? "'$idusuario_resolucion'" : "NULL") . ",'$estado','$observaciones')";
        return ejecutarConsulta($sql);
    }

    public function editar($idincidencia, $idestudiante, $idreserva, $tipo_incidencia, $descripcion, $fecha_resolucion, $idusuario_resolucion, $estado, $observaciones)
    {
        $sql = "UPDATE incidencia SET idestudiante='$idestudiante',idreserva=" . ($idreserva ? "'$idreserva'" : "NULL") . ",tipo_incidencia='$tipo_incidencia',descripcion='$descripcion',fecha_resolucion=" . ($fecha_resolucion ? "'$fecha_resolucion'" : "NULL") . ",idusuario_resolucion=" . ($idusuario_resolucion ? "'$idusuario_resolucion'" : "NULL") . ",estado='$estado',observaciones='$observaciones' WHERE idincidencia='$idincidencia'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($idincidencia)
    {
        $sql = "DELETE FROM incidencia WHERE idincidencia='$idincidencia'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idincidencia)
    {
        $sql = "SELECT idincidencia,idestudiante,idreserva,idusuario_registro,tipo_incidencia,descripcion,fecha_registro,fecha_resolucion,idusuario_resolucion,estado,observaciones FROM incidencia WHERE idincidencia='$idincidencia'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function verificarResolucion($idincidencia)
    {
        $sql = "SELECT fecha_resolucion,idusuario_resolucion FROM incidencia WHERE idincidencia='$idincidencia'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $tipoIncidenciaBuscar = "", $estadoBuscar = "")
    {
        $sql = "SELECT i.idincidencia,i.idestudiante,i.tipo_incidencia,i.descripcion,i.fecha_registro,i.estado,e.nombre as estudiante,e.codigo_estudiante,u.nombre as usuario_registro FROM incidencia i INNER JOIN estudiante e ON i.idestudiante = e.idestudiante INNER JOIN usuario u ON i.idusuario_registro = u.idusuario WHERE 1=1";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND DATE(i.fecha_registro) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND DATE(i.fecha_registro) >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND DATE(i.fecha_registro) <= '$fecha_fin'";
        }

        if ($tipoIncidenciaBuscar != "") {
            $sql .= " AND i.tipo_incidencia = '$tipoIncidenciaBuscar'";
        }

        if ($estadoBuscar != "") {
            $sql .= " AND i.estado = '$estadoBuscar'";
        }

        $sql .= " ORDER BY i.fecha_registro DESC, i.idincidencia DESC";
        return ejecutarConsulta($sql);
    }

    public function listarPorEstudiante($idusuario, $fecha_inicio = "", $fecha_fin = "", $tipoIncidenciaBuscar = "", $estadoBuscar = "")
    {
        $sql = "SELECT i.idincidencia,i.idestudiante,i.tipo_incidencia,i.descripcion,i.fecha_registro,i.estado,e.nombre as estudiante,e.codigo_estudiante,u.nombre as usuario_registro FROM incidencia i INNER JOIN estudiante e ON i.idestudiante = e.idestudiante INNER JOIN usuario u ON i.idusuario_registro = u.idusuario WHERE e.idusuario='$idusuario'";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND DATE(i.fecha_registro) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND DATE(i.fecha_registro) >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND DATE(i.fecha_registro) <= '$fecha_fin'";
        }

        if ($tipoIncidenciaBuscar != "") {
            $sql .= " AND i.tipo_incidencia = '$tipoIncidenciaBuscar'";
        }

        if ($estadoBuscar != "") {
            $sql .= " AND i.estado = '$estadoBuscar'";
        }

        $sql .= " ORDER BY i.fecha_registro DESC, i.idincidencia DESC";
        return ejecutarConsulta($sql);
    }

    public function listarEstudiantes()
    {
        $sql = "SELECT idestudiante,nombre,codigo_estudiante FROM estudiante WHERE estado='activado' ORDER BY nombre ASC";
        return ejecutarConsulta($sql);
    }

    public function listarReservasEstudiante($idestudiante)
    {
        $sql = "SELECT r.idreserva,r.codigo_reserva,r.fecha_reserva,m.titulo as menu FROM reserva r INNER JOIN menu m ON r.idmenu = m.idmenu WHERE r.idestudiante='$idestudiante' ORDER BY r.fecha_reserva DESC";
        return ejecutarConsulta($sql);
    }
}
