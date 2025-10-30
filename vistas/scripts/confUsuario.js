function init() {
	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$('#mPerfilUsuario').addClass("treeview active");
	$('#lConfUsuario').addClass("active");

	mostrar();
}

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/configuracion.php?op=guardaryeditarPerfil",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datas) {
			if (datas == "El nombre que ha ingresado ya existe." || datas == "El número de documento que ha ingresado ya existe." || datas == "El email que ha ingresado ya existe." || datas == "El nombre del usuario que ha ingresado ya existe." || datas == "El código de estudiante que ha ingresado ya existe.") {
				bootbox.alert(datas);
				$("#btnGuardar").prop("disabled", false);
				return;
			}
			bootbox.alert(datas);
			actualizarInfoUsuario();
			$("#btnGuardar").prop("disabled", false);
		}
	});
}

function actualizarInfoUsuario() {
	$.ajax({
		url: "../ajax/configuracion.php?op=actualizarSession",
		dataType: 'json',
		success: function (data) {
			console.log(data)
			$('.user-image, .img-circle').attr('src', '../files/usuarios/' + data.imagen);
			$('.user-menu .user').html(data.nombre + ' - ' + '<strong> Rol: ' + data.cargo + '</strong>');
			$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
		}
	});
}

function mostrar() {
	$.post("../ajax/configuracion.php?op=mostrarPerfil", function (data, status) {
		data = JSON.parse(data);
		console.log(data);
		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#login").val(data.login);
		$("#clave").val(data.clave);
		$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
		$("#imagenmuestra").show();
		$("#imagenactual").val(data.imagen);

		if (data.cargo == 'estudiante' && data.idestudiante) {
			$("#codigo_estudiante").val(data.codigo_estudiante);
			$("#facultad").val(data.facultad);
			$("#carrera").val(data.carrera);
			$("#ciclo").val(data.ciclo);
		}
	});
}

init();