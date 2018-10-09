<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Agenda Virtual</title>

		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<link rel="stylesheet" href="../css/agenda.css">

		<link rel="shortcut icon" href="../images/icon.png">

		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
		<script>
		$(function () {
		$('#fecha').datepicker();
		});
		</script>
	</head>
	<body>
		<?php
		session_start();
		if (!isset($_SESSION['mail'])) {
			header("location: ../index.php");
			exit;
		}
		?>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="#">Agenda Virtual</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item active">
						<a class="nav-link" href="#">Inicio <span class="sr-only">(current)</span></a>
					</li>
				</ul>
				<form action="../app/cerrarSesion.php">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Log out</button>
				</form>
			</div>
		</nav>
		<div class="linea">
			<div class="izq">
				<h5>
					Hola
					<?php echo $_SESSION['nombre']; ?>
				</h5>
				<form action="misCitas.php">
					<button class="btn btn-lg btn-primary btn-block" type="submit">Mis citas</button>
				</form>
				<form action="#">
					<button class="btn btn-lg btn-primary btn-block" type="submit">Calendario</button>
				</form>
				<form action="agenda.php" method="get">
					<h4>Día
						<label for="fecha">
							<input type="text" name="dia" id="fecha" value="<?php $hoy = getdate();
										if($hoy['hours']>=20) {
											$hoy['mday'] = $hoy['mday'] + 1;
										}
										$dia = $hoy['mday'];
										$mes = $hoy['mon'];
										$year = $hoy['year'];
										if($dia<10) $dia = '0' . $dia;
										if($mes<10) $mes = '0' . $mes;
										echo $dia . '/' . $mes . '/' . $year;
										?>" required />
						</label>
					</h4>
					<button class="btn btn-lg btn-primary btn-block" type="submit">Ir al día actual</button>
				</form>
			</div>
			<div class="der">
				<form action="agendaPedirCita.php" method="post">
					<?php
					include("../app/safemysql.php");
					$opts = array(
						'db' => 'tweb'
					);
					$fechaDeHoy = $year . "-" . $mes . "-" . $dia;
					$db = new SafeMySQL($opts);
					$citas = $db->getAll("SELECT * FROM citas");
					if (isset($_GET['dia'])) {
						if(substr($_GET['dia'], 2, 1) != "/" || substr($_GET['dia'], 5, 1) != "/") {
							header("location: agenda.php");
							exit;
						}
						$fecha = substr($_GET['dia'], 6, 4) . "-" . substr($_GET['dia'], 3, 2) . "-" . substr($_GET['dia'], 0, 2);
						if(!is_numeric(substr($fecha, 8, 2))
						|| !is_numeric(substr($fecha, 5, 2))
						|| !is_numeric(substr($fecha, 0, 4))
						|| substr($fecha, 4, 1) != "-"
						|| substr($fecha, 7, 1) != "-") {
							header("location: agenda.php");
							exit;
						} elseif(substr($fecha, 5, 2) == 2) {
							if(substr($fecha, 0, 4) % 4 == 0) {
								if(substr($fecha, 8, 2) > 29) {
									header("location: agenda.php");
									exit;
								}
							} else {
								if(substr($fecha, 8, 2) > 28) {
									header("location: agenda.php");
									exit;
								}
							}
						} elseif(substr($fecha, 5, 2) == 4 || substr($fecha, 5, 2) == 6
										|| substr($fecha, 5, 2) == 9 || substr($fecha, 5, 2) == 11) {
							if(substr($fecha, 8, 2) > 30) {
								header("location: agenda.php");
								exit;
							}
						} else {
							if(substr($fecha, 8, 2) > 31) {
								header("location: agenda.php");
								exit;
							}
						}
						if(date_parse($fecha) < date_parse($fechaDeHoy)) {
							header("location: agenda.php");
							exit;
						}
						if(!checkdate(substr($_GET['dia'], 3, 2), substr($_GET['dia'], 0, 2), substr($_GET['dia'], 6, 4))) {
							header("location: agenda.php");
							exit;
						}
					} else {
						$dia = $hoy['mday'];
						$mes = $hoy['mon'];
						$year = $hoy['year'];
						if($dia<10) $dia = '0' . $dia;
						if($mes<10) $mes = '0' . $mes;
						$fecha = $year . "-" . $mes . "-" . $dia;
					}
					echo "<h4>Día " . substr($fecha, 8, 2) . "-" . substr($fecha, 5, 2) . "-" . substr($fecha, 0, 4) . "</h4>";
					echo "<input type='hidden' name='dia' value='" . $fecha . "'>";
					echo "<div class='horas'>";
					$clase = "hora";
					$tipo = "submit";
					for ($i = 6; $i <= 21; $i++) {
						if(empty($citas)) {
							if($hoy['hours'] >= ($i-1)) {
								$clase = "horaRojo";
								$tipo = "button";
							} else {
								$clase = "hora";
								$tipo = "submit";
							}
						} else {
							foreach ($citas as $cita) {
								if($cita['dia'] == $fecha && $cita['hora'] == $i && $cita['usuario_mail'] == $_SESSION['mail']) {
									$clase = "horaAzul";
									$tipo = "button";
									break;
								} else if($cita['dia'] == $fecha && $cita['hora'] == $i ) {
									$clase = "horaRojo";
									$tipo = "button";
									break;
								} else {
									$hoy = getdate();
									$dia = $hoy['mday'];
									$mes = $hoy['mon'];
									$year = $hoy['year'];
									if($dia<10) $dia = '0' . $dia;
									if($mes<10) $mes = '0' . $mes;
									$fechaDeHoy = $year . "-" . $mes . "-" . $dia;
									if(date_parse($fecha) == date_parse($fechaDeHoy)) {
										if($hoy['hours']>=($i-1)) {
											$clase = "horaRojo";
											$tipo = "button";
										} else {
											$clase = "hora";
											$tipo = "submit";
										}
									} else {
										$clase = "hora";
										$tipo = "submit";
									}
								}
							}
						}
						if ($i<10) {
							echo "<button class='btn btn-lg btn-primary btn-block " . $clase . "' name='hora' value='" . $i . "' type='" . $tipo . "'>0" . $i . ":00</button>";
						} else {
							echo "<button class='btn btn-lg btn-primary btn-block " . $clase . "' name='hora' value='" . $i . "' type='" . $tipo . "'>" . $i . ":00</button>";
						}
					}
					?>
					</div>
				</form>
				<div class="leyenda">
					<button class='btn btn-lg btn-primary btn-block botonAzul'>Tus citas</button>
					<button class='btn btn-lg btn-primary btn-block botonRojo'>No disponible</button>
					<button class='btn btn-lg btn-primary btn-block botonBlanco'>Disponible</button>
				</div>
			</div>
		</div>

	</body>
	<script>
	var f = new Date();
	cad = f.getHours();
	if(cad>=20) {
		f.setDate(f.getDate() + 1);
	}
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '< Ant',
		nextText: 'Sig >',
		currentText: 'Hoy',
		monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		minDate: f,
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['es']);
	$("input[name=dia]").change(function(){
		setTimeout("location.href='agenda.php?dia=" + $("input[name=dia]").val()  + "'", 0);
  });
	$(function () {
		$("#fecha").datepicker();
	});
	</script>
</html>
