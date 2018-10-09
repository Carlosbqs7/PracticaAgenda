<!DOCTYPE html>
<html lang="es">
	<head>
		<title>Agenda Virtual</title>

		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<link rel="stylesheet" href="../css/agendaPedirCita.css">

		<link rel="shortcut icon" href="../images/icon.png">
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
						<a class="nav-link" href="agenda.php">Inicio <span class="sr-only">(current)</span></a>
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
				<form action="agenda.php">
					<button class="btn btn-lg btn-primary btn-block" type="submit">Calendario</button>
				</form>
			</div>
			<div class="der">
				<form action="../app/anadirCita.php" method="post">
					<?php
					include("../app/safemysql.php");
					$opts = array(
						'db' => 'tweb'
					);
					$db = new SafeMySQL($opts);
					$citas = $db->getAll("SELECT * FROM citas WHERE dia=?s AND hora=?s" , $_POST['dia'], $_POST['hora']);
					if($_SERVER["REQUEST_METHOD"] == "POST") {
						if ($_POST['hora'] != null) {
							if($citas==null) {
								$hoy = getdate();
								$dia = $hoy['mday'];
								$mes = $hoy['mon'];
								$year = $hoy['year'];
								if($dia<10) $dia = '0' . $dia;
								if($mes<10) $mes = '0' . $mes;
								$fechaDeHoy = $year . "-" . $mes . "-" . $dia;
								if(($fechaDeHoy==$_POST['dia'] && $hoy['hours']>=($_POST['hora']-1)) || date_parse($fechaDeHoy)>date_parse($_POST['dia'])) {
									header("location: agenda.php");
									exit;
								}
								$dia = $_POST['dia'];
								$hora = $_POST['hora'];
								if(!checkdate(substr($dia, 5, 2), substr($dia, 8, 2), substr($dia, 0, 4))) {
							    header("location: ../pages/agenda.php");
							    exit;
							  }
								if ($hora<10) {
									echo "<h4>Cita para el dia " . substr($dia, 8, 2) . "-" . substr($dia, 5, 2) . "-" . substr($dia, 0, 4) . " a las 0" . $hora . ":00 horas</h4>";
								} else {
									echo "<h4>Cita para el dia " . substr($dia, 8, 2) . "-" . substr($dia, 5, 2) . "-" . substr($dia, 0, 4) . " a las " . $hora . ":00 horas</h4>";
								}
								echo "<input type='hidden' name='dia' value='" . $dia . "'>";
								echo "<input type='hidden' name='hora' value='" . $hora . "'>";
							} else {
								header("location: agenda.php");
								exit;
							}
						} else {
							header("location: agenda.php");
							exit;
						}
					} else {
						header("location: agenda.php");
						exit;
					}
					?>
					<label>Asunto:</label><br>
					<textarea name="comentarios" rows="5" cols="40"></textarea>
					<button class="btn btn-lg btn-primary btn-block nueva" type="submit">Añadir cita</button>
				</form>
			</div>
		</div>

		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	</body>
</html>
