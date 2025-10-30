<?php
if (strlen(session_id()) < 1)
  session_start();

$nombre_login = $_SESSION['nombre'];
$cargo_login = $_SESSION['cargo_detalle'];
?>

<style>
  .bs-searchbox .form-control {
    height: 34px !important;
  }

  .content-wrapper {
    min-height: 919px !important;
  }

  div.dt-buttons {
    position: relative;
    float: left;
    margin-bottom: 10px !important;
  }

  .skin-blue-light .main-header .navbar .sidebar-toggle {
    transition: .3s ease all;
  }

  .skin-blue-light .main-header .navbar .sidebar-toggle:hover {
    transition: .3s ease all;
    background: #0234ac !important;
  }

  .skin-blue-light .main-header .navbar .nav>li>a {
    transition: .3s ease all;
  }

  .skin-blue-light .main-header .navbar .nav>li>a:hover {
    transition: .3s ease all;
    background: #0234ac !important;
  }

  .sidebar-menu .fa {
    color: #3e79fd;
  }

  .sidebar-menu .pull-right {
    color: #Fa7d1e;
    font-weight: bold;
    transition: .3s ease all;
  }

  .btn-bcp,
  .btn-danger,
  .btn-warning,
  .btn-info,
  .btn-success,
  .btn-secondary {
    transition: .3s ease all;
    border: 0px !important;
  }

  .btn-bcp.focus,
  .btn-danger.focus,
  .btn-warning.focus,
  .btn-info.focus,
  .btn-success.focus {
    color: white !important;
    text-decoration: none;
  }

  .btn-bcp:focus,
  .btn-danger:focus,
  .btn-warning:focus,
  .btn-info:focus,
  .btn-success:focus {
    color: white !important;
    text-decoration: none;
  }

  .main-header .navbar-custom-menu a,
  .main-header .navbar-right a {
    color: white !important;
  }

  .btn-warning {
    background-color: #Fa7d1e !important;
    color: while !important;
  }

  .btn-warning:hover {
    background-color: #ff961f !important;
  }

  .btn-default {
    background-color: #ffffff !important;
    transition: .3s ease all;
    border-color: #ccc;
  }

  .btn-default.disabled {
    background-color: #eeeeee !important;
    transition: .3s ease all;
    opacity: 1 !important;
  }

  .modal-footer .btn+.btn,
  .btn-bcp {
    background-color: #3e79fd !important;
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    transition: .3s ease all;
    color: white;
  }

  .modal-footer .btn+.btn:hover,
  .btn-bcp:hover {
    background-color: #5288ff !important;
    transition: .3s ease all !important;
    color: white !important;
  }

  .btn-bcp.disabled {
    background-color: #5288ff !important;
    transition: .3s ease all;
    opacity: 1 !important;
  }

  .nowrap-cell {
    white-space: nowrap;
  }

  .two-row {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  body {
    padding-right: 0 !important
  }

  .table-responsive {
    border: none !important;
  }

  #total2,
  #total {
    font-weight: bold;
  }

  .box {
    box-shadow: none !important;
    border-top: 3px #002a8e solid !important;
  }

  input,
  .form-control,
  button {
    border-radius: 5px !important;
  }

  label {
    text-transform: uppercase;
  }

  textarea {
    resize: none !important;
  }

  @media (max-width: 991.50px) {
    #labelCustom {
      display: none;
    }
  }

  .popover {
    z-index: 999999 !important;
  }

  .box-title2 {
    display: inline-block;
    font-size: 18px;
    margin: 0;
    line-height: 1;
  }


  @media (max-width: 991.50px) {
    .smallModal {
      width: 90% !important;
    }
  }
</style>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- avoid cache -->
  <meta http-equiv="Expires" content="0">
  <meta http-equiv="Last-Modified" content="0">
  <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <title>Sistema de Reservas</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="../public/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../public/css/font-awesome.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../public/css/AdminLTE.min.css">
  <!-- Lightbox style -->
  <link href="../public/glightbox/css/glightbox.min.css" rel="stylesheet" asp-append-version="true">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="../public/css/_all-skins.min.css">
  <link rel="apple-touch-icon" href="../public/img/icon-img.png">
  <link rel="shortcut icon" href="../public/img/icon-img.ico">

  <!-- DATATABLES -->
  <link rel="stylesheet" type="text/css" href="../public/datatables/jquery.dataTables.min.css">
  <link href="../public/datatables/buttons.dataTables.min.css" rel="stylesheet" />
  <link href="../public/datatables/responsive.dataTables.min.css" rel="stylesheet" />

  <link rel="stylesheet" type="text/css" href="../public/css/bootstrap-select.min.css">
</head>

