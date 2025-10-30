<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion.php";

class Usuario
{
	public function __construct() {}

	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos, $codigo_estudiante, $facultad, $carrera)
	{
		$nombreExiste = $this->verificarNombreExiste($login);
		if ($nombreExiste)
			return false;
		if (empty($imagen))
			$imagen = "default.jpg";
		$sql1 = "INSERT INTO usuario (nombre,tipo_documento,num_documento,direccion,telefono,email,cargo,login,clave,imagen,estado)
				VALUES ('$nombre','$tipo_documento','$num_documento','$direccion','$telefono','$email','$cargo','$login','$clave','$imagen','1')";
		$idusuarionew = ejecutarConsulta_retornarID($sql1);
		if ($cargo == 'estudiante') {
			$fecha_registro = date('Y-m-d H:i:s');
			$sql_estudiante = "INSERT INTO estudiante (idusuario,codigo_estudiante,nombre,tipo_documento,num_documento,email,telefono,facultad,carrera,fecha_registro,estado)
							VALUES ('$idusuarionew','$codigo_estudiante','$nombre','$tipo_documento','$num_documento','$email','$telefono','$facultad','$carrera','$fecha_registro','activado')";
			ejecutarConsulta($sql_estudiante);
		}
		$sql_delete_permisos = "DELETE FROM usuario_permiso WHERE idusuario='$idusuarionew'";
		ejecutarConsulta($sql_delete_permisos);
		$num_elementos = 0;
		$sw = true;
		while ($num_elementos < count($permisos)) {
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuarionew', '$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos++;
		}
		return $sw;
	}

	public function verificarUsuarioExiste($login)
	{
		$sql = "SELECT * FROM usuario WHERE login = '$login'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			return true;
		}
		return false;
	}

	public function verificarUsuarioEditarExiste($login, $idusuario)
	{
		$sql = "SELECT * FROM usuario WHERE login = '$login' AND idusuario != '$idusuario'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			return true;
		}
		return false;
	}

	public function verificarEmailExiste($email)
	{
		$sql = "SELECT * FROM usuario WHERE email = '$email'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			return true;
		}
		return false;
	}

	public function verificarNombreExiste($nombre)
	{
		$sql = "SELECT * FROM usuario WHERE nombre = '$nombre'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			return true;
		}
		return false;
	}

	public function verificarDniExiste($num_documento)
	{
		$sql = "SELECT * FROM usuario WHERE num_documento = '$num_documento'";
		$resultado = ejecutarConsulta($sql);
		if (mysqli_num_rows($resultado) > 0) {
			return true;
		}
		return false;
	}

	public function editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos, $codigo_estudiante, $facultad, $carrera)
	{
		$sql = "UPDATE usuario SET nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',direccion='$direccion',telefono='$telefono',email='$email',cargo='$cargo',login='$login',clave='$clave',imagen='$imagen' WHERE idusuario='$idusuario'";
		ejecutarConsulta($sql);
		if ($cargo == 'estudiante') {
			$verificar = $this->verificar_estudiante($idusuario);
			$existe = $verificar->fetch_object();
			if (isset($existe)) {
				$sql_estudiante = "UPDATE estudiante SET codigo_estudiante='$codigo_estudiante',nombre='$nombre',tipo_documento='$tipo_documento',num_documento='$num_documento',email='$email',telefono='$telefono',facultad='$facultad',carrera='$carrera' WHERE idusuario='$idusuario'";
			} else {
				$fecha_registro = date('Y-m-d H:i:s');
				$sql_estudiante = "INSERT INTO estudiante (idusuario,codigo_estudiante,nombre,tipo_documento,num_documento,email,telefono,facultad,carrera,fecha_registro,estado)
								VALUES ('$idusuario','$codigo_estudiante','$nombre','$tipo_documento','$num_documento','$email','$telefono','$facultad','$carrera','$fecha_registro','activado')";
			}
			ejecutarConsulta($sql_estudiante);
		}
		$sqldel = "DELETE FROM usuario_permiso WHERE idusuario='$idusuario'";
		ejecutarConsulta($sqldel);
		$num_elementos = 0;
		$sw = true;
		while ($num_elementos < count($permisos)) {
			$sql_detalle = "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES('$idusuario', '$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw = false;
			$num_elementos = $num_elementos + 1;
		}
		return $sw;
	}

	public function desactivar($idusuario)
	{
		$sql = "UPDATE usuario SET estado='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	public function activar($idusuario)
	{
		$sql = "UPDATE usuario SET estado='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	public function mostrar($idusuario)
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado,
			e.codigo_estudiante,e.facultad,e.carrera
			FROM usuario u
			LEFT JOIN estudiante e ON u.idusuario = e.idusuario
			WHERE u.idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function eliminar($idusuario)
	{
		$sql = "DELETE FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	public function listar($cargoBuscar = "")
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			WHERE 1=1";
		if ($cargoBuscar != "") {
			$sql .= " AND u.cargo = '$cargoBuscar'";
		}
		$sql .= " ORDER BY u.idusuario DESC";
		return ejecutarConsulta($sql);
	}

	public function listarASC()
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			ORDER BY u.idusuario ASC";
		return ejecutarConsulta($sql);
	}

	public function listarASCactivos()
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			WHERE u.estado='1'
			ORDER BY u.idusuario ASC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuarioASCActivos($idusuario)
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			WHERE u.estado='1'
			AND u.idusuario = '$idusuario'
			ORDER BY u.idusuario ASC";
		return ejecutarConsulta($sql);
	}

	public function listarPorUsuario($idusuarioSession)
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			WHERE u.idusuario = '$idusuarioSession'";
		return ejecutarConsulta($sql);
	}

	public function listarUsuariosActivos()
	{
		$sql = "SELECT u.idusuario,u.nombre,u.tipo_documento,u.num_documento,u.direccion,u.telefono,u.email,u.cargo,u.login,u.clave,u.imagen,u.estado
			FROM usuario u
			WHERE u.estado='1'
			ORDER BY u.idusuario DESC";
		return ejecutarConsulta($sql);
	}

	public function listarmarcados($idusuario)
	{
		$sql = "SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	public function verificar($login, $clave)
	{
		$sql = "SELECT idusuario,nombre,tipo_documento,num_documento,telefono,email,cargo,imagen,login,clave,estado FROM usuario WHERE login='$login' AND clave='$clave'";
		return ejecutarConsulta($sql);
	}

	public function verificar_estudiante($idusuario)
	{
		$sql = "SELECT idestudiante FROM estudiante WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}
}
