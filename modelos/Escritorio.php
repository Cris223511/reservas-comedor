<?php
require "../config/Conexion.php";

class Escritorio
{
    public function __construct() {}

    public function obtenerEstudiante($idusuario)
    {
        $sql = "SELECT idestudiante FROM estudiante WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function totalReservasEstudiante($idestudiante)
    {
        $sql = "SELECT COUNT(*) as total FROM reserva WHERE idestudiante='$idestudiante'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function reservasPendientesEstudiante($idestudiante)
    {
        $sql = "SELECT COUNT(*) as total FROM reserva WHERE idestudiante='$idestudiante' AND estado_reserva='pendiente'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function reservasConfirmadasEstudiante($idestudiante)
    {
        $sql = "SELECT COUNT(*) as total FROM reserva WHERE idestudiante='$idestudiante' AND estado_reserva='confirmada'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function reservasEstudianteUltimos10dias($idestudiante)
    {
        $sql = "SELECT DATE(fecha_registro) as fecha, COUNT(*) as total 
				FROM reserva 
				WHERE idestudiante='$idestudiante' 
				AND DATE(fecha_registro) >= CURDATE() - INTERVAL 9 DAY 
				GROUP BY DATE(fecha_registro) 
				ORDER BY fecha_registro DESC";
        return ejecutarConsulta($sql);
    }

    public function totalReservas()
    {
        $sql = "SELECT COUNT(*) as total FROM reserva";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function reservasConfirmadas()
    {
        $sql = "SELECT COUNT(*) as total FROM reserva WHERE estado_reserva='confirmada'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function totalIngresos()
    {
        $sql = "SELECT IFNULL(SUM(precio), 0) as total FROM reserva WHERE estado_pago='confirmado'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function reservasUltimos10dias()
    {
        $sql = "SELECT DATE(fecha_registro) as fecha, COUNT(*) as total 
				FROM reserva 
				WHERE DATE(fecha_registro) >= CURDATE() - INTERVAL 9 DAY 
				GROUP BY DATE(fecha_registro) 
				ORDER BY fecha_registro DESC";
        return ejecutarConsulta($sql);
    }

    public function ingresosUltimos10dias()
    {
        $sql = "SELECT DATE(fecha_registro) as fecha, IFNULL(SUM(precio), 0) as total 
				FROM reserva 
				WHERE estado_pago='confirmado' 
				AND DATE(fecha_registro) >= CURDATE() - INTERVAL 9 DAY 
				GROUP BY DATE(fecha_registro) 
				ORDER BY fecha_registro DESC";
        return ejecutarConsulta($sql);
    }
}
