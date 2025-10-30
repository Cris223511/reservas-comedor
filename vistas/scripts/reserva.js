var tabla;
var tabla2;
var fecha_inicio = "";
var fecha_fin = "";
var estadoPagoBuscar = "";
var estadoReservaBuscar = "";
var cont = 0;
var detalles = 0;

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $("#formularioActualizar").on("submit", function (e) {
        actualizarReserva(e);
    });

    $("#btnGuardar").hide();
    $('#mReservas').addClass("active");
}

function limpiar() {
    $("#idreserva").val("");
    $("#detalles tbody").html("");
    cont = 0;
    detalles = 0;
    evaluar();
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
            url: '../ajax/reserva.php?op=listar',
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

function abrirModalMenus() {
    $('#modalMenus').modal('show');
    listarMenus();
}

function listarMenus() {
    tabla2 = $('#tblmenus').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "dom": 'Bfrtip',
        "buttons": [],
        "ajax": {
            url: '../ajax/reserva.php?op=listarMenusDisponibles',
            type: "GET",
            dataType: "json",
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "iDisplayLength": 5,
        "order": [],
        "drawCallback": function (settings) {
            $('#tblmenus button[data-idmenu]').removeAttr('disabled');
            var detallesActuales = getDetalles();
            for (var i = 0; i < detallesActuales.length; i++) {
                var idmenu = detallesActuales[i].idmenu;
                $('#tblmenus button[data-idmenu="' + idmenu + '"]').attr('disabled', true);
            }
        }
    });
}

function getDetalles() {
    var detallesArr = [];
    $("#detalles tbody tr").each(function (index) {
        var detalle = {
            idmenu: $(this).find("input[name='idmenu[]']").val()
        };
        detallesArr.push(detalle);
    });
    return detallesArr;
}

function agregarDetalle(idmenu, titulo, descripcion, precio, imagen) {
    if (detalles >= 1) {
        bootbox.alert("Solo puede seleccionar 1 menú por reserva");
        return;
    }

    if (idmenu != "") {
        var fila = '<tr class="filas" id="fila' + cont + '">' +
            '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' + cont + ', ' + idmenu + ')">X</button></td>' +
            '<td><a href="../files/menus/' + imagen + '" class="galleria-lightbox" style="z-index: 10000 !important;"><img src="../files/menus/' + imagen + '" height="50px" width="50px" class="img-fluid"></a></td>' +
            '<td><input type="hidden" name="idmenu[]" value="' + idmenu + '">' + titulo + '</td>' +
            '<td>' + descripcion + '</td>' +
            '<td>S/. ' + precio + '</td>' +
            '</tr>';
        cont++;
        detalles = detalles + 1;
        $('#detalles tbody').append(fila);
        evaluar();
        $('#modalMenus').modal('hide');
    } else {
        alert("Error al agregar el menú");
    }
}

function evaluar() {
    if (detalles > 0) {
        $("#btnGuardar").show();
        $("#btnGuardar").prop("disabled", false);
    } else {
        $("#btnGuardar").prop("disabled", false);
        $("#btnGuardar").hide();
        cont = 0;
    }
}

function eliminarDetalle(indice, idmenu) {
    $("#fila" + indice).remove();
    $('#tblmenus button[data-idmenu="' + idmenu + '"]').removeAttr('disabled');
    detalles = detalles - 1;
    evaluar();
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/reserva.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            if (datos.indexOf("ERROR:") !== -1 || datos.indexOf("Ya tiene una reserva") !== -1 || datos.indexOf("No hay cupos") !== -1) {
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

function mostrarActualizar(idreserva) {
    $.post("../ajax/reserva.php?op=mostrar", { idreserva: idreserva }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);

        $("#idreserva_actualizar").val(data.idreserva);
        $("#codigo_reserva_mostrar").val(data.codigo_reserva);
        $("#estado_pago").val(data.estado_pago);
        $("#estado_pago").selectpicker('refresh');
        $("#estado_reserva").val(data.estado_reserva);
        $("#estado_reserva").selectpicker('refresh');
        $("#metodo_pago").val(data.metodo_pago);
        $("#observaciones_actualizar").val(data.observaciones);
        $("#comprobante_actual").val(data.comprobante_pago);

        if (data.comprobante_pago != "" && data.comprobante_pago != null) {
            $("#ver_comprobante").attr("href", "../files/comprobantes/" + data.comprobante_pago);
            $("#ver_comprobante").show();
        } else {
            $("#ver_comprobante").hide();
        }

        $('#modalActualizar').modal('show');
    });
}

function actualizarReserva(e) {
    e.preventDefault();
    $("#btnActualizar").prop("disabled", true);
    var formData = new FormData($("#formularioActualizar")[0]);

    $.ajax({
        url: "../ajax/reserva.php?op=actualizarReserva",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            bootbox.alert(datos);
            $("#btnActualizar").prop("disabled", false);
            $('#modalActualizar').modal('hide');
            tabla.ajax.reload();
        }
    });
}

function cancelarReserva(idreserva) {
    bootbox.prompt({
        title: "¿Está seguro de cancelar la reserva? Ingrese el motivo:",
        inputType: 'textarea',
        callback: function (motivo) {
            if (motivo !== null) {
                if (motivo.trim() == "") {
                    bootbox.alert("Debe ingresar un motivo de cancelación");
                    return;
                }
                $.post("../ajax/reserva.php?op=cancelarReserva", { idreserva: idreserva, motivo_cancelacion: motivo }, function (e) {
                    bootbox.alert(e);
                    tabla.ajax.reload();
                });
            }
        }
    });
}

function eliminar(idreserva) {
    bootbox.confirm("¿Estás seguro de eliminar la reserva?", function (result) {
        if (result) {
            $.post("../ajax/reserva.php?op=eliminar", { idreserva: idreserva }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

init();