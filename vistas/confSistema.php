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
                                <h1 class="box-title">Datos del Comedor
                                    <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Datos del Comedor</strong>" data-html="true" data-content="Módulo para gestionar la información general del comedor universitario como nombre, dirección, teléfono y datos de contacto." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
                                </h1>
                                <div class="box-tools pull-right">
                                </div>
                            </div>

                            <div class="panel-body" id="formularioregistros">
                                <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="idconfiguracion" id="idconfiguracion">

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Nombre del Comedor(*):</label>
                                        <input type="text" class="form-control" name="nombre_comedor" id="nombre_comedor" maxlength="100" placeholder="Nombre del comedor" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Universidad(*):</label>
                                        <input type="text" class="form-control" name="universidad" id="universidad" maxlength="100" placeholder="Nombre de la universidad" required>
                                    </div>

                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Dirección(*):</label>
                                        <input type="text" class="form-control" name="direccion" id="direccion" maxlength="150" placeholder="Dirección del comedor" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Teléfono(*):</label>
                                        <input type="number" class="form-control" name="telefono" id="telefono" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="20" placeholder="Teléfono de contacto" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Email(*):</label>
                                        <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email de contacto" required>
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>Logo:</label>
                                        <input type="file" class="form-control" name="logo" id="logo" accept=".jpg,.jpeg,.png,.jfif,.bmp">
                                        <input type="hidden" name="logoactual" id="logoactual">
                                        <img src="" width="150px" id="logomuestra" style="display: none; margin-top: 10px;">
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
    <script type="text/javascript" src="scripts/confSistema.js"></script>
<?php
}
ob_end_flush();
?>