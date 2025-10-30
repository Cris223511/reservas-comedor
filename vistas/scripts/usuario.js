var tabla;
var cargoFiltro = "";

function init() {
	mostrarform(false);
	listar();

	$("#formulario").on("submit", function (e) {
		guardaryeditar(e);
	});

	$("#imagenmuestra").hide();

	$.post("../ajax/usuario.php?op=permisos&id=", function (r) {
		$("#permisos").html(r);
	});

	$('#mAcceso').addClass("treeview active");
	$('#lUsuarios').addClass("active");
	$("#checkAll").prop("checked", false);

	$("#cargo").on("change", function () {
		toggleCamposEstudiante();
	});

	$("#mostrarClave").on("click", function () {
		var tipo = $("#clave").attr("type");
		if (tipo == "password") {
			$("#clave").attr("type", "text");
			$(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
		} else {
			$("#clave").attr("type", "password");
			$(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
		}
	});

	$(".tab-btn").on("click", function () {
		$(".tab-btn").removeClass("active");
		$(this).addClass("active");
		cargoFiltro = $(this).data("cargo");
		tabla.ajax.reload();
	});
}

function toggleCheckboxes(checkbox) {
	var checkboxes = document.querySelectorAll('#permisos input[type="checkbox"]');
	checkboxes.forEach(function (cb) {
		cb.checked = checkbox.checked;
	});
}

function toggleCamposEstudiante() {
	var cargo = $("#cargo").val();
	if (cargo == "estudiante") {
		$("#campos_estudiante").show();
		$("#codigo_estudiante").prop("required", true);
	} else {
		$("#campos_estudiante").hide();
		$("#codigo_estudiante").prop("required", false);
		$("#codigo_estudiante").val("");
		$("#facultad").val("");
		$("#carrera").val("");
	}
}

function limpiar() {
	$("#nombre").val("");
	$("#tipo_documento").val("");
	$("#tipo_documento").selectpicker('refresh');
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#cargo").val("");
	$("#cargo").selectpicker('refresh');
	$("#login").val("");
	$("#clave").val("");
	$("#imagen").val("");
	$("#imagenmuestra").attr("src", "");
	$("#imagenmuestra").hide();
	$("#imagenactual").val("");
	$("#idusuario").val("");
	$("#checkAll").prop("checked", false);
	$("#codigo_estudiante").val("");
	$("#facultad").val("");
	$("#carrera").val("");
	$("#campos_estudiante").hide();

	$("#permisos input[type='checkbox']").each(function () {
		$(this).prop('checked', false);
	});
}

function mostrarform(flag) {
	if (flag) {
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled", false);
		$("#btnagregar").hide();
	} else {
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();

		$("#permisos input[type='checkbox']").each(function () {
			$(this).prop('checked', false);
		});
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
			url: '../ajax/usuario.php?op=listar',
			type: "post",
			dataType: "json",
			data: function (d) {
				d.cargoBuscar = cargoFiltro;
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

function guardaryeditar(e) {
	e.preventDefault();
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);

	$.ajax({
		url: "../ajax/usuario.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (datos) {
			datos = limpiarCadena(datos);
			if (datos == "El nombre del usuario que ha ingresado ya existe." || datos == "El número de documento que ha ingresado ya existe.") {
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

function mostrar(idusuario) {
	$.post("../ajax/usuario.php?op=mostrar", { idusuario: idusuario }, function (data, status) {
		data = JSON.parse(data);
		console.log(data);
		mostrarform(true);

		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#cargo").val(data.cargo);
		$("#cargo").selectpicker('refresh');
		$("#login").val(data.login);
		$("#clave").val(data.clave);
		$("#imagenmuestra").show();
		$("#imagenmuestra").attr("src", "../files/usuarios/" + data.imagen);
		$("#imagenactual").val(data.imagen);
		$("#idusuario").val(data.idusuario);
		$("#checkAll").prop("checked", false);

		if (data.cargo == "estudiante") {
			$("#codigo_estudiante").val(data.codigo_estudiante);
			$("#facultad").val(data.facultad);
			$("#carrera").val(data.carrera);
			$("#campos_estudiante").show();
			$("#codigo_estudiante").prop("required", true);
		} else {
			$("#campos_estudiante").hide();
			$("#codigo_estudiante").prop("required", false);
		}
	});

	$.post("../ajax/usuario.php?op=permisos&id=" + idusuario, function (r) {
		$("#permisos").html(r);
	});
}

function desactivar(idusuario) {
	bootbox.confirm("¿Está seguro de desactivar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=desactivar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	});
}

function activar(idusuario) {
	bootbox.confirm("¿Está seguro de activar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=activar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	});
}

function eliminar(idusuario) {
	bootbox.confirm("¿Estás seguro de eliminar el usuario?", function (result) {
		if (result) {
			$.post("../ajax/usuario.php?op=eliminar", { idusuario: idusuario }, function (e) {
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	});
}

init();
evitarCaracteresEspecialesCamposNumericos();