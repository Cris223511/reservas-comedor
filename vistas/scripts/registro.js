$("#frmRegistro").on('submit', function (e) {
    e.preventDefault();
    $("#btnGuardar").prop("disabled", true);

    var formData = new FormData($("#frmRegistro")[0]);

    $.ajax({
        url: "../ajax/usuario.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            datos = limpiarCadena(datos);
            console.log(datos);

            if (datos == "El número de documento que ha ingresado ya existe.") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El número de documento ya está registrado en el sistema.',
                });
                $("#btnGuardar").prop("disabled", false);
            } else if (datos == "El nombre del usuario que ha ingresado ya existe.") {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El nombre de usuario ya existe. Por favor, elige otro.',
                });
                $("#btnGuardar").prop("disabled", false);
            } else if (datos == "Usuario registrado") {
                Swal.fire({
                    icon: 'success',
                    title: 'Registro exitoso',
                    text: 'Tu cuenta ha sido creada correctamente. Redirigiendo al login...'
                });
                setTimeout(function () {
                    $(location).attr("href", "login.html");
                }, 2000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo registrar el usuario. Intenta nuevamente.',
                });
                $("#btnGuardar").prop("disabled", false);
            }
        },
        error: function (e) {
            console.log(e.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error del servidor',
                text: 'Hubo un problema al procesar tu solicitud.',
            });
            $("#btnGuardar").prop("disabled", false);
        }
    });
});

function mostrarOcultarClave() {
    var claveInput = $('#clave');

    if ($('#mostrarClave').is(':checked')) {
        claveInput.attr('type', 'text');
    } else {
        claveInput.attr('type', 'password');
    }
}

function mostrar() {
    $.post("../ajax/verPortada.php?op=mostrar", function (datas, status) {
        data = JSON.parse(datas);
        if (data != null) {
            console.log(data.imagen);
            $("#imagenmuestra").attr("src", "../files/portadas/" + data.imagen);
            $(".fondo-login").css("background-image", "url('../files/portadas/" + data.imagen + "')");
        } else {
            $("#imagenmuestra").attr("src", "../files/portadas/portada_default.jpg");
            $(".fondo-login").css("background-image", "url('../files/portadas/portada_default.jpg')");
        }
    });
}

function limpiarCadena(cadena) {
    let cadenaLimpia = cadena.trim();
    cadenaLimpia = cadenaLimpia.replace(/^[\n\r]+/, '');
    return cadenaLimpia;
}

function changeValue(dropdown) {
    var option = dropdown.options[dropdown.selectedIndex].value;

    console.log(option);

    $("#num_documento").val("");

    if (option == 'DNI') {
        setMaxLength('#num_documento', 8);
    } else if (option == 'CEDULA') {
        setMaxLength('#num_documento', 10);
    } else {
        setMaxLength('#num_documento', 11);
    }
}

function setMaxLength(fieldSelector, maxLength) {
    $(fieldSelector).attr('maxLength', maxLength);
}

mostrar();

function evitarCaracteresEspecialesCamposNumericos() {
    var camposNumericos = document.querySelectorAll('input[type="number"]');

    camposNumericos.forEach(function (campo) {
        campo.addEventListener('keydown', function (event) {
            var teclasPermitidas = [46, 8, 9, 27, 13, 110, 190, 37, 38, 39, 40, 17, 82]; // ., delete, tab, escape, enter, flechas, Ctrl+R

            // Permitir Ctrl+C, Ctrl+V, Ctrl+X y Ctrl+A
            if ((event.ctrlKey || event.metaKey) && (event.which === 67 || event.which === 86 || event.which === 88 || event.which === 65)) {
                return;
            }

            // Permitir Ctrl+Z y Ctrl+Alt+Z
            if ((event.ctrlKey || event.metaKey) && event.which === 90) {
                if (!event.altKey) {
                    // Permitir Ctrl+Z
                    return;
                } else if (event.altKey) {
                    // Permitir Ctrl+Alt+Z
                    return;
                }
            }

            if (teclasPermitidas.includes(event.which) || (event.which >= 48 && event.which <= 57) || (event.which >= 96 && event.which <= 105) || event.which === 190 || event.which === 110) {
                // Si es una tecla permitida o numérica, no hacer nada
                return;
            } else {
                event.preventDefault(); // Prevenir cualquier otra tecla no permitida
            }
        });
    });
}

evitarCaracteresEspecialesCamposNumericos();

function convertirMayus(inputElement) {
    if (typeof inputElement.value === 'string') {
        inputElement.value = inputElement.value.toUpperCase();
    }
}
