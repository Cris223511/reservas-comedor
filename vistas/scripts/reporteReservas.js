var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var estadoPagoBuscar = "";
var estadoReservaBuscar = "";

function init() {
    listar();
    $('#mReportes').addClass("treeview active");
    $('#lReporteReservas').addClass("active");
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
            url: '../ajax/reporte.php?op=listarReservas',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.estadoPagoBuscar = estadoPagoBuscar;
                d.estadoReservaBuscar = estadoReservaBuscar;
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

function buscar() {
    fecha_inicio = $("#fecha_inicio").val();
    fecha_fin = $("#fecha_fin").val();
    estadoPagoBuscar = $("#estadoPagoBuscar").val();
    estadoReservaBuscar = $("#estadoReservaBuscar").val();
    tabla.ajax.reload();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#estadoPagoBuscar").val("");
    $("#estadoPagoBuscar").selectpicker('refresh');
    $("#estadoReservaBuscar").val("");
    $("#estadoReservaBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    estadoPagoBuscar = "";
    estadoReservaBuscar = "";
    tabla.ajax.reload();
}

init();