<?php
ob_start();
if (strlen(session_id()) < 1) {
    session_start();
}
if (empty($_SESSION['idusuario']) && empty($_SESSION['cargo'])) {
    session_unset();
    session_destroy();
    header("Location: ../vistas/login.html");
    exit();
}
require_once "../modelos/Menu.php";
$menu = new Menu();
$idmenu = isset($_POST["idmenu"]) ? limpiarCadena($_POST["idmenu"]) : "";
$titulo = isset($_POST["titulo"]) ? limpiarCadena($_POST["titulo"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$precio = isset($_POST["precio"]) ? limpiarCadena($_POST["precio"]) : "";
$imagen = isset($_POST["imagen"]) ? limpiarCadena($_POST["imagen"]) : "";
$tipo_menu = isset($_POST["tipo_menu"]) ? limpiarCadena($_POST["tipo_menu"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_menus'] == 1) {
                if (!empty($_FILES['imagen']['name'])) {
                    $uploadDirectory = "../files/menus/";
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
                if (empty($idmenu)) {
                    $tituloExiste = $menu->verificarTituloExiste($titulo);
                    if ($tituloExiste) {
                        echo "El título del menú que ha ingresado ya existe.";
                    } else {
                        $idusuario = $_SESSION['idusuario'];
                        $fecha_registro = date('Y-m-d H:i:s');
                        $rspta = $menu->insertar($idusuario, $titulo, $descripcion, $precio, $imagen, $tipo_menu, $fecha_registro);
                        echo $rspta ? "Menú registrado correctamente" : "No se pudo registrar el menú";
                    }
                } else {
                    $menuExistente = $menu->mostrar($idmenu);
                    $tituloExiste = $menu->verificarTituloExiste($titulo);
                    if ($tituloExiste && $titulo != $menuExistente['titulo']) {
                        echo "El título del menú que ha ingresado ya existe.";
                    } else {
                        $rspta = $menu->editar($idmenu, $titulo, $descripcion, $precio, $imagen, $tipo_menu);
                        echo $rspta ? "Menú actualizado correctamente" : "No se pudo actualizar el menú";
                    }
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'desactivar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_menus'] == 1) {
                $rspta = $menu->desactivar($idmenu);
                echo $rspta ? "Menú desactivado" : "No se puede desactivar el menú";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'activar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_menus'] == 1) {
                $rspta = $menu->activar($idmenu);
                echo $rspta ? "Menú activado" : "No se puede activar el menú";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'eliminar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_menus'] == 1) {
                $rspta = $menu->eliminar($idmenu);
                echo $rspta ? "Menú eliminado" : "No se puede eliminar el menú";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_menus'] == 1) {
                $rspta = $menu->mostrar($idmenu);
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
            if ($_SESSION['gestion_menus'] == 1) {
                $fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
                $fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
                $tipoMenuBuscar = isset($_POST["tipoMenuBuscar"]) ? limpiarCadena($_POST["tipoMenuBuscar"]) : "";
                $estadoBuscar = isset($_POST["estadoBuscar"]) ? limpiarCadena($_POST["estadoBuscar"]) : "";
                $rspta = $menu->listar($fecha_inicio, $fecha_fin, $tipoMenuBuscar, $estadoBuscar);
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
                    $tipo_menu_detalle = "";
                    switch ($reg->tipo_menu) {
                        case 'almuerzo':
                            $tipo_menu_detalle = "Almuerzo";
                            break;
                        case 'cena':
                            $tipo_menu_detalle = "Cena";
                            break;
                        default:
                            break;
                    }
                    $data[] = array(
                        "0" => '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
                            (($reg->estado == 'activado') ?
                                (($_SESSION['cargo'] == 'administrador' || $_SESSION['cargo'] == 'personal') ? ('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idmenu . ');"><i class="fa fa-pencil"></i></button>') : '') .
                                (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="desactivar(' . $reg->idmenu . ')"><i class="fa fa-close"></i></button>') : '') .
                                (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idmenu . ')"><i class="fa fa-trash"></i></button>') : '') : (($_SESSION['cargo'] == 'administrador' || $_SESSION['cargo'] == 'personal') ? ('<button class="btn btn-warning" style="margin-right: 3px; height: 35px;" onclick="mostrar(' . $reg->idmenu . ');"><i class="fa fa-pencil"></i></button>') : '') .
                                (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-success" style="margin-right: 3px; width: 35px; height: 35px; padding: 0;" onclick="activar(' . $reg->idmenu . ')"><i style="margin-left: -2px" class="fa fa-check"></i></button>') : '') .
                                (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idmenu . ')"><i class="fa fa-trash"></i></button>') : '')) . '</div>',
                        "1" => ($reg->imagen != "" && $reg->imagen != null) ? '<a href="../files/menus/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;">
                      <img src="../files/menus/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid">
                    </a>' : '<img src="../files/menus/default.jpg" height="50px" width="50px" class="img-fluid">',
                        "2" => $reg->titulo,
                        "3" => $reg->descripcion,
                        "4" => 'S/. ' . number_format($reg->precio, 2),
                        "5" => $tipo_menu_detalle,
                        "6" => $reg->nombre_usuario,
                        "7" => date('d-m-Y H:i', strtotime($reg->fecha_registro)),
                        "8" => $cargo_detalle,
                        "9" => ($reg->estado == 'activado') ? '<span class="label bg-green">Activado</span>' : '<span class="label bg-red">Desactivado</span>'
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

    case 'listarMenusDisponibles':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['registro_reservas'] == 1) {
                $rspta = $menu->listarMenusDisponibles();
                echo '<option value="">- Seleccione -</option>';
                while ($reg = $rspta->fetch_object()) {
                    echo '<option value="' . $reg->idmenu . '">(' . $reg->titulo . ') - S/ ' . number_format($reg->precio, 2) . '</option>';
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'selectMenus':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            $rspta = $menu->selectMenus();
            echo '<option value="">- Seleccione -</option>';
            while ($reg = $rspta->fetch_object()) {
                echo '<option value="' . $reg->idmenu . '">' . $reg->titulo . ' - S/ ' . number_format($reg->precio, 2) . '</option>';
            }
        }
        break;
}
ob_end_flush();
