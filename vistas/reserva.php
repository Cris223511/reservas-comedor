<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['gestion_reservas'] == 1) {
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
                                <h1 class="box-title">Gestión de Reservas
                                    <?php if ($_SESSION["cargo"] == "estudiante") { ?>
                                        <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Nueva Reserva</button>
                                    <?php } ?>
                                    <?php if ($_SESSION["cargo"] == "administrador") { ?>
                                        <a href="../reportes/rptreservas.php" target="_blank">
                                            <button class="btn btn-secondary" style="color: black !important;">
                                                <i class="fa fa-clipboard"></i> Reporte
                                            </button>
                                        </a>
                                    <?php } ?>
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Gestión de Reservas</strong>" data-html="true" data-content="Módulo para gestionar las reservas de los estudiantes. Los estudiantes pueden crear reservas, y el personal puede confirmar pagos y actualizar estados." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
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
                                            <label>Estado de Pago:</label>
                                            <select id="estadoPagoBuscar" name="estadoPagoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="confirmado">Confirmado</option>
                                                <option value="rechazado">Rechazado</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                                            <label>Estado de Reserva:</label>
                                            <select id="estadoReservaBuscar" name="estadoReservaBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                                                <option value="">- Seleccione -</option>
                                                <option value="pendiente">Pendiente</option>
                                                <option value="confirmada">Confirmada</option>
                                                <option value="cancelada">Cancelada</option>
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
                                            <th>Código Reserva</th>
                                            <th style="width: 20%; min-width: 180px;">Estudiante</th>
                                            <th>Código Estudiante</th>
                                            <th>Menú</th>
                                            <th>Fecha y Hora Reserva</th>
                                            <th>Precio</th>
                                            <th>Estado Pago</th>
                                            <th>Estado Reserva</th>
                                            <th>Fecha Registro</th>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <th>Opciones</th>
                                            <th>Código Reserva</th>
                                            <th>Estudiante</th>
                                            <th>Código Estudiante</th>
                                            <th>Menú</th>
                                            <th>Fecha y Hora Reserva</th>
                                            <th>Precio</th>
                                            <th>Estado Pago</th>
                                            <th>Estado Reserva</th>
                                            <th>Fecha Registro</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                                <form name="formulario" id="formulario" method="POST">
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 20px;">
                                        <input type="hidden" name="idreserva" id="idreserva">
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                            <button type="button" class="btn btn-info" onclick="abrirModalMenus()"><i class="fa fa-plus"></i> Seleccionar Menú</button>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Fecha de Reserva(*):</label>
                                            <input type="date" class="form-control" name="fecha_reserva" id="fecha_reserva" required>
                                        </div>

                                        <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                            <label>Hora de Reserva(*):</label>
                                            <input type="time" class="form-control" name="hora_reserva" id="hora_reserva" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 table-responsive" style="border: 0px solid transparent !important; margin-top: 10px">
                                            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                                                <thead style="background-color:#A9D0F5">
                                                    <th>Opción</th>
                                                    <th>Imagen</th>
                                                    <th>Menú</th>
                                                    <th>Descripción</th>
                                                    <th>Precio</th>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12" style="background-color: white !important; padding: 10px !important;">
                                        <button class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                        <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar Reserva</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal Seleccionar Menú -->
        <div class="modal fade" id="modalMenus" tabindex="-1" role="dialog" aria-labelledby="modalMenusLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 90% !important; max-height: 95vh; margin: 0 !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%); overflow-x: hidden;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">SELECCIONE UN MENÚ</h4>
                    </div>
                    <div class="modal-body table-responsive">
                        <table id="tblmenus" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                            <thead>
                                <th>Opciones</th>
                                <th>Imagen</th>
                                <th style="width: 20%; min-width: 180px;">Título</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Tipo Menú</th>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <th>Opciones</th>
                                <th>Imagen</th>
                                <th>Título</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Tipo Menú</th>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Actualizar Reserva -->
        <div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="modalActualizarLabel" aria-hidden="true">
            <div class="modal-dialog" style="width: 60%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">ACTUALIZAR RESERVA</h4>
                    </div>
                    <form name="formularioActualizar" id="formularioActualizar" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" name="idreserva_actualizar" id="idreserva_actualizar">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label>Código de Reserva:</label>
                                <input type="text" class="form-control" id="codigo_reserva_mostrar" readonly>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label>Estado de Pago(*):</label>
                                <select class="form-control selectpicker" name="estado_pago" id="estado_pago" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label>Estado de Reserva(*):</label>
                                <select class="form-control selectpicker" name="estado_reserva" id="estado_reserva" required>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label>Método de Pago:</label>
                                <input type="text" class="form-control" name="metodo_pago" id="metodo_pago" maxlength="30" placeholder="Ej: Transferencia, Yape, Plin">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-sm-12">
                                <label>Comprobante de Pago:</label>
                                <input type="file" class="form-control" name="comprobante_pago" id="comprobante_pago" accept=".jpg,.jpeg,.png,.pdf">
                                <input type="hidden" name="comprobante_actual" id="comprobante_actual">
                                <a id="ver_comprobante" href="#" target="_blank" style="display: none; margin-top: 5px;">Ver comprobante actual</a>
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12">
                                <label>Observaciones:</label>
                                <textarea class="form-control" name="observaciones_actualizar" id="observaciones_actualizar" rows="3" maxlength="500" placeholder="Ingrese observaciones adicionales"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-bcp" id="btnActualizar"><i class="fa fa-save"></i> Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php
    } else {
        require 'noacceso.php';
    }
    require 'footer.php';
    ?>
    <script type="text/javascript" src="scripts/reserva.js"></script>
<?php
}
ob_end_flush();
?>