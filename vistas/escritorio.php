<?php
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) {
  header("Location: login.html");
} else {
  require 'header.php';

  if ($_SESSION['escritorio'] == 1) {

    require_once "../modelos/Escritorio.php";
    $consulta = new Escritorio();

    $cargo = $_SESSION["cargo"];
    $idusuario = $_SESSION["idusuario"];

    if ($cargo == "estudiante") {
      $rspta_estudiante = $consulta->obtenerEstudiante($idusuario);
      $estudiante = $rspta_estudiante->fetch_object();
      $idestudiante = $estudiante->idestudiante;

      $totalReservas = $consulta->totalReservasEstudiante($idestudiante)["total"];
      $reservasPendientes = $consulta->reservasPendientesEstudiante($idestudiante)["total"];
      $reservasConfirmadas = $consulta->reservasConfirmadasEstudiante($idestudiante)["total"];

      $reservas10dias = $consulta->reservasEstudianteUltimos10dias($idestudiante);
      $fechasr = '';
      $totalesr = '';
      while ($regfechar = $reservas10dias->fetch_object()) {
        $fechasr = $fechasr . '"' . $regfechar->fecha . '",';
        $totalesr = $totalesr . $regfechar->total . ',';
      }
      $fechasr = substr($fechasr, 0, -1);
      $totalesr = substr($totalesr, 0, -1);
    } else {
      $totalReservas = $consulta->totalReservas()["total"];
      $reservasConfirmadas = $consulta->reservasConfirmadas()["total"];
      $totalIngresos = $consulta->totalIngresos()["total"];

      $reservas10dias = $consulta->reservasUltimos10dias();
      $fechasr = '';
      $totalesr = '';
      while ($regfechar = $reservas10dias->fetch_object()) {
        $fechasr = $fechasr . '"' . $regfechar->fecha . '",';
        $totalesr = $totalesr . $regfechar->total . ',';
      }
      $fechasr = substr($fechasr, 0, -1);
      $totalesr = substr($totalesr, 0, -1);

      $ingresos10dias = $consulta->ingresosUltimos10dias();
      $fechasi = '';
      $totalesi = '';
      while ($regfechai = $ingresos10dias->fetch_object()) {
        $fechasi = $fechasi . '"' . $regfechai->fecha . '",';
        $totalesi = $totalesi . $regfechai->total . ',';
      }
      $fechasi = substr($fechasi, 0, -1);
      $totalesi = substr($totalesi, 0, -1);
    }
?>
    <style>
      .tarjeta1 {
        background-color: #27a844;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .tarjeta2 {
        background-color: #fec107;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .tarjeta3 {
        background-color: #17a2b7;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .ticket1 {
        color: #90ee90;
      }

      .ticket2 {
        color: #f7c87d;
      }

      .ticket3 {
        color: #3aadea;
      }

      .tarjeta1,
      .tarjeta2,
      .tarjeta3 {
        padding: 15px;
        border-radius: 20px;
        color: white;
      }

      .tarjeta1 h1,
      .tarjeta2 h1,
      .tarjeta3 h1 {
        font-weight: bold;
        margin: 0;
        padding: 5px 0 5px 0;
      }

      @media (max-width: 520px) {

        .ticket1,
        .ticket2,
        .ticket3 {
          display: none;
        }
      }

      @media (max-width: 1199px) {
        .marco {
          padding-top: 10px !important;
          padding-bottom: 10px !important;
          padding-left: 15px !important;
          padding-right: 15px !important;
        }

        .marco:nth-child(1),
        .marco:nth-child(2) {
          padding-top: 0 !important;
        }
      }

      @media (max-width: 991px) {
        .marco:nth-child(2) {
          padding-top: 10px !important;
        }
      }
    </style>
    <div class="content-wrapper">
      <section class="content">
        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h1 class="box-title">Escritorio</h1>
                <div class="box-tools pull-right">
                </div>
              </div>
              <div class="panel-body formularioregistros" style="background-color: white !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
                <div class="panel-body" style="padding-top: 0; padding-bottom: 0; padding-left: 15px; padding-right: 15px;">
                  <div class="row">
                    <?php if ($cargo == "estudiante") { ?>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-right: 5px">
                        <div class="tarjeta1 bg-green">
                          <div>
                            <h1><?php echo $totalReservas ?></h1>
                            <span>Mis reservas totales</span>
                          </div>
                          <i class="fa fa-calendar-check-o ticket1" style="font-size: 60px;"></i>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-left: 5px; padding-right: 5px;">
                        <div class="tarjeta2 bg-yellow">
                          <div>
                            <h1><?php echo $reservasPendientes ?></h1>
                            <span>Reservas pendientes</span>
                          </div>
                          <i class="fa fa-clock-o ticket2" style="font-size: 60px;"></i>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-left: 5px;">
                        <div class="tarjeta3 bg-aqua">
                          <div>
                            <h1><?php echo $reservasConfirmadas ?></h1>
                            <span>Reservas confirmadas</span>
                          </div>
                          <i class="fa fa-check-circle ticket3" style="font-size: 60px;"></i>
                        </div>
                      </div>
                    <?php } else { ?>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-right: 5px">
                        <div class="tarjeta1 bg-green">
                          <div>
                            <h1><?php echo $totalReservas ?></h1>
                            <span>Total de reservas</span>
                          </div>
                          <i class="fa fa-calendar-check-o ticket1" style="font-size: 60px;"></i>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-left: 5px; padding-right: 5px;">
                        <div class="tarjeta2 bg-yellow">
                          <div>
                            <h1><?php echo $reservasConfirmadas ?></h1>
                            <span>Reservas confirmadas</span>
                          </div>
                          <i class="fa fa-check-circle ticket2" style="font-size: 60px;"></i>
                        </div>
                      </div>
                      <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 marco" style="padding-left: 5px;">
                        <div class="tarjeta3 bg-aqua">
                          <div>
                            <h1>S/. <?php echo number_format($totalIngresos, 2) ?></h1>
                            <span>Total de ingresos</span>
                          </div>
                          <i class="fa fa-money ticket3" style="font-size: 60px;"></i>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-body formularioregistros" style="background-color: white !important; padding-left: 0 !important; padding-right: 0 !important; height: max-content;">
              <div class="panel-body">
                <?php if ($cargo == "estudiante") { ?>
                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="box box-primary">
                      <div class="box-body">
                        <canvas id="reservas" width="300" height="180"></canvas>
                      </div>
                    </div>
                  </div>
                <?php } else { ?>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="box box-primary">
                      <div class="box-body">
                        <canvas id="reservas" width="300" height="180"></canvas>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="box box-primary">
                      <div class="box-body">
                        <canvas id="ingresos" width="300" height="180"></canvas>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <script src="../public/plugins/node_modules/chart.js/dist/chart.min.js"></script>
    <script src="../public/plugins/node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
    <script type="text/javascript">
      Chart.register(ChartDataLabels);

      function ajustarMaximo(valor) {
        if (valor < 10) {
          return valor + 1;
        } else {
          const exponent = Math.floor(Math.log10(valor));
          const increment = Math.pow(10, exponent - 1);
          return valor + increment;
        }
      }

      let totalesr = [<?php echo $totalesr; ?>];
      let max1 = ajustarMaximo(Math.max(...totalesr));

      var ctx = document.getElementById("reservas").getContext('2d');
      var reservas = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [<?php echo $fechasr; ?>],
          datasets: [{
            barPercentage: 0.5,
            label: '<?php echo $cargo == "estudiante" ? "Mis reservas de los últimos 10 días" : "Reservas de los últimos 10 días"; ?>',
            data: [<?php echo $totalesr; ?>],
            backgroundColor: [
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
            ],
            borderColor: [
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
              'rgba(0,166,149,255)',
            ],
            borderWidth: 1,
            borderRadius: {
              topLeft: 10,
              topRight: 10
            }
          }]
        },
        options: {
          scales: {
            y: {
              suggestedMax: max1
            }
          },
          plugins: {
            datalabels: {
              anchor: 'end',
              align: 'top',
              formatter: function(value, context) {
                return value;
              },
              font: {
                weight: 'bold'
              }
            }
          }
        }
      });

      <?php if ($cargo != "estudiante") { ?>
        let totalesi = [<?php echo $totalesi; ?>];
        let max2 = ajustarMaximo(Math.max(...totalesi));

        var ctx2 = document.getElementById("ingresos").getContext('2d');
        var ingresos = new Chart(ctx2, {
          type: 'bar',
          data: {
            labels: [<?php echo $fechasi; ?>],
            datasets: [{
              barPercentage: 0.5,
              label: 'Ingresos en S/ de los últimos 10 días',
              data: [<?php echo $totalesi; ?>],
              backgroundColor: [
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
              ],
              borderColor: [
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
                'rgba(0,166,149,255)',
              ],
              borderWidth: 1,
              borderRadius: {
                topLeft: 10,
                topRight: 10
              }
            }]
          },
          options: {
            scales: {
              y: {
                suggestedMax: max2
              }
            },
            plugins: {
              datalabels: {
                anchor: 'end',
                align: 'top',
                formatter: function(value, context) {
                  return value.toLocaleString('es-PE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  }).replace(',', '.');
                },
                font: {
                  weight: 'bold'
                }
              }
            }
          }
        });
      <?php } ?>
    </script>
<?php

  } else {
    require 'noacceso.php';
  }
}

require 'footer.php';
ob_end_flush();

?>