<body class="hold-transition skin-blue-light sidebar-mini" style="padding: 0 !important;">
  <div class="wrapper">

    <header class="main-header" style="box-shadow: 0px 0px 15px -7px; position: sticky !important; width: 100%">
      <a href="escritorio.php" class="logo" style="color: white !important; background-color: #002a8e !important;">
        <span class="logo-mini"><b>S.R.</b></span>
        <span class="logo-lg" style="font-size: 15px;"><b>Sistema de Reservas S.A.C.</b></span>
      </a>
      <nav class="navbar" role="navigation" style="background-color: #002a8e !important;">
        <div style="display: flex; align-items: center; float: left;">
          <a href="#" class="sidebar-toggle" style="background: #002a8e; color: white !important;" data-toggle="offcanvas" role="button">
            <span class="sr-only">Navegación</span>
          </a>
        </div>
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu" style="background: #002a8e !important; display: inline-flex; align-items: center; height: 50px;">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color: white !important; height: 50px;">
                <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="user-image" alt="User Image">
                <span class="hidden-xs user-info user"><?php echo $nombre_login; ?> - <?php echo '<strong> Rol: ' . $cargo_login . '</strong>' ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header" style="background: #002a8e !important;">
                  <img src="../files/usuarios/<?php echo $_SESSION['imagen']; ?>" class="img-circle" alt="User Image">
                  <p style="color: white !important;">
                    Sistema de Reservas S.A.C.
                    <small>nuestro contacto: +51 992 719 552</small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="pull-right">
                    <a href="../ajax/usuario.php?op=salir" class="btn btn-warning btn-flat" onclick="destruirSession()">Cerrar sesión</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <aside class="main-sidebar">
      <section class="sidebar">
        <ul class="sidebar-menu">
          <li class="header"></li>
          <?php
          if ($_SESSION['escritorio'] == 1) {
            echo '<li id="mEscritorio">
          <a href="escritorio.php">
            <i class="fa fa-dashboard"></i> <span>Escritorio</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['gestion_menus'] == 1) {
            echo '<li id="mMenus">
          <a href="menu.php">
            <i class="fa fa-cutlery"></i> <span>Gestión de menús</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['gestion_reservas'] == 1) {
            echo '<li id="mReservas">
          <a href="reserva.php">
            <i class="fa fa-calendar-check-o"></i> <span>Gestión de reservas</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['confirmacion_pagos'] == 1) {
            echo '<li id="mPagos">
          <a href="confirmacion_pago.php">
            <i class="fa fa-money"></i> <span>Confirmación de pagos</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['registro_asistencia'] == 1) {
            echo '<li id="mAsistencia">
          <a href="asistencia.php">
            <i class="fa fa-check-square-o"></i> <span>Registro de asistencia</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['gestion_incidencias'] == 1) {
            echo '<li id="mIncidencias">
          <a href="incidencia.php">
            <i class="fa fa-exclamation-triangle"></i> <span>Gestión de incidencias</span>
          </a>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['reportes'] == 1) {
            echo '<li id="mReportes" class="treeview">
          <a href="#">
            <i class="fa fa-bar-chart"></i>
            <span>Reportes generales</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li id="lReporteReservas"><a href="reporteReservas.php"><i class="fa fa-circle-o"></i> Reporte de reservas</a></li>
            <li id="lReporteMenus"><a href="reporteMenus.php"><i class="fa fa-circle-o"></i> Menús más solicitados</a></li>
            <li id="lReporteAsistencia"><a href="reporteAsistencia.php"><i class="fa fa-circle-o"></i> Tasa de asistencia</a></li>
            <li id="lReporteIngresos"><a href="reporteIngresos.php"><i class="fa fa-circle-o"></i> Ingresos generados</a></li>
          </ul>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['perfilu'] == 1) {
            echo '<li id="mPerfilUsuario" class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Perfil de usuario</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li id="lConfUsuario"><a href="confUsuario.php"><i class="fa fa-circle-o"></i> Configuración de perfil</a></li>';
            if ($_SESSION['cargo'] == "administrador") {
              echo '<li id="lConfPortada"><a href="confPortada.php"><i class="fa fa-circle-o"></i> Configuración de portada</a></li>';
            }
            echo '</ul>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['acceso'] == 1) {
            echo '<li id="mAcceso" class="treeview">
          <a href="#">
            <i class="fa fa-unlock-alt"></i> <span>Acceso</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li id="lUsuarios"><a href="usuario.php"><i class="fa fa-circle-o"></i> Usuarios</a></li>
            <li id="lPermisos"><a href="permiso.php"><i class="fa fa-circle-o"></i> Permisos</a></li>
          </ul>
        </li>';
          }
          ?>
          <?php
          if ($_SESSION['configuracion'] == 1) {
            echo '<li id="mConfiguracion" class="treeview">
          <a href="#">
            <i class="fa fa-cogs"></i> <span>Configuración del sistema</span>
            <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li id="lConfAforo"><a href="confAforo.php"><i class="fa fa-circle-o"></i> Configuración de aforo</a></li>
            <li id="lConfSistema"><a href="confSistema.php"><i class="fa fa-circle-o"></i> Datos del comedor</a></li>
          </ul>
        </li>';
          }
          ?>
        </ul>
      </section>
    </aside>
    <script>
      function destruirSession() {
        sessionStorage.clear();
      }
    </script>