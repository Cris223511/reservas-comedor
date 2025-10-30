<?php
require "../config/Conexion.php";

class Menu
{
    public function __construct() {}

    public function insertar($idusuario, $titulo, $descripcion, $precio, $imagen, $fecha_disponible, $tipo_menu, $fecha_registro)
    {
        if (empty($imagen))
            $imagen = "default.jpg";
        $sql = "INSERT INTO menu (idusuario,titulo,descripcion,precio,imagen,fecha_disponible,tipo_menu,fecha_registro,estado) VALUES ('$idusuario','$titulo','$descripcion','$precio','$imagen','$fecha_disponible','$tipo_menu','$fecha_registro','activado')";
        return ejecutarConsulta($sql);
    }

    public function editar($idmenu, $titulo, $descripcion, $precio, $imagen, $fecha_disponible, $tipo_menu)
    {
        $sql = "UPDATE menu SET titulo='$titulo',descripcion='$descripcion',precio='$precio',imagen='$imagen',fecha_disponible='$fecha_disponible',tipo_menu='$tipo_menu' WHERE idmenu='$idmenu'";
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
        $sql = "SELECT idmenu,idusuario,titulo,descripcion,precio,imagen,fecha_disponible,tipo_menu,fecha_registro,estado FROM menu WHERE idmenu='$idmenu'";
        return ejecutarConsultaSimpleFila($sql);
    }

    public function listar($fecha_inicio = "", $fecha_fin = "", $tipoMenuBuscar = "", $estadoBuscar = "")
    {
        $sql = "SELECT m.idmenu,m.idusuario,m.titulo,m.descripcion,m.precio,m.imagen,m.fecha_disponible,m.tipo_menu,m.fecha_registro,m.estado,u.nombre as nombre_usuario,u.cargo FROM menu m INNER JOIN usuario u ON m.idusuario = u.idusuario WHERE 1=1";

        if ($fecha_inicio != "" && $fecha_fin != "") {
            $sql .= " AND m.fecha_disponible BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        } elseif ($fecha_inicio != "") {
            $sql .= " AND m.fecha_disponible >= '$fecha_inicio'";
        } elseif ($fecha_fin != "") {
            $sql .= " AND m.fecha_disponible <= '$fecha_fin'";
        }

        if ($tipoMenuBuscar != "") {
            $sql .= " AND m.tipo_menu = '$tipoMenuBuscar'";
        }

        if ($estadoBuscar != "") {
            $sql .= " AND m.estado = '$estadoBuscar'";
        }

        $sql .= " ORDER BY m.fecha_disponible DESC, m.idmenu DESC";
        return ejecutarConsulta($sql);
    }

    public function verificarTituloFechaExiste($titulo, $fecha_disponible)
    {
        $sql = "SELECT * FROM menu WHERE titulo = '$titulo' AND fecha_disponible = '$fecha_disponible'";
        $resultado = ejecutarConsulta($sql);
        if (mysqli_num_rows($resultado) > 0) {
            return true;
        }
        return false;
    }

    public function verificarTituloFechaEditarExiste($titulo, $fecha_disponible, $idmenu)
    {
        $sql = "SELECT * FROM menu WHERE titulo = '$titulo' AND fecha_disponible = '$fecha_disponible' AND idmenu != '$idmenu'";
        $resultado = ejecutarConsulta($sql);
        if (mysqli_num_rows($resultado) > 0) {
            return true;
        }
        return false;
    }
}
