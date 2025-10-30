function init() {
    $("#formulario").on("submit", function (e) {
        guardaryeditar(e);
    });
    mostrar();
    $('#mConfiguracion').addClass("treeview active");
    $('#lConfSistema').addClass("active");
}

function guardaryeditar(e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);
    var formData = new FormData($("#formulario")[0]);

    $.ajax({
        url: "../ajax/configuracion.php?op=guardaryeditarSistema",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            bootbox.alert(datos);
            $("#btnGuardar").prop("disabled", false);
            mostrar();
        }
    });
}

function mostrar() {
    $.post("../ajax/configuracion.php?op=mostrarSistema", function (data, status) {
        data = JSON.parse(data);
        console.log(data);

        $("#idconfiguracion").val(data.idconfiguracion);
        $("#nombre_comedor").val(data.nombre_comedor);
        $("#universidad").val(data.universidad);
        $("#direccion").val(data.direccion);
        $("#telefono").val(data.telefono);
        $("#email").val(data.email);
        $("#logoactual").val(data.logo);

        if (data.logo != "" && data.logo != null) {
            $("#logomuestra").show();
            $("#logomuestra").attr("src", "../files/sistema/" + data.logo);
        } else {
            $("#logomuestra").hide();
        }
    });
}

init();