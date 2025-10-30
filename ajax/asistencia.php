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
require_once "../modelos/Asistencia.php";
$asistencia = new Asistencia();

$idreserva = isset($_POST["idreserva"]) ? limpiarCadena($_POST["idreserva"]) : "";
$codigo_reserva = isset($_POST["codigo_reserva"]) ? limpiarCadena($_POST["codigo_reserva"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'registrarAsistencia':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['registro_asistencia'] == 1) {
                $yaRegistrado = $asistencia->verificarAsistenciaRegistrada($idreserva);
                if ($yaRegistrado) {
                    echo "El estudiante ya registró su asistencia para esta reserva.";
                } else {
                    $idusuario_asistencia = $_SESSION['idusuario'];
                    $fecha_asistencia = date('Y-m-d H:i:s');
                    $rspta = $asistencia->registrarAsistencia($idreserva, $idusuario_asistencia, $fecha_asistencia, $observaciones);
                    echo $rspta ? "Asistencia registrada correctamente" : "No se pudo registrar la asistencia";
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
            if ($_SESSION['registro_asistencia'] == 1) {
                $rspta = $asistencia->mostrar($idreserva);
                echo json_encode($rspta);
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'buscarReserva':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['registro_asistencia'] == 1) {
                $rspta = $asistencia->buscarReservaPorCodigo($codigo_reserva);
                if ($rspta) {
                    echo json_encode($rspta);
                } else {
                    echo json_encode(array("error" => "La reserva no existe o no está confirmada."));
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'listar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['registro_asistencia'] == 1) {
                $fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
                $fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
                $asistenciaBuscar = isset($_POST["asistenciaBuscar"]) ? limpiarCadena($_POST["asistenciaBuscar"]) : "";

                $cargo = $_SESSION['cargo'];
                $idusuario = $_SESSION['idusuario'];

                if ($cargo == 'estudiante') {
                    $rspta = $asistencia->listarPorEstudiante($idusuario, $fecha_inicio, $fecha_fin, $asistenciaBuscar);
                } else {
                    $rspta = $asistencia->listar($fecha_inicio, $fecha_fin, $asistenciaBuscar);
                }

                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $opciones = '';
                    if ($cargo == 'administrador' || $cargo == 'personal') {
                        if ($reg->asistio == 0) {
                            $opciones = '<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idreserva . ');"><i class="fa fa-pencil"></i></button>';
                        }
                    } elseif ($cargo == 'estudiante') {
                        if ($reg->asistio == 0) {
                            $opciones = '<span class="label bg-orange">Pendiente</span>';
                        } else {
                            $opciones = '<span class="label bg-green">Registrado</span>';
                        }
                    }

                    $data[] = array(
                        "0" => $opciones,
                        "1" => $reg->codigo_reserva,
                        "2" => $reg->estudiante,
                        "3" => $reg->codigo_estudiante,
                        "4" => $reg->menu,
                        "5" => date('d-m-Y', strtotime($reg->fecha_reserva)),
                        "6" => ($reg->asistio == 1) ? '<span class="label bg-green">Sí</span>' : '<span class="label bg-red">No</span>',
                        "7" => ($reg->fecha_asistencia != null) ? date('d-m-Y H:i:s', strtotime($reg->fecha_asistencia)) : '-',
                        "8" => ($reg->usuario_asistencia != null) ? $reg->usuario_asistencia : '-'
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
