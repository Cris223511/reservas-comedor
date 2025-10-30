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
require_once "../modelos/ConfirmacionPago.php";
$confirmacion = new ConfirmacionPago();

$idreserva = isset($_POST["idreserva"]) ? limpiarCadena($_POST["idreserva"]) : "";
$metodo_pago = isset($_POST["metodo_pago"]) ? limpiarCadena($_POST["metodo_pago"]) : "";
$accion = isset($_POST["accion"]) ? limpiarCadena($_POST["accion"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'confirmarRechazar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['confirmacion_pagos'] == 1) {
                $idusuario_confirma = $_SESSION['idusuario'];
                $fecha_confirmacion = date('Y-m-d H:i:s');

                if ($accion == 'confirmar') {
                    $estado_pago = 'confirmado';
                    $estado_reserva = 'confirmada';
                    $rspta = $confirmacion->confirmarPago($idreserva, $idusuario_confirma, $fecha_confirmacion, $metodo_pago, $estado_pago, $estado_reserva, $observaciones);
                    echo $rspta ? "Pago confirmado correctamente" : "No se pudo confirmar el pago";
                } elseif ($accion == 'rechazar') {
                    $estado_pago = 'rechazado';
                    $estado_reserva = 'cancelada';
                    $fecha_cancelacion = date('Y-m-d H:i:s');
                    $motivo_cancelacion = "Pago rechazado: " . $observaciones;
                    $rspta = $confirmacion->rechazarPago($idreserva, $idusuario_confirma, $fecha_confirmacion, $estado_pago, $estado_reserva, $fecha_cancelacion, $motivo_cancelacion, $observaciones);
                    echo $rspta ? "Pago rechazado correctamente" : "No se pudo rechazar el pago";
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['confirmacion_pagos'] == 1) {
                $rspta = $confirmacion->mostrar($idreserva);
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
            if ($_SESSION['confirmacion_pagos'] == 1) {
                $fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
                $fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
                $estadoPagoBuscar = isset($_POST["estadoPagoBuscar"]) ? limpiarCadena($_POST["estadoPagoBuscar"]) : "";

                $rspta = $confirmacion->listar($fecha_inicio, $fecha_fin, $estadoPagoBuscar);
                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $opciones = '';
                    if ($reg->estado_pago == 'pendiente') {
                        $opciones = '<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idreserva . ');"><i class="fa fa-pencil"></i></button>';
                    } else {
                        $opciones = '<button class="btn btn-info" style="margin-right: 3px;" onclick="mostrar(' . $reg->idreserva . ');"><i class="fa fa-eye"></i></button>';
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

                    $data[] = array(
                        "0" => $opciones,
                        "1" => $reg->codigo_reserva,
                        "2" => $reg->estudiante,
                        "3" => $reg->codigo_estudiante,
                        "4" => $reg->menu,
                        "5" => date('d-m-Y', strtotime($reg->fecha_reserva)),
                        "6" => 'S/. ' . number_format($reg->precio, 2),
                        "7" => $estado_pago_html,
                        "8" => $estado_reserva_html,
                        "9" => ($reg->fecha_confirmacion != null) ? date('d-m-Y H:i:s', strtotime($reg->fecha_confirmacion)) : '-'
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
