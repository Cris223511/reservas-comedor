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
require_once "../modelos/Reserva.php";
$reserva = new Reserva();

$idreserva = isset($_POST["idreserva"]) ? limpiarCadena($_POST["idreserva"]) : "";
$idreserva_actualizar = isset($_POST["idreserva_actualizar"]) ? limpiarCadena($_POST["idreserva_actualizar"]) : "";
$estado_pago = isset($_POST["estado_pago"]) ? limpiarCadena($_POST["estado_pago"]) : "";
$estado_reserva = isset($_POST["estado_reserva"]) ? limpiarCadena($_POST["estado_reserva"]) : "";
$metodo_pago = isset($_POST["metodo_pago"]) ? limpiarCadena($_POST["metodo_pago"]) : "";
$observaciones = isset($_POST["observaciones_actualizar"]) ? limpiarCadena($_POST["observaciones_actualizar"]) : "";
$motivo_cancelacion = isset($_POST["motivo_cancelacion"]) ? limpiarCadena($_POST["motivo_cancelacion"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_reservas'] == 1 && $_SESSION['cargo'] == 'estudiante') {
                $idusuario = $_SESSION['idusuario'];
                $rspta_estudiante = $reserva->obtenerEstudiantePorUsuario($idusuario);
                $estudiante = $rspta_estudiante->fetch_object();
                $idestudiante = $estudiante->idestudiante;

                if (!isset($_POST["idmenu"]) || empty($_POST["idmenu"])) {
                    echo "ERROR: Debe seleccionar un menú";
                    break;
                }

                $idmenu = limpiarCadena($_POST["idmenu"][0]);
                $fecha_reserva = isset($_POST["fecha_reserva"]) ? limpiarCadena($_POST["fecha_reserva"]) : "";
                $hora_reserva = isset($_POST["hora_reserva"]) ? limpiarCadena($_POST["hora_reserva"]) : "";

                if (empty($fecha_reserva) || empty($hora_reserva)) {
                    echo "ERROR: Debe especificar fecha y hora de reserva";
                    break;
                }

                $menu_info = $reserva->obtenerMenuInfo($idmenu);
                if (!$menu_info) {
                    echo "ERROR: El menú seleccionado no existe";
                    break;
                }

                if ($menu_info['estado'] != 'activado') {
                    echo "ERROR: El menú seleccionado no está disponible";
                    break;
                }

                $precio = $menu_info['precio'];
                $config = $reserva->obtenerConfiguracionAforo();
                $dias_anticipacion = $config['dias_anticipacion'];
                $hora_inicio_almuerzo = $config['hora_inicio_almuerzo'];
                $hora_fin_almuerzo = $config['hora_fin_almuerzo'];

                $fecha_minima = date('Y-m-d', strtotime('+' . $dias_anticipacion . ' days'));
                if ($fecha_reserva < $fecha_minima) {
                    echo "ERROR: Debe reservar con al menos $dias_anticipacion día(s) de anticipación";
                    break;
                }

                if ($hora_reserva < $hora_inicio_almuerzo || $hora_reserva > $hora_fin_almuerzo) {
                    echo "ERROR: La hora de reserva debe estar entre $hora_inicio_almuerzo y $hora_fin_almuerzo";
                    break;
                }

                if (empty($idreserva)) {
                    $tiene_reserva = $reserva->verificarReservaActivaEstudiante($idestudiante, $fecha_reserva);
                    if ($tiene_reserva) {
                        echo "Ya tiene una reserva activa para esta fecha";
                        break;
                    }

                    $aforo_disponible = $reserva->verificarAforoDisponible($fecha_reserva);
                    if (!$aforo_disponible) {
                        echo "No hay cupos disponibles para esta fecha";
                        break;
                    }

                    $fecha_registro = date('Y-m-d H:i:s');
                    $codigo_reserva = $reserva->generarCodigoReserva();

                    $rspta = $reserva->insertar($idestudiante, $idmenu, $codigo_reserva, $fecha_reserva, $hora_reserva, $fecha_registro, $precio);

                    if ($rspta) {
                        $reserva->actualizarControlAforo($fecha_reserva);
                        $whatsapp = $config['whatsapp_contacto'];
                        $mensaje_whatsapp = $config['mensaje_whatsapp'];
                        echo "Reserva creada correctamente. Código: $codigo_reserva\n\nPara confirmar su reserva, envíe su comprobante de pago al WhatsApp: $whatsapp\n\n$mensaje_whatsapp";
                    } else {
                        echo "No se pudo crear la reserva";
                    }
                } else {
                    $rspta = $reserva->editarReservaEstudiante($idreserva, $idmenu, $fecha_reserva, $hora_reserva, $precio);
                    echo $rspta ? "Reserva actualizada correctamente" : "No se pudo actualizar la reserva";
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'actualizarReserva':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_reservas'] == 1 && ($_SESSION['cargo'] == 'administrador' || $_SESSION['cargo'] == 'personal')) {
                $comprobante_pago = "";

                if (!empty($_FILES['comprobante_pago']['name'])) {
                    $uploadDirectory = "../files/comprobantes/";
                    $tempFile = $_FILES['comprobante_pago']['tmp_name'];
                    $fileExtension = strtolower(pathinfo($_FILES['comprobante_pago']['name'], PATHINFO_EXTENSION));
                    $newFileName = sprintf("%09d", rand(0, 999999999)) . '.' . $fileExtension;
                    $targetFile = $uploadDirectory . $newFileName;
                    $allowedExtensions = array('jpg', 'jpeg', 'png', 'pdf');
                    if (in_array($fileExtension, $allowedExtensions) && move_uploaded_file($tempFile, $targetFile)) {
                        $comprobante_pago = $newFileName;
                    } else {
                        echo "Error al subir el comprobante.";
                        exit;
                    }
                } else {
                    $comprobante_pago = $_POST["comprobante_actual"];
                }

                $idusuario_confirma = NULL;
                $fecha_confirmacion = NULL;

                if ($estado_pago == 'confirmado' && $estado_reserva == 'confirmada') {
                    $idusuario_confirma = $_SESSION['idusuario'];
                    $fecha_confirmacion = date('Y-m-d H:i:s');
                }

                $rspta = $reserva->actualizar($idreserva_actualizar, $estado_pago, $estado_reserva, $metodo_pago, $comprobante_pago, $idusuario_confirma, $fecha_confirmacion, $observaciones);
                echo $rspta ? "Reserva actualizada correctamente" : "No se pudo actualizar la reserva";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'cancelarReserva':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_reservas'] == 1) {
                $cargo = $_SESSION['cargo'];
                $idusuario = $_SESSION['idusuario'];

                if ($cargo == 'estudiante') {
                    $reserva_info = $reserva->obtenerReservaInfo($idreserva);
                    $config = $reserva->obtenerConfiguracionAforo();
                    $horas_limite = $config['horas_limite_cancelacion'];

                    $fecha_reserva = $reserva_info['fecha_reserva'];
                    $hora_inicio = $config['hora_inicio_almuerzo'];
                    $fecha_hora_limite = date('Y-m-d H:i:s', strtotime("$fecha_reserva $hora_inicio -$horas_limite hours"));

                    if (date('Y-m-d H:i:s') > $fecha_hora_limite) {
                        echo "No puede cancelar la reserva. El tiempo límite de cancelación es $horas_limite horas antes del almuerzo";
                        break;
                    }
                }

                $fecha_cancelacion = date('Y-m-d H:i:s');
                $rspta = $reserva->cancelar($idreserva, $fecha_cancelacion, $motivo_cancelacion);
                echo $rspta ? "Reserva cancelada correctamente" : "No se pudo cancelar la reserva";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'eliminar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_reservas'] == 1 && $_SESSION['cargo'] == 'administrador') {
                $rspta = $reserva->eliminar($idreserva);
                echo $rspta ? "Reserva eliminada" : "No se puede eliminar la reserva";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_reservas'] == 1) {
                $rspta = $reserva->mostrar($idreserva);
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
            if ($_SESSION['gestion_reservas'] == 1) {
                $fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
                $fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
                $estadoPagoBuscar = isset($_POST["estadoPagoBuscar"]) ? limpiarCadena($_POST["estadoPagoBuscar"]) : "";
                $estadoReservaBuscar = isset($_POST["estadoReservaBuscar"]) ? limpiarCadena($_POST["estadoReservaBuscar"]) : "";

                $cargo = $_SESSION['cargo'];
                $idusuario = $_SESSION['idusuario'];

                if ($cargo == 'estudiante') {
                    $rspta = $reserva->listarPorEstudiante($idusuario, $fecha_inicio, $fecha_fin, $estadoPagoBuscar, $estadoReservaBuscar);
                } else {
                    $rspta = $reserva->listar($fecha_inicio, $fecha_fin, $estadoPagoBuscar, $estadoReservaBuscar);
                }

                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $opciones = '';
                    if ($cargo == 'administrador' || $cargo == 'personal') {
                        $opciones = '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
                            '<a target="_blank" href="../reportes/exTicketReserva.php?id=' . $reg->idreserva . '"><button class="btn btn-success" style="height: 35px; margin-right: 3px;"><i class="fa fa-print"></i></button></a>' .
                            (($reg->estado_reserva != 'cancelada') ? ('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrarActualizar(' . $reg->idreserva . ');"><i class="fa fa-pencil"></i></button>') : '') .
                            (($reg->estado_reserva != 'cancelada') ? ('<button class="btn btn-danger" style="margin-right: 3px; height: 35px;" onclick="cancelarReserva(' . $reg->idreserva . ')"><i class="fa fa-close"></i></button>') : '') .
                            (($_SESSION['cargo'] == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idreserva . ')"><i class="fa fa-trash"></i></button>') : '') .
                            '</div>';
                    } elseif ($cargo == 'estudiante') {
                        $opciones = '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
                            '<a target="_blank" href="../reportes/exTicketReserva.php?id=' . $reg->idreserva . '"><button class="btn btn-success" style="height: 35px; margin-right: 3px;"><i class="fa fa-print"></i></button></a>' .
                            (($reg->estado_reserva == 'pendiente') ? ('<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idreserva . ');"><i class="fa fa-pencil"></i></button>') : '') .
                            (($reg->estado_reserva == 'pendiente') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="cancelarReserva(' . $reg->idreserva . ')"><i class="fa fa-close"></i></button>') : '') .
                            '</div>';
                    }

                    $estado_pago_html = '';
                    switch ($reg->estado_pago) {
                        case 'pendiente':
                            $estado_pago_html = '<span class="label bg-orange">Pendiente</span>';
                            break;
                        case 'confirmado':
                            $estado_pago_html = '<span class="label bg-green">Confirmado</span>';
                            break;
                        case 'rechazado':
                            $estado_pago_html = '<span class="label bg-red">Rechazado</span>';
                            break;
                        default:
                            break;
                    }

                    $estado_reserva_html = '';
                    switch ($reg->estado_reserva) {
                        case 'pendiente':
                            $estado_reserva_html = '<span class="label bg-orange">Pendiente</span>';
                            break;
                        case 'confirmada':
                            $estado_reserva_html = '<span class="label bg-green">Confirmada</span>';
                            break;
                        case 'cancelada':
                            $estado_reserva_html = '<span class="label bg-red">Cancelada</span>';
                            break;
                        default:
                            break;
                    }

                    $fecha_hora_reserva = date('d-m-Y', strtotime($reg->fecha_reserva)) . '<br>' . date('H:i', strtotime($reg->hora_reserva));

                    $data[] = array(
                        "0" => $opciones,
                        "1" => $reg->codigo_reserva,
                        "2" => $reg->estudiante,
                        "3" => $reg->codigo_estudiante,
                        "4" => $reg->menu,
                        "5" => $fecha_hora_reserva,
                        "6" => 'S/. ' . number_format($reg->precio, 2),
                        "7" => $estado_pago_html,
                        "8" => $estado_reserva_html,
                        "9" => date('d-m-Y H:i', strtotime($reg->fecha_registro))
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
            if ($_SESSION['gestion_reservas'] == 1) {
                $rspta = $reserva->listarMenusDisponibles();
                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $tipo_menu_detalle = '';
                    switch ($reg->tipo_menu) {
                        case 'almuerzo':
                            $tipo_menu_detalle = 'Almuerzo';
                            break;
                        case 'cena':
                            $tipo_menu_detalle = 'Cena';
                            break;
                        default:
                            break;
                    }

                    $boton_agregar = '';
                    if ($reg->estado == 'activado') {
                        $boton_agregar = '<div style="display: flex; justify-content: center;"><button class="btn btn-warning" style="height: 35px;" data-idmenu="' . $reg->idmenu . '" onclick="agregarDetalle(' . $reg->idmenu . ',\'' . addslashes($reg->titulo) . '\',\'' . addslashes(substr($reg->descripcion, 0, 50)) . '\',\'' . number_format($reg->precio, 2) . '\',\'' . $reg->imagen . '\');"><span class="fa fa-plus"></span></button></div>';
                    } else {
                        $boton_agregar = '';
                    }

                    $data[] = array(
                        "0" => $boton_agregar,
                        "1" => ($reg->imagen != "" && $reg->imagen != null) ? '<a href="../files/menus/' . $reg->imagen . '" class="galleria-lightbox" style="z-index: 10000 !important;"><img src="../files/menus/' . $reg->imagen . '" height="50px" width="50px" class="img-fluid"></a>' : '<img src="../files/menus/default.jpg" height="50px" width="50px" class="img-fluid">',
                        "2" => $reg->titulo,
                        "3" => substr($reg->descripcion, 0, 100) . '...',
                        "4" => 'S/. ' . number_format($reg->precio, 2),
                        "5" => $tipo_menu_detalle
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
}
ob_end_flush();
