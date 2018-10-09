<!doctype html>
<html lang="en">
	<head>
		<title>Agenda Virtual</title>

		<meta charset="UTF-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<link rel="stylesheet" href="../css/registrarse.css">

		<link rel="shortcut icon" href="../images/icon.png">
	</head>
	<body>
		<div class="container">
			<form method="post" class="form-signin">
				<h2 class="form-signin-heading">Introduzca sus datos</h2>
				<label for="inputNombre" class="sr-only">Nombre</label>
				<input name="nomb" type="text" id="inputNombre" class="form-control" placeholder="Nombre" required autofocus>
				<label for="inputEmail" class="sr-only">Email</label>
				<input name="mail" type="email" id="inputEmail" class="form-control" placeholder="Email" required>
				<label for="inputPassword" class="sr-only">Contraseña</label>
				<input name="cont" type="password" id="inputPassword" class="form-control" placeholder="Contraseña" required>
				<label for="inputPassword2" class="sr-only">Contraseña2</label>
				<input name="cont2" type="password" id="inputPassword2" class="form-control" placeholder="Repetir contraseña" required>
				<button class="btn btn-lg btn-primary btn-block" type="submit">Registrarse</button>
			</form>
			<form class="form-signin" action="../index.php">
				<button class="btn btn-lg btn-primary btn-block" type="submit">Ya estoy registrado</button>
			</form>
		</div> <!-- /container -->
		<?php
		include("../app/safemysql.php");
		$opts = array(
			'db' => 'tweb'
		);
		$db = new SafeMySQL($opts);
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			// recoge en $nombre el valor del campo input del formulario
			$mail = $_POST['mail'];
			$nombre = strip_tags($_POST['nomb']);
			unset($compruebaMail);
			$compruebaMail  = $db->getOne("SELECT mail FROM usuarios WHERE mail = ?s",$mail);
			if($mail == "" || $nombre == "" || $_POST['cont'] == "" || $_POST['cont2'] == "") {
				echo '<script language="javascript">alert("Ningún dato puede ser nulo");</script>';
			} elseif (strlen($nombre)>15) {
				echo '<script language="javascript">alert("El nombre no puede contener más de 10 caracteres");</script>';
			} elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
				echo '<script language="javascript">alert("El formato de e-mail no es válido");</script>';
			} else {
				if ($compruebaMail != $mail) {
					$passw = hash('sha256', $_POST['cont']);
					$passw2 = hash('sha256', $_POST['cont2']);
					if ($passw!==$passw2) {
						echo '<script language="javascript">alert("Las contraseñas no son iguales");</script>';
					} else {
						$sql = "INSERT INTO usuarios SET mail=?s, contraseña=?s, nombre=?s";
						$db->query($sql, $mail, $passw, $nombre);
						echo '<script language="javascript">alert("Registrado con éxito");</script>';
					}
				} else {
					echo '<script language="javascript">alert("Ya hay un usuario registrado con ese correo");</script>';
				}
			}
			unset($compruebaMail);
		}
		?>
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	</body>
</html>
