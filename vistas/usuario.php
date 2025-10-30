<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';
  if ($_SESSION['acceso'] == 1) {
?>
    <style>
      .tabs-filter {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #ddd;
        margin-bottom: 25px;
        flex-wrap: wrap;
        background: #fafafa;
        border-radius: 8px 8px 0 0;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      }

      .tab-btn {
        background: transparent;
        border: none;
        padding: 14px 28px;
        cursor: pointer;
        font-size: 14px;
        color: #555;
        border-bottom: 3px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        position: relative;
        letter-spacing: 0.3px;
      }

      .tab-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0, 42, 142, 0.03) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .tab-btn:hover {
        background: #f0f2f5;
        color: #002a8e;
        transform: translateY(-1px);
      }

      .tab-btn:hover::before {
        opacity: 1;
      }

      .tab-btn.active {
        color: #002a8e;
        border-bottom: 3px solid #002a8e;
        background: #ffffff;
        font-weight: 600;
        box-shadow: 0 -2px 8px rgba(0, 42, 142, 0.1);
      }

      .tab-btn.active::before {
        opacity: 0;
      }

      .tab-btn:focus {
        outline: none;
      }

      .tab-btn:active {
        transform: translateY(0);
      }

      @media (max-width: 768px) {
        .tab-btn {
          padding: 12px 20px;
          font-size: 13px;
        }
      }

      @media (max-width: 480px) {
        .tabs-filter {
          gap: 0;
          border-radius: 0;
        }

        .tab-btn {
          flex: 1;
          padding: 12px 10px;
          font-size: 12px;
          text-align: center;
        }
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Usuarios
                  <?php
                  if ($_SESSION['cargo'] == 'administrador') {
                  ?>
                    <button class="btn btn-bcp" id="btnagregar" onclick="mostrarform(true)">
                      <i class="fa fa-plus-circle"></i> Agregar
                    </button>
                  <?php
                  }
                  ?>
                  <?php if ($_SESSION["cargo"] == "administrador") { ?>
                    <a href="../reportes/rptusuarios.php" target="_blank">
                      <button class="btn btn-secondary" style="color: black !important;">
                        <i class="fa fa-clipboard"></i> Reporte
                      </button>
                    </a>
                  <?php } ?>
                  <a href="#" data-toggle="popover" data-placement="bottom" title="<strong>Usuarios</strong>" data-html="true" data-content="Módulo para registrar los usuarios quienes tendrán acceso al sistema. Los usuarios pueden tener los roles de <strong>administrador, personal comedor y estudiante</strong>." style="color: #002a8e; font-size: 18px;">&nbsp;<i class="fa fa-question-circle"></i></a>
                </h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body" id="listadoregistros">
                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin: 0px; padding: 0px;">
                  <div class="tabs-filter">
                    <button class="tab-btn active" data-cargo="">Todos</button>
                    <button class="tab-btn" data-cargo="administrador">Administradores</button>
                    <button class="tab-btn" data-cargo="personal">Personal Comedor</button>
                    <button class="tab-btn" data-cargo="estudiante">Estudiantes</button>
                  </div>
                </div>
                <div class="table-responsive" style="width: 100% !important">
                  <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover w-100" style="width: 100% !important">
                    <thead>
                      <th style="width: 1%;">Opciones</th>
                      <th>Foto</th>
                      <th style="width: 15%; min-width: 180px;">Nombre</th>
                      <th>Usuario</th>
                      <th>Cargo</th>
                      <th>Documento</th>
                      <th>Número Doc.</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Estado</th>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                      <th>Opciones</th>
                      <th>Foto</th>
                      <th>Nombre</th>
                      <th>Usuario</th>
                      <th>Cargo</th>
                      <th>Documento</th>
                      <th>Número Doc.</th>
                      <th>Teléfono</th>
                      <th>Email</th>
                      <th>Estado</th>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="panel-body" id="formularioregistros">
                <form name="formulario" id="formulario" method="POST" enctype="multipart/form-data">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <label>Nombre(*):</label>
                    <input type="hidden" name="idusuario" id="idusuario">
                    <input type="text" class="form-control" name="nombre" id="nombre" maxlength="100" placeholder="Nombre completo" required>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Tipo Documento(*):</label>
                    <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" onchange="changeValue(this);" required>
                      <option value="">- Seleccione -</option>
                      <option value="DNI">DNI</option>
                      <option value="RUC">RUC</option>
                      <option value="CEDULA">CEDULA</option>
                    </select>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Número Documento(*):</label>
                    <input type="number" class="form-control" name="num_documento" id="num_documento" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="20" placeholder="Número de documento" required>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Dirección:</label>
                    <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Dirección" maxlength="70">
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Teléfono:</label>
                    <input type="number" class="form-control" name="telefono" id="telefono" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="9" placeholder="Teléfono">
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Email:</label>
                    <input type="email" class="form-control" name="email" id="email" maxlength="50" placeholder="Email">
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Cargo(*):</label>
                    <select name="cargo" id="cargo" class="form-control selectpicker" required>
                      <option value="">- Seleccione -</option>
                      <option value="administrador">Administrador</option>
                      <option value="personal">Personal Comedor</option>
                      <option value="estudiante">Estudiante</option>
                    </select>
                  </div>

                  <div id="campos_estudiante" style="display: none; width: 100%;">
                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Código Estudiante(*):</label>
                      <input type="text" class="form-control" name="codigo_estudiante" id="codigo_estudiante" maxlength="20" oninput="convertirMayus(this);" placeholder="Código de estudiante">
                    </div>

                    <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <label>Facultad:</label>
                      <input type="text" class="form-control" name="facultad" id="facultad" maxlength="100" placeholder="Facultad">
                    </div>

                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <label>Carrera:</label>
                      <input type="text" class="form-control" name="carrera" id="carrera" maxlength="100" placeholder="Carrera profesional">
                    </div>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Usuario(*):</label>
                    <input type="text" class="form-control" name="login" id="login" maxlength="20" placeholder="Usuario" oninput="javascript: this.value = this.value.replace(/\s/g, '');" required>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Clave(*):</label>
                    <div style="display: flex;">
                      <input type="password" class="form-control" name="clave" id="clave" maxlength="64" placeholder="Clave" required>
                      <a id="mostrarClave" class="btn btn-bcp" style="display: flex; align-items: center;"><i class="fa fa-eye"></i></a>
                    </div>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Permisos:</label>
                    <ul style="list-style: none; margin-bottom: 0px;">
                      <li><input id="checkAll" type="checkbox" onchange="toggleCheckboxes(this)"> Marcar todos</li>
                    </ul>
                    <ul style="list-style: none;" id="permisos">
                    </ul>
                  </div>

                  <div class="form-group col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <label>Imagen:</label>
                    <input type="file" class="form-control" name="imagen" id="imagen" accept=".jpg,.jpeg,.png,.jfif,.bmp">
                    <input type="hidden" name="imagenactual" id="imagenactual">
                    <img src="" width="150px" id="imagenmuestra" style="display: none; margin-top: 10px;">
                  </div>

                  <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <button class="btn btn-warning" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                    <button class="btn btn-bcp" type="submit" id="btnGuardar"><i class="fa fa-save"></i> Guardar</button>
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

  <script type="text/javascript" src="scripts/usuario.js"></script>
<?php
}
ob_end_flush();
?>