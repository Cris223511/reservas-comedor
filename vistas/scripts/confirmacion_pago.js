var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var estadoPagoBuscar = "";

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $('#mPagos').addClass("active");
}

function limpiar() {
    $("#idreserva").val("");
    $("#dato_codigo_reserva").text("");
    $("#dato_estudiante").text("");
    $("#dato_codigo_estudiante").text("");
    $("#dato_menu").text("");
    $("#dato_fecha_reserva").text("");
    $("#dato_precio").text("");
    $("#dato_estado_pago").text("");
    $("#dato_estado_reserva").text("");
    $("#metodo_pago").val("");
    $("#metodo_pago").selectpicker('refresh');
    $("#accion").val("");
    $("#accion").selectpicker('refresh');
    $("#observaciones").val("");
}

function mostrarform(flag) {
    if (flag) {
        $(".listadoregistros").hide();
        $("#formularioregistros").show();
        $("#btnGuardar").prop("disabled", false);
        $("#btnagregar").hide();
    } else {
        $(".listadoregistros").show();
        $("#formularioregistros").hide();
        $("#btnagregar").show();
    }
}

function cancelarform() {
    limpiar();
    mostrarform(false);
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
                    'columns': ':not(:first-child)'
                },
                'customize': function (doc) {
                    doc.defaultStyle.fontSize = 8.5;
                    doc.styles.tableHeader.fontSize = 8.5;
                },
            },
        ],
        "ajax": {
            url: '../ajax/confirmacion_pago.php?op=listar',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.estadoPagoBuscar = estadoPagoBuscar;
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
        "iDisplayLength": 5,
        "order": [],
        "createdRow": function (row, data, dataIndex) {
        },
    }).DataTable();
}

function buscar() {
    fecha_inicio = $("#fecha_inicio").val();
    fecha_fin = $("#fecha_fin").val();
    estadoPagoBuscar = $("#estadoPagoBuscar").val();
    tabla.ajax.reload();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#estadoPagoBuscar").val("");
    $("#estadoPagoBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    estadoPagoBuscar = "";
    tabla.ajax.reload();
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/confirmacion_pago.php?op=confirmarRechazar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            if (datos == "La reserva no existe o ya fue procesada.") {
                bootbox.alert(datos);
                $("#btnGuardar").prop("disabled", false);
                return;
            }
            limpiar();
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
}

function mostrar(idreserva) {
    $.post("../ajax/confirmacion_pago.php?op=mostrar", { idreserva: idreserva }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);
        mostrarform(true);

        $("#idreserva").val(data.idreserva);
        $("#dato_codigo_reserva").text(data.codigo_reserva);
        $("#dato_estudiante").text(data.estudiante);
        $("#dato_codigo_estudiante").text(data.codigo_estudiante);
        $("#dato_menu").text(data.menu);
        $("#dato_fecha_reserva").text(data.fecha_reserva);
        $("#dato_precio").text(data.precio);
        $("#dato_estado_pago").html(data.estado_pago_html);
        $("#dato_estado_reserva").html(data.estado_reserva_html);
        $("#metodo_pago").val(data.metodo_pago);
        $("#metodo_pago").selectpicker('refresh');
        $("#observaciones").val(data.observaciones);
    });
}

init();