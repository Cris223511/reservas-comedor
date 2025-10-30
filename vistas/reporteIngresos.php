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
                                <h1 class="box-title">Ingresos Generados
                                    <a href="../reportes/rptreporteIngresos.php" target="_blank">
                                        <button class="btn btn-secondary" style="color: black !important;">
                                            <i class="fa fa-clipboard"></i> Reporte PDF
                                        </button>
                                    </a>
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Ingresos Generados</strong>" data-html="true" data-content="Reporte de los ingresos generados por las reservas confirmadas y pagadas en el comedor universitario." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
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
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 5px; margin: 0px;">
                                            <label>Método de Pago:</label>
                                            <select id="metodoPagoBuscar" name="metodoPagoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="Transferencia">Transferencia</option>
                                                <option value="Yape">Yape</option>
                                                <option value="Plin">Plin</option>
                                                <option value="Efectivo">Efectivo</option>
                                                <option value="Otro">Otro</option>
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
                                    <div class="col-lg-12 col-md-12 col-sm-12" style="margin-bottom: 20px;">
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="info-box bg-green">
                                                    <span class="info-box-icon"><i class="fa fa-money"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Ingresos</span>
                                                        <span class="info-box-number" id="totalIngresos">S/. 0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="info-box bg-aqua">
                                                    <span class="info-box-icon"><i class="fa fa-calendar-check-o"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Total Reservas Pagadas</span>
                                                        <span class="info-box-number" id="totalReservasPagadas">0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 col-sm-12">
                                                <div class="info-box bg-yellow">
                                                    <span class="info-box-icon"><i class="fa fa-calculator"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Promedio por Reserva</span>
                                                        <span class="info-box-number" id="promedioReserva">S/. 0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                                        <thead>
                                            <th>Fecha</th>
                                            <th>Total Reservas</th>
                                            <th>Ingresos del Día</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Fecha</th>
                                            <th>Total Reservas</th>
                                            <th>Ingresos del Día</th>
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
    <script type="text/javascript" src="scripts/reporteIngresos.js"></script>
<?php
}
ob_end_flush();
?>