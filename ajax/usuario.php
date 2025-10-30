<?php
ob_start();

if (strlen(session_id()) < 1) {
	session_start();
}

if (empty($_SESSION['idusuario']) && empty($_SESSION['cargo']) && $_GET["op"] !== 'verificar') {
	session_unset();
	session_destroy();
	header("Location: ../vistas/login.html");
	exit();
}

require_once "../modelos/Usuario.php";
$usuario = new Usuario();

$idusuario = isset($_POST["idusuario"]) ? limpiarCadena($_POST["idusuario"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$cargo = isset($_POST["cargo"]) ? limpiarCadena($_POST["cargo"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$codigo_estudiante = isset($_POST["codigo_estudiante"]) ? limpiarCadena($_POST["codigo_estudiante"]) : "";
$facultad = isset($_POST["facultad"]) ? limpiarCadena($_POST["facultad"]) : "";
$carrera = isset($_POST["carrera"]) ? limpiarCadena($_POST["carrera"]) : "";

switch ($_GET["op"]) {
	case 'guardaryeditar':
		if ($cargo == 'estudiante' && empty($idusuario)) {
			if (!empty($_FILES['imagen']['name'])) {
				$uploadDirectory = "../files/usuarios/";
				$tempFile = $_FILES['imagen']['tmp_name'];
				$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
				$newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
				$targetFile = $uploadDirectory . $newFileName;
				$allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif', 'bmp');
				if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
					$imagen = $newFileName;
				} else {
					echo "Error al subir la imagen.";
					exit;
				}
			} else {
				$imagen = "default.jpg";
			}
			$dniExiste = $usuario->verificarDniExiste($num_documento);
			$usuarioExiste = $usuario->verificarUsuarioExiste($login);
			if ($dniExiste) {
				echo "El número de documento que ha ingresado ya existe.";
			} else if ($usuarioExiste) {
				echo "El nombre del usuario que ha ingresado ya existe.";
			} else {
				$permisos_estudiante = array(1, 3, 5);
				$rspta = $usuario->insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos_estudiante, $codigo_estudiante, $facultad, $carrera);
				echo $rspta ? "Usuario registrado" : "Usuario no se pudo registrar.";
			}
		} else {
			if (!isset($_SESSION["nombre"])) {
				header("Location: ../vistas/login.html");
			} else {
				if ($_SESSION['acceso'] == 1) {
					if (!empty($_FILES['imagen']['name'])) {
						$uploadDirectory = "../files/usuarios/";
						$tempFile = $_FILES['imagen']['tmp_name'];
						$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
						$newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
						$targetFile = $uploadDirectory . $newFileName;
						$allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif', 'bmp');
						if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
							$imagen = $newFileName;
						} else {
							echo "Error al subir la imagen.";
							exit;
						}
					} else {
						$imagen = isset($_POST["imagenactual"]) ? $_POST["imagenactual"] : "default.jpg";
					}
					if (empty($idusuario)) {
						$dniExiste = $usuario->verificarDniExiste($num_documento);
						$usuarioExiste = $usuario->verificarUsuarioExiste($login);
						if ($dniExiste) {
							echo "El número de documento que ha ingresado ya existe.";
						} else if ($usuarioExiste) {
							echo "El nombre del usuario que ha ingresado ya existe.";
						} else {
							$permisos = isset($_POST['permiso']) ? $_POST['permiso'] : array();
							$rspta = $usuario->insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos, $codigo_estudiante, $facultad, $carrera);
							echo $rspta ? "Usuario registrado" : "Usuario no se pudo registrar.";
						}
					} else {
						$usuarioExiste = $usuario->verificarUsuarioEditarExiste($login, $idusuario);
						if ($usuarioExiste) {
							echo "El nombre del usuario que ha ingresado ya existe.";
						} else {
							$permisos = isset($_POST['permiso']) ? $_POST['permiso'] : array();
							$rspta = $usuario->editar($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos, $codigo_estudiante, $facultad, $carrera);
							echo $rspta ? "Usuario actualizado" : "Usuario no se pudo actualizar";
						}
					}
				} else {
					require 'noacceso.php';
				}
			}
		}
		break;

	case 'desactivar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$rspta = $usuario->desactivar($idusuario);
				echo $rspta ? "Usuario Desactivado" : "Usuario no se puede desactivar";
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'activar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$rspta = $usuario->activar($idusuario);
				echo $rspta ? "Usuario activado" : "Usuario no se puede activar";
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'eliminar':
		$rspta = $usuario->eliminar($idusuario);
		echo $rspta ? "Usuario eliminado" : "Usuario no se puede eliminar";
		break;

	case 'mostrar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['perfilu'] == 1) {
				$rspta = $usuario->mostrar($idusuario);
				echo json_encode($rspta);
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'listar':
		if (!isset($_SESSION["nombre"])) {
			header("Location: ../vistas/login.html");
		} else {
			if ($_SESSION['acceso'] == 1) {
				$cargoBuscar = isset($_POST["cargoBuscar"]) ? limpiarCadena($_POST["cargoBuscar"]) : "";
				$rspta = $usuario->listar($cargoBuscar);
				$data = array();
				while ($reg = $rspta->fetch_object()) {
					$cargo_detalle = "";
					switch ($reg->cargo) {
						case 'administrador':
							$cargo_detalle = "Administrador";
							break;
						case 'personal':
							$cargo_detalle = "Personal Comedor";
							break;
						case 'estudiante':
							$cargo_detalle = "Estudiante";
							break;
						default:
							break;
					}
					$telefono = ($reg->telefono == '') ? 'Sin registrar' : number_format($reg->telefono, 0, '', ' ');
					$data[] = array(
						"0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
							(($reg->estado) ?
								(($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idusuario . ');"><i class="fa fa-pencil"></i></button>') : '') .
								(($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idusuario . ')"><i class="fa fa-close"></i></button>') : '') .
								(($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idusuario . ')"><i class="fa fa-trash"></i></button>') : '') : (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idusuario . ');"><i class="fa fa-pencil"></i></button>') : '') .
								(($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px; padding: 0;" onclick="activar(' . $reg->idusuario . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>') : '') .
								(($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idusuario . ')"><i class="fa fa-trash"></i></button>') : '')) . '</div>',
						"1" => '<a href="../files/usuarios/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
									<img src="../files/usuarios/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
								</a>',
						"2" => $reg->nombre,
						"3" => $reg->login,
						"4" => $cargo_detalle,
						"5" => $reg->tipo_documento,
						"6" => $reg->num_documento,
						"7" => $telefono,
						"8" => $reg->email,
						"9" => ($reg->estado) ? '<span class="label bg-green">Activado</span>' :
							'<span class="label bg-red">Desactivado</span>'
					);
				}
				$results = array(
					"sEcho" => 1,
					"iTotalRecords" => count($data),
					"iTotalDisplayRecords" => count($data),
					"aaData" => $data
				);
				echo json_encode($results);
			} else {
				require 'noacceso.php';
			}
		}
		break;

	case 'listarUsuariosActivos':
		$rspta = $usuario->listarUsuariosActivos();
		while ($reg = $rspta->fetch_object()) {
			$cargo_detalle = "";
			switch ($reg->cargo) {
				case 'administrador':
					$cargo_detalle = "Administrador";
					break;
				case 'personal':
					$cargo_detalle = "Personal Comedor";
					break;
				case 'estudiante':
					$cargo_detalle = "Estudiante";
					break;
				default:
					break;
			}
			echo '<option value="' . $reg->idusuario . '"> ' . $reg->nombre  . ' - ' . $cargo_detalle . '</option>';
		}
		break;

	case 'selectUsuarios':
		$cargoSession = $_SESSION["cargo"];
		if ($cargoSession == "administrador") {
			$rspta = $usuario->listarASCactivos();
		} else {
			$rspta = $usuario->listarPorUsuarioASCActivos($_SESSION['idusuario']);
		}
		echo '<option value="">- Seleccione -</option>';
		while ($reg = $rspta->fetch_object()) {
			$cargo_detalle = "";
			switch ($reg->cargo) {
				case 'administrador':
					$cargo_detalle = "Administrador";
					break;
				case 'personal':
					$cargo_detalle = "Personal Comedor";
					break;
				case 'estudiante':
					$cargo_detalle = "Estudiante";
					break;
				default:
					break;
			}
			echo '<option value="' . $reg->idusuario . '"> ' . $reg->nombre  . ' - ' . $cargo_detalle . '</option>';
		}
		break;

	case 'permisos':
		require_once "../modelos/Permiso.php";
		$permiso = new Permiso();
		$rspta = $permiso->listar();
		$id = $_GET['id'];
		$marcados = $usuario->listarmarcados($id);
		$valores = array();
		while ($per = $marcados->fetch_object()) {
			array_push($valores, $per->idpermiso);
		}
		while ($reg = $rspta->fetch_object()) {
			$sw = in_array($reg->idpermiso, $valores) ? 'checked' : '';
			echo '<li> <input type="checkbox" ' . $sw . '  name="permiso[]" value="' . $reg->idpermiso . '">' . $reg->nombre . '</li>';
		}
		break;

	case 'getSessionId':
		$sessionIdData = array(
			'idusuario' => $_SESSION['idusuario']
		);
		echo json_encode($sessionIdData);
		break;

	case 'verificar':
		$logina = $_POST['logina'];
		$clavea = $_POST['clavea'];
		$rspta = $usuario->verificar($logina, $clavea);
		$fetch = $rspta->fetch_object();
		if (isset($fetch)) {
			if ($fetch->estado == 0) {
				echo 0;
				return;
			}
			$_SESSION['idusuario'] = $fetch->idusuario;
			$_SESSION['nombre'] = $fetch->nombre;
			$_SESSION['imagen'] = $fetch->imagen;
			$_SESSION['login'] = $fetch->login;
			$_SESSION['clave'] = $fetch->clave;
			$_SESSION['cargo'] = $fetch->cargo;

			switch ($_SESSION['cargo']) {
				case 'administrador':
					$_SESSION['cargo_detalle'] = "Administrador";
					break;
				case 'personal':
					$_SESSION['cargo_detalle'] = "Personal Comedor";
					break;
				case 'estudiante':
					$_SESSION['cargo_detalle'] = "Estudiante";
					$verificar_est = $usuario->verificar_estudiante($fetch->idusuario);
					$est = $verificar_est->fetch_object();
					$_SESSION['es_estudiante'] = isset($est) ? 1 : 0;
					break;
				default:
					break;
			}

			if ($_SESSION['cargo'] == 'estudiante' && $_SESSION['es_estudiante'] == 1) {
				$_SESSION['escritorio'] = 1;
				$_SESSION['perfilu'] = 1;
				$_SESSION['gestion_reservas'] = 1;
				$_SESSION['acceso'] = 0;
				$_SESSION['gestion_menus'] = 0;
				$_SESSION['confirmacion_pagos'] = 0;
				$_SESSION['registro_asistencia'] = 0;
				$_SESSION['gestion_incidencias'] = 0;
				$_SESSION['reportes'] = 0;
				$_SESSION['configuracion'] = 0;
			} else {
				$marcados = $usuario->listarmarcados($fetch->idusuario);
				$valores = array();
				while ($per = $marcados->fetch_object()) {
					array_push($valores, $per->idpermiso);
				}
				in_array(1, $valores) ? $_SESSION['escritorio'] = 1 : $_SESSION['escritorio'] = 0;
				in_array(2, $valores) ? $_SESSION['acceso'] = 1 : $_SESSION['acceso'] = 0;
				in_array(3, $valores) ? $_SESSION['perfilu'] = 1 : $_SESSION['perfilu'] = 0;
				in_array(4, $valores) ? $_SESSION['gestion_menus'] = 1 : $_SESSION['gestion_menus'] = 0;
				in_array(5, $valores) ? $_SESSION['gestion_reservas'] = 1 : $_SESSION['gestion_reservas'] = 0;
				in_array(6, $valores) ? $_SESSION['confirmacion_pagos'] = 1 : $_SESSION['confirmacion_pagos'] = 0;
				in_array(7, $valores) ? $_SESSION['registro_asistencia'] = 1 : $_SESSION['registro_asistencia'] = 0;
				in_array(8, $valores) ? $_SESSION['gestion_incidencias'] = 1 : $_SESSION['gestion_incidencias'] = 0;
				in_array(9, $valores) ? $_SESSION['reportes'] = 1 : $_SESSION['reportes'] = 0;
				in_array(10, $valores) ? $_SESSION['configuracion'] = 1 : $_SESSION['configuracion'] = 0;
			}
		}
		echo json_encode($fetch);
		break;

	case 'salir':
		//Limpiamos las variables de sesión   
		session_unset();
		//Destruìmos la sesión
		session_destroy();
		//Redireccionamos al login
		header("Location: ../index.php");

		break;
}

ob_end_flush();
