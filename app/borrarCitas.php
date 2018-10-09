<?php
session_start();
include("safemysql.php");
$opts = array(
  'db' => 'tweb'
);
$mail = $_SESSION['mail'];
$hoy = getdate();
$dia = $hoy['mday'];
$mes = $hoy['mon'];
$year = $hoy['year'];
if($dia<10) $dia = '0' . $dia;
if($mes<10) $mes = '0' . $mes;
$fechaDeHoy = $year . "-" . $mes . "-" . $dia;
if((date_parse($fechaDeHoy)==date_parse($_GET['dia']) && $hoy['hours']>=$_GET['hora']-1) || date_parse($fechaDeHoy)>date_parse($_GET['dia'])) {
  header("location: ../pages/misCitas.php");
  exit;
} else {
  $db = new SafeMySQL($opts);
  $dia = $_GET['dia'];
  $hora = $_GET['hora'];
  $db->query("DELETE FROM citas WHERE dia=?s AND hora=?s AND usuario_mail=?s", $dia, $hora, $mail);
  header("location: ../pages/misCitas.php");
  exit;
}

?>
