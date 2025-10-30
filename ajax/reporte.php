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
require_once "../modelos/Reporte.php";
$reporte = new Reporte();

$fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
$fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
$estadoPagoBuscar = isset($_POST["estadoPagoBuscar"]) ? limpiarCadena($_POST["estadoPagoBuscar"]) : "";
$estadoReservaBuscar = isset($_POST["estadoReservaBuscar"]) ? limpiarCadena($_POST["estadoReservaBuscar"]) : "";
$tipoMenuBuscar = isset($_POST["tipoMenuBuscar"]) ? limpiarCadena($_POST["tipoMenuBuscar"]) : "";
$metodoPagoBuscar = isset($_POST["metodoPagoBuscar"]) ? limpiarCadena($_POST["metodoPagoBuscar"]) : "";

switch ($_GET["op"]) {
    case 'listarReservas':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $cargo = $_SESSION['cargo'];
                $idusuario = $_SESSION['idusuario'];

                if ($cargo == 'estudiante') {
                    $rspta = $reporte->listarReservasPorEstudiante($idusuario, $fecha_inicio, $fecha_fin, $estadoPagoBuscar, $estadoReservaBuscar);
                } else {
                    $rspta = $reporte->listarReservas($fecha_inicio, $fecha_fin, $estadoPagoBuscar, $estadoReservaBuscar);
                }

                $data = array();
                while ($reg = $rspta->fetch_object()) {
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

                    $data[] = array(
                        "0" => $reg->codigo_reserva,
                        "1" => $reg->estudiante,
                        "2" => $reg->codigo_estudiante,
                        "3" => $reg->menu,
                        "4" => date('d-m-Y', strtotime($reg->fecha_reserva)),
                        "5" => 'S/. ' . number_format($reg->precio, 2),
                        "6" => $estado_pago_html,
                        "7" => $estado_reserva_html,
                        "8" => date('d-m-Y H:i', strtotime($reg->fecha_registro))
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

    case 'listarMenus':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $rspta = $reporte->listarMenusMasSolicitados($fecha_inicio, $fecha_fin, $tipoMenuBuscar);
                $data = array();
                $posicion = 1;
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

                    $data[] = array(
                        "0" => $posicion,
                        "1" => $reg->menu,
                        "2" => $tipo_menu_detalle,
                        "3" => 'S/. ' . number_format($reg->precio, 2),
                        "4" => $reg->total_reservas,
                        "5" => 'S/. ' . number_format($reg->ingresos_generados, 2)
                    );
                    $posicion++;
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

    case 'listarAsistencia':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $rspta = $reporte->listarTasaAsistencia($fecha_inicio, $fecha_fin);
                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $tasa = 0;
                    if ($reg->total_confirmadas > 0) {
                        $tasa = round(($reg->total_asistencias / $reg->total_confirmadas) * 100, 2);
                    }

                    $data[] = array(
                        "0" => date('d-m-Y', strtotime($reg->fecha)),
                        "1" => $reg->total_confirmadas,
                        "2" => $reg->total_asistencias,
                        "3" => $reg->total_no_asistencias,
                        "4" => $tasa . '%'
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

    case 'estadisticasAsistencia':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $rspta = $reporte->estadisticasAsistencia($fecha_inicio, $fecha_fin);
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'listarIngresos':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $rspta = $reporte->listarIngresosPorFecha($fecha_inicio, $fecha_fin, $metodoPagoBuscar);
                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $data[] = array(
                        "0" => date('d-m-Y', strtotime($reg->fecha)),
                        "1" => $reg->total_reservas,
                        "2" => 'S/. ' . number_format($reg->ingresos, 2)
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

    case 'estadisticasIngresos':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['reportes'] == 1) {
                $rspta = $reporte->estadisticasIngresos($fecha_inicio, $fecha_fin, $metodoPagoBuscar);
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;
}
ob_end_flush();
