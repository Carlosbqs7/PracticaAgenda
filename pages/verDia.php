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
	</head>
	<body>
		<?php
		session_start();
		if (isset($_SESSION['mail'])) {
			header("location: agenda.php");
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
				<form action="../index.php">
					<button class="btn btn-outline-success my-2 my-sm-0" type="submit">Login</button>
				</form>
			</div>
		</nav>
		<div class="linea">
			<div class="izq">
				<h5>
					Hola
				</h5>
				<form action="misCitas.php">
					<button class="btn btn-lg btn-primary btn-block" type="submit">Mis citas</button>
				</form>
				<form action="agenda.php">
					<button class="btn btn-lg btn-primary btn-block" type="submit">Calendario</button>
				</form>
			</div>
			<div class="der">
				<form action="agendaPedirCita.php" method="post">
					<?php
					include("../app/safemysql.php");
					$opts = array(
						'db' => 'tweb'
					);
					$db = new SafeMySQL($opts);
					$citas = $db->getAll("SELECT * FROM citas");
          $hoy = getdate();
          $dia = $hoy['mday'];
          $mes = $hoy['mon'];
          $year = $hoy['year'];
          if($dia<10) $dia = '0' . $dia;
          if($mes<10) $mes = '0' . $mes;
          $fechaDeHoy = $year . "-" . $mes . "-" . $dia;
					$fecha = substr($_GET['dia'], 6, 4) . "-" . substr($_GET['dia'], 3, 2) . "-" . substr($_GET['dia'], 0, 2);
					if(!is_numeric(substr($fecha, 8, 2))
					|| !is_numeric(substr($fecha, 5, 2))
					|| !is_numeric(substr($fecha, 0, 4))
					|| substr($fecha, 4, 1) != "-"
					|| substr($fecha, 7, 1) != "-") {
						header("location: verDia.php?dia=" . substr($fechaDeHoy, 8, 2) . "/" . substr($fechaDeHoy, 5, 2) . "/" . substr($fechaDeHoy, 0, 4));
						exit;
					} elseif(substr($fecha, 5, 2) == 2) {
						if(substr($fecha, 0, 4) % 4 == 0) {
							if(substr($fecha, 8, 2) > 29) {
								header("location: verDia.php?dia=" . substr($fechaDeHoy, 8, 2) . "/" . substr($fechaDeHoy, 5, 2) . "/" . substr($fechaDeHoy, 0, 4));
								exit;
							}
						} else {
							if(substr($fecha, 8, 2) > 28) {
								header("location: verDia.php?dia=" . substr($fechaDeHoy, 8, 2) . "/" . substr($fechaDeHoy, 5, 2) . "/" . substr($fechaDeHoy, 0, 4));
								exit;
							}
						}
					} elseif(substr($fecha, 5, 2) == 1 || substr($fecha, 5, 2) == 3
									|| substr($fecha, 5, 2) == 5 || substr($fecha, 5, 2) == 7
									|| substr($fecha, 5, 2) == 8 || substr($fecha, 5, 2) == 10
									|| substr($fecha, 5, 2) == 12) {
						if(substr($fecha, 8, 2) > 30) {
							header("location: verDia.php?dia=" . substr($fechaDeHoy, 8, 2) . "/" . substr($fechaDeHoy, 5, 2) . "/" . substr($fechaDeHoy, 0, 4));
							exit;
						}
					}
					if(date_parse($fecha) < date_parse($fechaDeHoy)) {
						header("location: verDia.php?dia=" . substr($fechaDeHoy, 8, 2) . "/" . substr($fechaDeHoy, 5, 2) . "/" . substr($fechaDeHoy, 0, 4));
						exit;
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
								if($cita['dia'] == $fecha && $cita['hora'] == $i ) {
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
					<button class='btn btn-lg btn-primary btn-block botonRojo'>No disponible</button>
					<button class='btn btn-lg btn-primary btn-block botonBlanco'>Disponible</button>
				</div>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	</body>
</html>
