var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var tipoIncidenciaBuscar = "";
var estadoBuscar = "";

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $('#mIncidencias').addClass("active");
    cargarEstudiantes();

    $("#idestudiante").change(function () {
        cargarReservasEstudiante();
    });
}

function limpiar() {
    $("#idincidencia").val("");
    $("#idestudiante").val("");
    $("#idestudiante").selectpicker('refresh');
    $("#idreserva").val("");
    $("#idreserva").selectpicker('refresh');
    $("#tipo_incidencia").val("");
    $("#tipo_incidencia").selectpicker('refresh');
    $("#estado").val("pendiente");
    $("#estado").selectpicker('refresh');
    $("#descripcion").val("");
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
            url: '../ajax/incidencia.php?op=listar',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.tipoIncidenciaBuscar = tipoIncidenciaBuscar;
                d.estadoBuscar = estadoBuscar;
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
    tipoIncidenciaBuscar = $("#tipoIncidenciaBuscar").val();
    estadoBuscar = $("#estadoBuscar").val();
    tabla.ajax.reload();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#tipoIncidenciaBuscar").val("");
    $("#tipoIncidenciaBuscar").selectpicker('refresh');
    $("#estadoBuscar").val("");
    $("#estadoBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    tipoIncidenciaBuscar = "";
    estadoBuscar = "";
    tabla.ajax.reload();
}

function cargarEstudiantes() {
    $.post("../ajax/incidencia.php?op=selectEstudiantes", function (r) {
        $("#idestudiante").html(r);
        $('#idestudiante').selectpicker('refresh');
    });
}

function cargarReservasEstudiante() {
    var idestudiante = $("#idestudiante").val();
    if (idestudiante != "") {
        $.post("../ajax/incidencia.php?op=selectReservasEstudiante", { idestudiante: idestudiante }, function (r) {
            $("#idreserva").html(r);
            $('#idreserva').selectpicker('refresh');
        });
    } else {
        $("#idreserva").html('<option value="">- Ninguna -</option>');
        $('#idreserva').selectpicker('refresh');
    }
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/incidencia.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            limpiar();
            bootbox.alert(datos);
            mostrarform(false);
            tabla.ajax.reload();
        }
    });
}

function mostrar(idincidencia) {
    $.post("../ajax/incidencia.php?op=mostrar", { idincidencia: idincidencia }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);
        mostrarform(true);

        $("#idincidencia").val(data.idincidencia);
        $("#idestudiante").val(data.idestudiante);
        $("#idestudiante").selectpicker('refresh');

        cargarReservasEstudiante();
        setTimeout(function () {
            $("#idreserva").val(data.idreserva);
            $("#idreserva").selectpicker('refresh');
        }, 500);

        $("#tipo_incidencia").val(data.tipo_incidencia);
        $("#tipo_incidencia").selectpicker('refresh');
        $("#estado").val(data.estado);
        $("#estado").selectpicker('refresh');
        $("#descripcion").val(data.descripcion);
        $("#observaciones").val(data.observaciones);
    });
}

function eliminar(idincidencia) {
    bootbox.confirm("¿Estás seguro de eliminar la incidencia?", function (result) {
        if (result) {
            $.post("../ajax/incidencia.php?op=eliminar", { idincidencia: idincidencia }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

init();