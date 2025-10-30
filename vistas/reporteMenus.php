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
                                <h1 class="box-title">Menús Más Solicitados
                                    <a href="../reportes/rptreporteMenus.php" target="_blank">
                                        <button class="btn btn-secondary" style="color: black !important;">
                                            <i class="fa fa-clipboard"></i> Reporte PDF
                                        </button>
                                    </a>
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Menús Más Solicitados</strong>" data-html="true" data-content="Reporte de los menús más solicitados por los estudiantes con el total de reservas por cada menú." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
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
                                            <label>Tipo de Menú:</label>
                                            <select id="tipoMenuBuscar" name="tipoMenuBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="almuerzo">Almuerzo</option>
                                                <option value="cena">Cena</option>
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
                                            <th>Posición</th>
                                            <th style="width: 30%; min-width: 180px;">Menú</th>
                                            <th>Tipo Menú</th>
                                            <th>Precio</th>
                                            <th>Total Reservas</th>
                                            <th>Ingresos Generados</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Posición</th>
                                            <th>Menú</th>
                                            <th>Tipo Menú</th>
                                            <th>Precio</th>
                                            <th>Total Reservas</th>
                                            <th>Ingresos Generados</th>
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
    <script type="text/javascript" src="scripts/reporteMenus.js"></script>
<?php
}
ob_end_flush();
?>