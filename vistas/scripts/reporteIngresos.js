var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var metodoPagoBuscar = "";

function init() {
    listar();
    cargarEstadisticas();
    $('#mReportes').addClass("treeview active");
    $('#lReporteIngresos').addClass("active");
}

function listar() {
    tabla = $('#tbllistado').dataTable({
        "lengthMenu": [5, 10, 25, 75, 100],
        "aProcessing": true,
        "aServerSide": true,
        dom: '<Bl<f>rtip>',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            {
                'extend': 'pdfHtml5',
                'orientation': 'landscape',
                'exportOptions': {
                    'columns': ':visible'
                },
                'customize': function (doc) {
                    doc.defaultStyle.fontSize = 8.5;
                    doc.styles.tableHeader.fontSize = 8.5;
                },
            },
        ],
        "ajax": {
            url: '../ajax/reporte.php?op=listarIngresos',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.metodoPagoBuscar = metodoPagoBuscar;
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "language": {
            "lengthMenu": "Mostrar : _MENU_ registros",
            "buttons": {
                "copyTitle": "Tabla Copiada",
                "copySuccess": {
                    _: '%d líneas copiadas',
                    1: '1 línea copiada'
                }
            }
        },
        "bDestroy": true,
        "iDisplayLength": 10,
        "order": [],
        "createdRow": function (row, data, dataIndex) {
        },
    }).DataTable();
}

function cargarEstadisticas() {
    $.post("../ajax/reporte.php?op=estadisticasIngresos", { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, metodoPagoBuscar: metodoPagoBuscar }, function (data) {
        data = JSON.parse(data);
        $("#totalIngresos").text("S/. " + data.total_ingresos);
        $("#totalReservasPagadas").text(data.total_reservas);
        $("#promedioReserva").text("S/. " + data.promedio_reserva);
    });
}

function buscar() {
    fecha_inicio = $("#fecha_inicio").val();
    fecha_fin = $("#fecha_fin").val();
    metodoPagoBuscar = $("#metodoPagoBuscar").val();
    tabla.ajax.reload();
    cargarEstadisticas();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#metodoPagoBuscar").val("");
    $("#metodoPagoBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    metodoPagoBuscar = "";
    tabla.ajax.reload();
    cargarEstadisticas();
}

init();