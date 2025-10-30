<?php
ob_start();
session_start();
if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';
  if ($_SESSION['gestion_menus'] == 1) {
?>
    <style>
      @media (max-width: 991px) {
        .caja1 {
          padding: 0 !important;
          margin: 0 !important;
        }

        .caja1 .contenedor {
          display: flex;
          flex-direction: column;
          justify-content: center;
          text-align: center;
          gap: 15px;
          margin-bottom: 20px;
        }

        .caja1 .contenedor img {
          width: 25% !important;
        }

        .contenedor_menus {
          display: flex;
          flex-direction: column-reverse !important;
        }
      }

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
                <h1 class="box-title">Gestión de Menús
                  <?php if ($_SESSION["cargo"] == "administrador" || $_SESSION["cargo"] == "personal") { ?>
                    <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i> Agregar</button>
                  <?php } ?>
                  <?php if ($_SESSION["cargo"] == "administrador") { ?>
                    <a href="../reportes/rptmenus.php" target="_blank">
                      <button class="btn btn-secondary" style="color: black !important;">
                        <i class="fa fa-clipboard"></i> Reporte
                      </button>
                    </a>
                  <?php } ?>
                  <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Gestión de Menús</strong>" data-html="true" data-content="Módulo para registrar los menús disponibles que los estudiantes podrán reservar. Se debe especificar el título, descripción, precio y fecha de disponibilidad." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
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
                      <select id="tipoMenuBuscar" name="tipoMenuBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="">- Seleccione -</option>
                        <option value="almuerzo">Almuerzo</option>
                        <option value="cena">Cena</option>
                      </select>
                    </div>
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12" style="padding: 5px; margin: 0px;">
                      <label>Buscar por estado:</label>
                      <select id="estadoBuscar" name="estadoBuscar" class="form-control selectpicker" data-live-search="true" data-size="5">
                        <option value="">- Seleccione -</option>
                        <option value="activado">Activado</option>
                        <option value="desactivado">Desactivado</option>
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
                      <th>Imagen</th>
                      <th style="width: 20%; min-width: 180px;">Título</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Fecha Disponible</th>
                      <th>Tipo Menú</th>
                      <th>Registrado por</th>
                      <th>Cargo</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Imagen</th>
                      <th>Título</th>
                      <th>Descripción</th>
                      <th>Precio</th>
                      <th>Fecha Disponible</th>
                      <th>Tipo Menú</th>
                      <th>Registrado por</th>
                      <th>Cargo</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros" style="background-color: #ecf0f5 !important; padding-left: 0 !important; padding-right: 0 !important;">
                <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                  <div class="contenedor_menus">
                    <div class="form-group col-lg-10 col-md-8 col-sm-12 caja2" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 20px;">
                      <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <label>Título del Menú(*):</label>
                        <input type="hidden" name="idmenu" id="idmenu">
                        <input type="text" class="form-control" name="titulo" id="titulo" maxlength="100" placeholder="Ingrese el título del menú" required>
                      </div>
                      <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <label>Descripción(*):</label>
                        <textarea class="form-control" name="descripcion" id="descripcion" rows="4" maxlength="500" placeholder="Ingrese la descripción del menú" required></textarea>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-6">
                        <label>Precio(*):</label>
                        <input type="number" class="form-control" name="precio" id="precio" step="0.01" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="8" onkeydown="evitarNegativo(event)" onpaste="return false;" onDrop="return false;" min="0.01" placeholder="Ingrese el precio" required>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-6">
                        <label>Fecha Disponible(*):</label>
                        <input type="date" class="form-control" name="fecha_disponible" id="fecha_disponible" required>
                      </div>
                      <div class="form-group col-lg-4 col-md-4 col-sm-6">
                        <label>Tipo de Menú(*):</label>
                        <select class="form-control selectpicker" name="tipo_menu" id="tipo_menu" required>
                          <option value="">- Seleccione -</option>
                          <option value="almuerzo" selected>Almuerzo</option>
                          <option value="cena">Cena</option>
                        </select>
                      </div>
                      <div class="form-group col-lg-12 col-md-12 col-sm-12">
                        <label>Imagen:</label>
                        <input type="file" class="form-control" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.jfif,.bmp">
                        <input type="hidden" name="imagenactual" id="imagenactual">
                      </div>
                    </div>
                    <div class="form-group col-lg-2 col-md-4 col-sm-12 caja1" style="padding-right: 0 !important; padding-left: 20px;">
                      <div class="contenedor" style="background-color: white; border-top: 3px #002a8e solid !important; padding: 10px 20px 20px 20px; text-align: center;">
                        <label>Imagen de muestra:</label>
                        <div>
                          <img src="" width="100%" id="imagenmuestra" style="display: none;">
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group col-lg-10 col-md-8 col-sm-12 botones" style="background-color: white !important; padding: 10px !important; float: left;">
                    <div style="float: left;">
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
  <script type="text/javascript" src="scripts/menu.js"></script>
<?php
}
ob_end_flush();
?>