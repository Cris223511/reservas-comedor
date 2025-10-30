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
require_once "../modelos/Incidencia.php";
$incidencia = new Incidencia();

$idincidencia = isset($_POST["idincidencia"]) ? limpiarCadena($_POST["idincidencia"]) : "";
$idestudiante = isset($_POST["idestudiante"]) ? limpiarCadena($_POST["idestudiante"]) : "";
$idreserva = isset($_POST["idreserva"]) ? limpiarCadena($_POST["idreserva"]) : "";
$tipo_incidencia = isset($_POST["tipo_incidencia"]) ? limpiarCadena($_POST["tipo_incidencia"]) : "";
$descripcion = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
$estado = isset($_POST["estado"]) ? limpiarCadena($_POST["estado"]) : "";
$observaciones = isset($_POST["observaciones"]) ? limpiarCadena($_POST["observaciones"]) : "";

switch ($_GET["op"]) {
    case 'guardaryeditar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_incidencias'] == 1) {
                if ($idreserva == "") {
                    $idreserva = NULL;
                }

                if (empty($idincidencia)) {
                    $idusuario_registro = $_SESSION['idusuario'];
                    $fecha_registro = date('Y-m-d H:i:s');
                    $fecha_resolucion = NULL;
                    $idusuario_resolucion = NULL;

                    if ($estado == 'resuelta' || $estado == 'cerrada') {
                        $fecha_resolucion = date('Y-m-d H:i:s');
                        $idusuario_resolucion = $_SESSION['idusuario'];
                    }

                    $rspta = $incidencia->insertar($idestudiante, $idreserva, $idusuario_registro, $tipo_incidencia, $descripcion, $fecha_registro, $fecha_resolucion, $idusuario_resolucion, $estado, $observaciones);
                    echo $rspta ? "Incidencia registrada correctamente" : "No se pudo registrar la incidencia";
                } else {
                    $fecha_resolucion = NULL;
                    $idusuario_resolucion = NULL;

                    if ($estado == 'resuelta' || $estado == 'cerrada') {
                        $verificar = $incidencia->verificarResolucion($idincidencia);
                        if ($verificar['fecha_resolucion'] == NULL) {
                            $fecha_resolucion = date('Y-m-d H:i:s');
                            $idusuario_resolucion = $_SESSION['idusuario'];
                        } else {
                            $fecha_resolucion = $verificar['fecha_resolucion'];
                            $idusuario_resolucion = $verificar['idusuario_resolucion'];
                        }
                    }

                    $rspta = $incidencia->editar($idincidencia, $idestudiante, $idreserva, $tipo_incidencia, $descripcion, $fecha_resolucion, $idusuario_resolucion, $estado, $observaciones);
                    echo $rspta ? "Incidencia actualizada correctamente" : "No se pudo actualizar la incidencia";
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'eliminar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_incidencias'] == 1) {
                $rspta = $incidencia->eliminar($idincidencia);
                echo $rspta ? "Incidencia eliminada" : "No se puede eliminar la incidencia";
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'mostrar':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_incidencias'] == 1) {
                $rspta = $incidencia->mostrar($idincidencia);
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
            if ($_SESSION['gestion_incidencias'] == 1) {
                $fecha_inicio = isset($_POST["fecha_inicio"]) ? limpiarCadena($_POST["fecha_inicio"]) : "";
                $fecha_fin = isset($_POST["fecha_fin"]) ? limpiarCadena($_POST["fecha_fin"]) : "";
                $tipoIncidenciaBuscar = isset($_POST["tipoIncidenciaBuscar"]) ? limpiarCadena($_POST["tipoIncidenciaBuscar"]) : "";
                $estadoBuscar = isset($_POST["estadoBuscar"]) ? limpiarCadena($_POST["estadoBuscar"]) : "";

                $cargo = $_SESSION['cargo'];
                $idusuario = $_SESSION['idusuario'];

                if ($cargo == 'estudiante') {
                    $rspta = $incidencia->listarPorEstudiante($idusuario, $fecha_inicio, $fecha_fin, $tipoIncidenciaBuscar, $estadoBuscar);
                } else {
                    $rspta = $incidencia->listar($fecha_inicio, $fecha_fin, $tipoIncidenciaBuscar, $estadoBuscar);
                }

                $data = array();
                while ($reg = $rspta->fetch_object()) {
                    $opciones = '';
                    if ($cargo == 'administrador' || $cargo == 'personal') {
                        $opciones = '<div style="display: flex; flex-wrap: nowrap; gap: 3px">' .
                            '<button class="btn btn-warning" style="margin-right: 3px;" onclick="mostrar(' . $reg->idincidencia . ');"><i class="fa fa-pencil"></i></button>' .
                            (($cargo == 'administrador') ? ('<button class="btn btn-danger" style="height: 35px;" onclick="eliminar(' . $reg->idincidencia . ')"><i class="fa fa-trash"></i></button>') : '') .
                            '</div>';
                    } elseif ($cargo == 'estudiante') {
                        $opciones = '<span class="label bg-blue">Solo lectura</span>';
                    }

                    $estado_html = '';
                    switch ($reg->estado) {
                        case 'pendiente':
                            $estado_html = '<span class="label bg-orange">Pendiente</span>';
                            break;
                        case 'resuelta':
                            $estado_html = '<span class="label bg-green">Resuelta</span>';
                            break;
                        case 'cerrada':
                            $estado_html = '<span class="label bg-gray">Cerrada</span>';
                            break;
                        default:
                            break;
                    }

                    $data[] = array(
                        "0" => $opciones,
                        "1" => $reg->estudiante,
                        "2" => $reg->codigo_estudiante,
                        "3" => $reg->tipo_incidencia,
                        "4" => substr($reg->descripcion, 0, 50) . '...',
                        "5" => date('d-m-Y H:i', strtotime($reg->fecha_registro)),
                        "6" => $estado_html,
                        "7" => $reg->usuario_registro
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

    case 'selectEstudiantes':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_incidencias'] == 1) {
                $rspta = $incidencia->listarEstudiantes();
                echo '<option value="">- Seleccione -</option>';
                while ($reg = $rspta->fetch_object()) {
                    echo '<option value="' . $reg->idestudiante . '">' . $reg->nombre . ' - ' . $reg->codigo_estudiante . '</option>';
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;

    case 'selectReservasEstudiante':
        if (!isset($_SESSION["nombre"])) {
            header("Location: ../vistas/login.html");
        } else {
            if ($_SESSION['gestion_incidencias'] == 1) {
                $rspta = $incidencia->listarReservasEstudiante($idestudiante);
                echo '<option value="">- Ninguna -</option>';
                while ($reg = $rspta->fetch_object()) {
                    echo '<option value="' . $reg->idreserva . '">' . $reg->codigo_reserva . ' - ' . date('d-m-Y', strtotime($reg->fecha_reserva)) . ' - ' . $reg->menu . '</option>';
                }
            } else {
                require 'noacceso.php';
            }
        }
        break;
}
ob_end_flush();
