var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var asistenciaBuscar = "";

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $('#mAsistencia').addClass("active");
}

function limpiar() {
    $("#idreserva").val("");
    $("#codigo_reserva").val("");
    $("#observaciones").val("");
    $("#datosReserva").hide();
    $("#dato_estudiante").text("");
    $("#dato_codigo_estudiante").text("");
    $("#dato_menu").text("");
    $("#dato_fecha_reserva").text("");
    $("#dato_estado_reserva").text("");
    $("#dato_estado_pago").text("");
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
            url: '../ajax/asistencia.php?op=listar',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.asistenciaBuscar = asistenciaBuscar;
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
    asistenciaBuscar = $("#asistenciaBuscar").val();
    tabla.ajax.reload();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#asistenciaBuscar").val("");
    $("#asistenciaBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    asistenciaBuscar = "";
    tabla.ajax.reload();
}

function buscarReserva() {
    var codigo_reserva = $("#codigo_reserva").val();
    if (codigo_reserva == "") {
        bootbox.alert("Debe ingresar un código de reserva");
        return;
    }

    $.post("../ajax/asistencia.php?op=buscarReserva", { codigo_reserva: codigo_reserva }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);

        if (data.error) {
            bootbox.alert(data.error);
            $("#datosReserva").hide();
            $("#idreserva").val("");
        } else {
            $("#idreserva").val(data.idreserva);
            $("#dato_estudiante").text(data.estudiante);
            $("#dato_codigo_estudiante").text(data.codigo_estudiante);
            $("#dato_menu").text(data.menu);
            $("#dato_fecha_reserva").text(data.fecha_reserva);
            $("#dato_estado_reserva").html(data.estado_reserva_html);
            $("#dato_estado_pago").html(data.estado_pago_html);
            $("#datosReserva").show();
        }
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/asistencia.php?op=registrarAsistencia",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            if (datos == "La reserva no existe o no está confirmada." || datos == "El estudiante ya registró su asistencia para esta reserva.") {
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
    $.post("../ajax/asistencia.php?op=mostrar", { idreserva: idreserva }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);
        mostrarform(true);

        $("#idreserva").val(data.idreserva);
        $("#codigo_reserva").val(data.codigo_reserva);
        $("#observaciones").val(data.observaciones);

        $("#dato_estudiante").text(data.estudiante);
        $("#dato_codigo_estudiante").text(data.codigo_estudiante);
        $("#dato_menu").text(data.menu);
        $("#dato_fecha_reserva").text(data.fecha_reserva);
        $("#dato_estado_reserva").html(data.estado_reserva_html);
        $("#dato_estado_pago").html(data.estado_pago_html);
        $("#datosReserva").show();
    });
}

init();