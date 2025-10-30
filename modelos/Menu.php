<?php
require "../config/Conexion.php";

class Menu
{
    public function __construct() {}

    public function insertar($idusuario, $titulo, $descripcion, $precio, $imagen, $tipo_menu, $fecha_registro)
    {
        if (empty($imagen))
            $imagen = "default.jpg";
        $sql = "INSERT INTO menu (idusuario,titulo,descripcion,precio,imagen,tipo_menu,fecha_registro,estado) VALUES ('$idusuario','$titulo','$descripcion','$precio','$imagen','$tipo_menu','$fecha_registro','activado')";
        return ejecutarConsulta($sql);
    }

    public function editar($idmenu, $titulo, $descripcion, $precio, $imagen, $tipo_menu)
    {
        $sql = "UPDATE menu SET titulo='$titulo',descripcion='$descripcion',precio='$precio',imagen='$imagen',tipo_menu='$tipo_menu' WHERE idmenu='$idmenu'";
        return ejecutarConsulta($sql);
    }

    public function desactivar($idmenu)
    {
        $sql = "UPDATE menu SET estado='desactivado' WHERE idmenu='$idmenu'";
        return ejecutarConsulta($sql);
    }

    public function activar($idmenu)
    {
        $sql = "UPDATE menu SET estado='activado' WHERE idmenu='$idmenu'";
        return ejecutarConsulta($sql);
    }

    public function eliminar($idmenu)
    {
        $sql = "DELETE FROM menu WHERE idmenu='$idmenu'";
        return ejecutarConsulta($sql);
    }

    public function mostrar($idmenu)
    {
        $sql = "SELECT idmenu,idusuario,titulo,descripcion,precio,imagen,tipo_menu,fecha_registro,estado FROM menu WHERE idmenu='$idmenu'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $tipoMenuBuscar = "", $estadoBuscar = "")
    {
        $sql = "SELECT m.idmenu,m.idusuario,m.titulo,m.descripcion,m.precio,m.imagen,m.tipo_menu,m.fecha_registro,m.estado,u.nombre as nombre_usuario,u.cargo FROM menu m INNER JOIN usuario u ON m.idusuario = u.idusuario WHERE 1=1";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND DATE(m.fecha_registro) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND DATE(m.fecha_registro) >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND DATE(m.fecha_registro) <= '$fecha_fin'";
        }

        if ($tipoMenuBuscar != "") {
            $sql .= " AND m.tipo_menu = '$tipoMenuBuscar'";
        }

        if ($estadoBuscar != "") {
            $sql .= " AND m.estado = '$estadoBuscar'";
        }

        $sql .= " ORDER BY m.fecha_registro DESC, m.idmenu DESC";
        return ejecutarConsulta($sql);
    }

    public function listarMenusDisponibles()
    {
        $sql = "SELECT idmenu,titulo,descripcion,precio,imagen FROM menu WHERE estado='activado' ORDER BY titulo ASC";
        return ejecutarConsulta($sql);
    }

    public function selectMenus()
    {
        $sql = "SELECT idmenu,titulo,precio FROM menu WHERE estado='activado' ORDER BY titulo ASC";
        return ejecutarConsulta($sql);
    }

    public function verificarTituloExiste($titulo)
    {
        $sql = "SELECT idmenu FROM menu WHERE titulo='$titulo'";
        $resultado = ejecutarConsulta($sql);
        if ($resultado->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}
