<?php
ob_start();
if (strlen(session_id()) < 1) {
    session_start();
}

$operaciones_publicas = ['mostrarPortadaLogin'];

if (!in_array($_GET["op"], $operaciones_publicas)) {
    if (empty($_SESSION['idusuario']) && empty($_SESSION['cargo'])) {
        session_unset();
        session_destroy();
        echo json_encode(["error" => "no_session"]);
        exit();
    }
}

require_once "../modelos/Configuracion.php";
require_once "../modelos/Usuario.php";
$configuracion = new Configuracion();
$usuario = new Usuario();

$idusuario = isset($_SESSION["idusuario"]) ? $_SESSION["idusuario"] : "";
$cargo = isset($_SESSION["cargo"]) ? $_SESSION["cargo"] : "";

$idconfiguracion = isset($_POST["idconfiguracion"]) ? limpiarCadena($_POST["idconfiguracion"]) : "";
$aforo_maximo = isset($_POST["aforo_maximo"]) ? limpiarCadena($_POST["aforo_maximo"]) : "";
$dias_anticipacion = isset($_POST["dias_anticipacion"]) ? limpiarCadena($_POST["dias_anticipacion"]) : "";
$horas_limite_cancelacion = isset($_POST["horas_limite_cancelacion"]) ? limpiarCadena($_POST["horas_limite_cancelacion"]) : "";
$hora_inicio_almuerzo = isset($_POST["hora_inicio_almuerzo"]) ? limpiarCadena($_POST["hora_inicio_almuerzo"]) : "";
$hora_fin_almuerzo = isset($_POST["hora_fin_almuerzo"]) ? limpiarCadena($_POST["hora_fin_almuerzo"]) : "";
$whatsapp_contacto = isset($_POST["whatsapp_contacto"]) ? limpiarCadena($_POST["whatsapp_contacto"]) : "";
$mensaje_whatsapp = isset($_POST["mensaje_whatsapp"]) ? limpiarCadena($_POST["mensaje_whatsapp"]) : "";
$nombre_comedor = isset($_POST["nombre_comedor"]) ? limpiarCadena($_POST["nombre_comedor"]) : "";
$universidad = isset($_POST["universidad"]) ? limpiarCadena($_POST["universidad"]) : "";
$direccion = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
$telefono = isset($_POST["telefono"]) ? limpiarCadena($_POST["telefono"]) : "";
$email = isset($_POST["email"]) ? limpiarCadena($_POST["email"]) : "";
$logo = isset($_POST["logo"]) ? limpiarCadena($_POST["logo"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$nombre = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
$tipo_documento = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
$num_documento = isset($_POST["num_documento"]) ? limpiarCadena($_POST["num_documento"]) : "";
$login = isset($_POST["login"]) ? limpiarCadena($_POST["login"]) : "";
$clave = isset($_POST["clave"]) ? limpiarCadena($_POST["clave"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditarAforo':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['configuracion'] == 1) {
                $idusuario = $_SESSION['idusuario'];
                $fecha_modificacion = date('Y-m-d H:i:s');
                $rspta = $configuracion->editarAforo($idconfiguracion, $idusuario, $aforo_maximo, $dias_anticipacion, $horas_limite_cancelacion, $hora_inicio_almuerzo, $hora_fin_almuerzo, $whatsapp_contacto, $mensaje_whatsapp, $fecha_modificacion);
                echo $rspta ? "Configuración actualizada correctamente" : "No se pudo actualizar la configuración";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'guardaryeditarSistema':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['configuracion'] == 1) {
                if (!empty($_FILES['logo']['name'])) {
                    $uploadDirectory = "../files/sistema/";
                    $tempFile = $_FILES['logo']['tmp_name'];
                    $fileExtension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                    $newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
                    $targetFile = $uploadDirectory . $newFileName;
                    $allowedExtensions = array('jpg', 'jpeg', 'png', 'jfif', 'bmp');
                    if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
                        $logo = $newFileName;
                    } else {
                        echo "Error al subir la imagen.";
                        exit;
                    }
                } else {
                    $logo = $_POST["logoactual"];
                }
                $rspta = $configuracion->editarSistema($idconfiguracion, $nombre_comedor, $universidad, $direccion, $telefono, $email, $logo);
                echo $rspta ? "Configuración actualizada correctamente" : "No se pudo actualizar la configuración";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'guardaryeditarPortada':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['perfilu'] == 1) {
                if (!empty($_FILES['imagen']['name'])) {
                    $uploadDirectory = "../files/portadas/";
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
                    $imagen = $_POST["imagenactual"];
                }
                $rspta = $configuracion->actualizarPortadaLogin($imagen);
                echo $rspta ? "Portada actualizada correctamente." : "Portada no se pudo actualizar.";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'guardaryeditarPerfil':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['perfilu'] == 1) {
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
                    $imagen = $_POST["imagenactual"];
                }
                $nombreExiste = $usuario->verificarNombreExiste($nombre);
                $dniExiste = $usuario->verificarDniExiste($num_documento);
                $emailExiste = $usuario->verificarEmailExiste($email);
                $usuarioExiste = $usuario->verificarUsuarioExiste($login);
                $perfilUsuario = $usuario->mostrar($idusuario);
                if ($nombreExiste && $nombre != $perfilUsuario['nombre']) {
                    echo "El nombre que ha ingresado ya existe.";
                } else if ($dniExiste && $num_documento != $perfilUsuario['num_documento']) {
                    echo "El número de documento que ha ingresado ya existe.";
                } else if ($emailExiste && $email != $perfilUsuario['email']) {
                    echo "El email que ha ingresado ya existe.";
                } else if ($usuarioExiste && $login != $perfilUsuario['login']) {
                    echo "El nombre del usuario que ha ingresado ya existe.";
                } else {
                    $rspta = $configuracion->actualizarPerfilUsuario($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $login, $clave, $imagen);
                    echo $rspta ? "Perfil actualizado correctamente" : "Perfil no se pudo actualizar";
                    if ($rspta) {
                        $_SESSION['nombre'] = $nombre;
                        $_SESSION['imagen'] = $imagen;
                    }
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrarAforo':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['configuracion'] == 1) {
                $rspta = $configuracion->mostrarAforo();
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrarSistema':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['configuracion'] == 1) {
                $rspta = $configuracion->mostrarSistema();
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrarPortada':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['perfilu'] == 1) {
                $rspta = $configuracion->obtenerPortadaLogin();
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrarPerfil':
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

    case 'actualizarSession':
        $info = array(
            'nombre' => $_SESSION['nombre'],
            'imagen' => $_SESSION['imagen'],
            'cargo' => $_SESSION['cargo_detalle']
        );
        echo json_encode($info);
        break;

    case 'mostrarPortadaLogin':
        $rspta = $configuracion->obtenerPortadaLogin();
        echo json_encode($rspta);
        break;
}
ob_end_flush();
