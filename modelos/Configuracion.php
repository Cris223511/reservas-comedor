<?php
require "../config/Conexion.php";

class Configuracion
{
    public function __construct() {}

    public function editarAforo($idconfiguracion, $idusuario, $aforo_maximo, $dias_anticipacion, $horas_limite_cancelacion, $hora_inicio_almuerzo, $hora_fin_almuerzo, $whatsapp_contacto, $mensaje_whatsapp, $fecha_modificacion)
    {
        $sql = "UPDATE configuracion_aforo SET idusuario='$idusuario',aforo_maximo='$aforo_maximo',dias_anticipacion='$dias_anticipacion',horas_limite_cancelacion='$horas_limite_cancelacion',hora_inicio_almuerzo='$hora_inicio_almuerzo',hora_fin_almuerzo='$hora_fin_almuerzo',whatsapp_contacto='$whatsapp_contacto',mensaje_whatsapp='$mensaje_whatsapp',fecha_modificacion='$fecha_modificacion' WHERE idconfiguracion='$idconfiguracion'";
        return ejecutarConsulta($sql);
    }

    public function editarSistema($idconfiguracion, $nombre_comedor, $universidad, $direccion, $telefono, $email, $logo)
    {
        $sql = "UPDATE configuracion_sistema SET nombre_comedor='$nombre_comedor',universidad='$universidad',direccion='$direccion',telefono='$telefono',email='$email',logo='$logo' WHERE idconfiguracion='$idconfiguracion'";
        return ejecutarConsulta($sql);
    }

    public function mostrarAforo()
    {
        $sql = "SELECT idconfiguracion,idusuario,aforo_maximo,dias_anticipacion,horas_limite_cancelacion,hora_inicio_almuerzo,hora_fin_almuerzo,whatsapp_contacto,mensaje_whatsapp,fecha_modificacion FROM configuracion_aforo WHERE idconfiguracion=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrarSistema()
    {
        $sql = "SELECT idconfiguracion,nombre_comedor,universidad,direccion,telefono,email,logo FROM configuracion_sistema WHERE idconfiguracion=1";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function actualizarPerfilUsuario($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $login, $clave, $imagen)
    {
        $sql = "UPDATE usuario SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',login='$login',clave='$clave',imagen='$imagen' WHERE idusuario='$idusuario'";
        return ejecutarConsulta($sql);
    }

    public function actualizarPortadaLogin($imagen)
    {
        $sql = "UPDATE portada_login SET imagen='$imagen'";
        return ejecutarConsulta($sql);
    }

    public function obtenerPortadaLogin()
    {
        $sql = "SELECT * FROM portada_login";
        return ejecutarConsultaSimpleFila($sql);
    }
}
