var tabla;
var fecha_inicio = "";
var fecha_fin = "";
var tipoMenuBuscar = "";
var estadoBuscar = "";

function init() {
    mostrarform(false);
    listar();

    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });

    $("#imagenmuestra").hide();
    $('#mGestionMenus').addClass("active");

    $("#imagen").change(function () {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $("#imagenmuestra").attr("src", e.target.result);
                $("#imagenmuestra").show();
            };
            reader.readAsDataURL(file);
        }
    });
}

function limpiar() {
    $("#idmenu").val("");
    $("#titulo").val("");
    $("#descripcion").val("");
    $("#precio").val("");
    $("#fecha_disponible").val("");
    $("#tipo_menu").val("almuerzo");
    $("#tipo_menu").selectpicker('refresh');
    $("#imagen").val("");
    $("#imagenmuestra").attr("src", "");
    $("#imagenmuestra").hide();
    $("#imagenactual").val("");
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
            url: '../ajax/menu.php?op=listar',
            type: "post",
            dataType: "json",
            data: function (d) {
                d.fecha_inicio = fecha_inicio;
                d.fecha_fin = fecha_fin;
                d.tipoMenuBuscar = tipoMenuBuscar;
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
    tipoMenuBuscar = $("#tipoMenuBuscar").val();
    estadoBuscar = $("#estadoBuscar").val();
    tabla.ajax.reload();
}

function resetear() {
    $("#fecha_inicio").val("");
    $("#fecha_fin").val("");
    $("#tipoMenuBuscar").val("");
    $("#tipoMenuBuscar").selectpicker('refresh');
    $("#estadoBuscar").val("");
    $("#estadoBuscar").selectpicker('refresh');
    fecha_inicio = "";
    fecha_fin = "";
    tipoMenuBuscar = "";
    estadoBuscar = "";
    tabla.ajax.reload();
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/menu.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            if (datos == "El título del menú que ha ingresado ya existe para esa fecha." || datos == "La fecha disponible debe ser al menos 1 día después de hoy.") {
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

function mostrar(idmenu) {
    $.post("../ajax/menu.php?op=mostrar", { idmenu: idmenu }, function (data, status) {
        data = JSON.parse(data);
        console.log(data);
        mostrarform(true);

        $("#idmenu").val(data.idmenu);
        $("#titulo").val(data.titulo);
        $("#descripcion").val(data.descripcion);
        $("#precio").val(data.precio);
        $("#fecha_disponible").val(data.fecha_disponible);
        $("#tipo_menu").val(data.tipo_menu);
        $("#tipo_menu").selectpicker('refresh');
        $("#imagenactual").val(data.imagen);

        if (data.imagen != "" && data.imagen != null) {
            $("#imagenmuestra").show();
            $("#imagenmuestra").attr("src", "../files/menus/" + data.imagen);
        } else {
            $("#imagenmuestra").hide();
        }
    });
}

function desactivar(idmenu) {
    bootbox.confirm("¿Está seguro de desactivar el menú?", function (result) {
        if (result) {
            $.post("../ajax/menu.php?op=desactivar", { idmenu: idmenu }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function activar(idmenu) {
    bootbox.confirm("¿Está seguro de activar el menú?", function (result) {
        if (result) {
            $.post("../ajax/menu.php?op=activar", { idmenu: idmenu }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function eliminar(idmenu) {
    bootbox.confirm("¿Estás seguro de eliminar el menú?", function (result) {
        if (result) {
            $.post("../ajax/menu.php?op=eliminar", { idmenu: idmenu }, function (e) {
                bootbox.alert(e);
                tabla.ajax.reload();
            });
        }
    });
}

function evitarNegativo(event) {
    if (event.key === '-' || event.key === 'e' || event.key === '+') {
        event.preventDefault();
    }
}

init();