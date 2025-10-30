function init() {
    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });
    mostrar();
    $('#mConfiguracion').addClass("treeview active");
    $('#lConfAforo').addClass("active");
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/configuracion.php?op=guardaryeditarAforo",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            bootbox.alert(datos);
            $("#btnGuardar").prop("disabled", false);
        }
    });
}

function mostrar() {
    $.post("../ajax/configuracion.php?op=mostrarAforo", function (data, status) {
        data = JSON.parse(data);
        console.log(data);

        $("#idconfiguracion").val(data.idconfiguracion);
        $("#aforo_maximo").val(data.aforo_maximo);
        $("#dias_anticipacion").val(data.dias_anticipacion);
        $("#horas_limite_cancelacion").val(data.horas_limite_cancelacion);
        $("#hora_inicio_almuerzo").val(data.hora_inicio_almuerzo);
        $("#hora_fin_almuerzo").val(data.hora_fin_almuerzo);
        $("#whatsapp_contacto").val(data.whatsapp_contacto);
        $("#mensaje_whatsapp").val(data.mensaje_whatsapp);
    });
}

init();