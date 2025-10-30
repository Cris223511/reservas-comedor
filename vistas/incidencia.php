<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['gestion_incidencias'] == 1) {
?>
        <style>
            @media (max-width: 767px) {
                .botones {
                    width: 100% !important;
                }

                .table-responsive {
                    margin: 0;
                }

                #label {
                    display: none;
                }
            }
        </style>
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Gestión de Incidencias
                                    <?php if ($_SESSION["cargo"] == "administrador" || $_SESSION["cargo"] == "personal") { ?>
                                        <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                                    <?php } ?>
                                    <?php if ($_SESSION["cargo"] == "administrador") { ?>
                                        <a href="../reportes/rptincidencias.php" target="_blank">
                                            <button class="btn btn-secondary" style="color: black !important;">
                                                <i class="fa fa-clipboard"></i> Reporte
                                            </button>
                                        </a>
                                    <?php } ?>
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Gestión de Incidencias</strong>" data-html="true" data-content="Módulo para registrar y controlar incidencias como cancelaciones de reservas, estudiantes que no asistieron, o inconsistencias detectadas en los datos." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
                                </h1>
                                <div class="box-tools pull-right"></div>
                                <div class="panel-body table-responsive listadoregistros" style="overflow: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                                    <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12" style="padding: 5px; margin: 0;">
                                        <label>Fecha Inicial:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-sm-6 col-xs-12" style="padding: 5px; margin: 0;">
                                        <label>Fecha Final:</label>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                    </div>
                                    <div class="form-group col-lg-5 col-md-5 col-sm-12 col-xs-12" style="padding: 0px; margin: 0px;">
                                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                                            <label>Buscar por tipo:</label>
                                            <select id="tipoIncidenciaBuscar" name="tipoIncidenciaBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="No asistió">No asistió</option>
                                                <option value="No pagó">No pagó</option>
                                                <option value="Cancelación tardía">Cancelación tardía</option>
                                                <option value="Inconsistencia de datos">Inconsistencia de datos</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                                            <label>Buscar por estado:</label>
                                            <select id="estadoBuscar" name="estadoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="resuelta">Resuelta</option>
                                                <option value="cerrada">Cerrada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="padding: 5px; margin: 0px;">
                                        <label id="labelCustom">ㅤ</label>
                                        <div style="display: flex; gap: 10px;">
                                            <button style="width: 100%;" class="btn btn-bcp" onclick="buscar()">Buscar</button>
                                            <button style="height: 32px;" class="btn btn-success" onclick="resetear()"><i class="fa fa-repeat"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body listadoregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                                <div class="table-responsive" style="padding: 8px !important; padding: 20px !important; background-color: white;">
                                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                                        <thead>
                                            <th style="width: 1%;">Opciones</th>
                                            <th style="width: 20%; min-width: 180px;">Estudiante</th>
                                            <th>Código Estudiante</th>
                                            <th>Tipo Incidencia</th>
                                            <th>Descripción</th>
                                            <th>Fecha Registro</th>
                                            <th>Estado</th>
                                            <th>Registrado por</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Opciones</th>
                                            <th>Estudiante</th>
                                            <th>Código Estudiante</th>
                                            <th>Tipo Incidencia</th>
                                            <th>Descripción</th>
                                            <th>Fecha Registro</th>
                                            <th>Estado</th>
                                            <th>Registrado por</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                                <form name="formulario" id="formulario" method="POST">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 20px;">
                                        <input type="hidden" name="idincidencia" id="idincidencia">
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Estudiante(*):</label>
                                            <select id="idestudiante" name="idestudiante" class="form-control selectpicker" data-live-search="true" data-size="5" required>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Reserva Asociada:</label>
                                            <select id="idreserva" name="idreserva" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Ninguna -</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Tipo de Incidencia(*):</label>
                                            <select class="form-control selectpicker" name="tipo_incidencia" id="tipo_incidencia" required>
                                                <option value="">- Seleccione -</option>
                                                <option value="No asistió">No asistió</option>
                                                <option value="No pagó">No pagó</option>
                                                <option value="Cancelación tardía">Cancelación tardía</option>
                                                <option value="Inconsistencia de datos">Inconsistencia de datos</option>
                                                <option value="Otro">Otro</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Estado(*):</label>
                                            <select class="form-control selectpicker" name="estado" id="estado" required>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="resuelta">Resuelta</option>
                                                <option value="cerrada">Cerrada</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                            <label>Descripción(*):</label>
                                            <textarea class="form-control" name="descripcion" id="descripcion" rows="4" maxlength="1000" placeholder="Ingrese la descripción detallada de la incidencia" required></textarea>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                            <label>Observaciones:</label>
                                            <textarea class="form-control" name="observaciones" id="observaciones" rows="3" maxlength="500" placeholder="Ingrese observaciones adicionales si es necesario"></textarea>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 botones" style="background-color: white !important; padding: 10px !important;">
                                            <button class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                            <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    <?php
    } else {
        require 'noacceso.php';
    }
    require 'footer.php';
    ?>
    <script type="text/javascript" src="scripts/incidencia.js"></script>
<?php
}
ob_end_flush();
?>