<!doctype html>
<html lang="en">
	<head>
		<title>Agenda Virtual</title>

		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<link rel="stylesheet" href="./css/login.css">

		<link rel="shortcut icon" href="images/icon.png">

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
		<div class="container">
			<form method="post" class="form-signin">
				<h2 class="form-signin-heading">Introduzca sus datos</h2>
				<label for="inputEmail" class="sr-only">Email</label>
				<input name="mail" type="email" id="inputEmail" class="form-control" placeholder="Email" required autofocus>
				<label for="inputPassword" class="sr-only">Contraseña</label>
				<input name="cont" type="password" id="inputPassword" class="form-control" placeholder="Contraseña" required>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Login</button>
			</form>
			<form class="form-signin" action="pages/registrarse.php">
				<button class="btn btn-lg btn-primary btn-block" type="submit">Registrarse</button>
			</form>
			<form class="form-signin" action="pages/verDia.php" method="get">
				<h4>
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
				</h4>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Ver el día</button>
			</form>

		</div> <!-- /container -->
		<?php
		include("app/safemysql.php");
		$opts = array(
			'db' => 'tweb'
		);
		$db = new SafeMySQL($opts);
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			// recoge en $nombre el valor del campo input del formulario
			$mail = $_POST['mail'];
			unset($compruebaMail);
			$compruebaMail = $db->getOne("SELECT mail FROM usuarios WHERE mail = ?s",$mail);
			if ($compruebaMail === $mail) {
				$passw = hash('sha256', $_POST['cont']);
				$compruebaPassw = $db->getOne("SELECT contraseña FROM usuarios WHERE mail = ?s",$mail);
				if ($passw == $compruebaPassw) {
					$nombre = $db->getOne("SELECT nombre FROM usuarios WHERE mail = ?s",$mail);
					session_start();
					$_SESSION['mail'] = $mail;
					$_SESSION['nombre'] = $nombre;
					header('location: pages/agenda.php');
					exit;
				} else {
					echo '<script language="javascript">alert("Contraseña incorrecta");</script>';
				}
			} else {
				echo '<script language="javascript">alert("El correo no está registrado");</script>';
			}
			unset($compruebaMail);
		}
		?>
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
	$(function () {
		$("#fecha").datepicker();
	});
	</script>
</html>
