<?php
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
    header("Location: login.html");
} else {
    require 'header.php';
    if ($_SESSION['configuracion'] == 1) {
?>
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Configuración de Aforo
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Configuración de Aforo</strong>" data-html="true" data-content="Módulo para configurar los parámetros del sistema de reservas como aforo máximo diario, días de anticipación, horarios y datos de contacto." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
                                </h1>
                                <div class="box-tools pull-right">
                                </div>
                            </div>

                            <div class="panel-body" id="formularioregistros">
                                <form name="formulario" id="formulario" method="POST">
                                    <input type="hidden" name="idconfiguracion" id="idconfiguracion">

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Aforo Máximo Diario(*):</label>
                                        <input type="number" class="form-control" name="aforo_maximo" id="aforo_maximo" min="1" max="9999" placeholder="Cantidad máxima de reservas por día" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Días de Anticipación(*):</label>
                                        <input type="number" class="form-control" name="dias_anticipacion" id="dias_anticipacion" min="0" max="30" placeholder="Días mínimos de anticipación" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Horas Límite para Cancelación(*):</label>
                                        <input type="number" class="form-control" name="horas_limite_cancelacion" id="horas_limite_cancelacion" min="1" max="72" placeholder="Horas antes del almuerzo" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Hora Inicio Almuerzo(*):</label>
                                        <input type="time" class="form-control" name="hora_inicio_almuerzo" id="hora_inicio_almuerzo" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Hora Fin Almuerzo(*):</label>
                                        <input type="time" class="form-control" name="hora_fin_almuerzo" id="hora_fin_almuerzo" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>WhatsApp de Contacto(*):</label>
                                        <input type="number" class="form-control" name="whatsapp_contacto" id="whatsapp_contacto" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="20" placeholder="Número de WhatsApp" required>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Mensaje de WhatsApp(*):</label>
                                        <textarea class="form-control" name="mensaje_whatsapp" id="mensaje_whatsapp" rows="3" maxlength="500" placeholder="Mensaje que se mostrará al estudiante para enviar el comprobante" required></textarea>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar Configuración</button>
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
    <script type="text/javascript" src="scripts/confAforo.js"></script>
<?php
}
ob_end_flush();
?>