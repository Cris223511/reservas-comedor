<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['reportes'] == 1) {
?>
        <style>
            .info-box {
                display: flex;
                align-items: center;
            }

            .info-box-icon {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .info-box-content {
                display: flex;
                flex-direction: column;
                justify-content: center;
                margin: 0;
            }

            @media (max-width: 767px) {
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
                                <h1 class="box-title">Tasa de Asistencia
                                    <a href="../reportes/rptreporteAsistencia.php" target="_blank">
                                        <button class="btn btn-secondary" style="color: black !important;">
                                            <i class="fa fa-clipboard"></i> Reporte PDF
                                        </button>
                                    </a>
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Tasa de Asistencia</strong>" data-html="true" data-content="Reporte que muestra el porcentaje de asistencia de los estudiantes que tenían reservas confirmadas comparado con los que realmente asistieron." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
                                </h1>
                                <div class="box-tools pull-right"></div>
                                <div class="panel-body table-responsive listadoregistros" style="overflow: visible; padding-left: 0px; padding-right: 0px; padding-bottom: 0px;">
                                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0;">
                                        <label>Fecha Inicial:</label>
                                        <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio">
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0;">
                                        <label>Fecha Final:</label>
                                        <input type="date" class="form-control" name="fecha_fin" id="fecha_fin">
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding: 5px; margin: 0px;">
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
                                    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom: 20px;">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="info-box bg-aqua">
                                                    <span class="info-box-icon"><i class="fa fa-calendar-check-o"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Reservas Confirmadas</span>
                                                        <span class="info-box-number" id="totalConfirmadas">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="info-box bg-green">
                                                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Asistencias</span>
                                                        <span class="info-box-number" id="totalAsistencias">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="info-box bg-red">
                                                    <span class="info-box-icon"><i class="fa fa-close"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">No Asistieron</span>
                                                        <span class="info-box-number" id="totalNoAsistencias">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-12">
                                                <div class="info-box bg-yellow">
                                                    <span class="info-box-icon"><i class="fa fa-percent"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Tasa de Asistencia</span>
                                                        <span class="info-box-number" id="tasaAsistencia">0%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                                        <thead>
                                            <th>Fecha</th>
                                            <th>Reservas Confirmadas</th>
                                            <th>Asistencias</th>
                                            <th>No Asistieron</th>
                                            <th>Tasa de Asistencia</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Fecha</th>
                                            <th>Reservas Confirmadas</th>
                                            <th>Asistencias</th>
                                            <th>No Asistieron</th>
                                            <th>Tasa de Asistencia</th>
                                        </tfoot>
                                    </table>
                                </div>
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
    <script type="text/javascript" src="scripts/reporteAsistencia.js"></script>
<?php
}
ob_end_flush();
?>