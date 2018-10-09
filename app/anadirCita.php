<?php
session_start();
include("safemysql.php");
$opts = array(
  'db' => 'tweb'
);
$db = new SafeMySQL($opts);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $hoy = getdate();
  $dia = $hoy['mday'];
  $mes = $hoy['mon'];
  $year = $hoy['year'];
  if($dia<10) $dia = '0' . $dia;
  if($mes<10) $mes = '0' . $mes;
  $fechaDeHoy = $year . "-" . $mes . "-" . $dia;
  if(($fechaDeHoy==$_POST['dia'] && $hoy['hours']>=($_POST['hora']-1)) || date_parse($fechaDeHoy)>date_parse($_POST['dia'])) {
    header("location: ../pages/agendaPedirCita.php");
    exit;
  }
  if(!checkdate(substr($_POST['dia'], 3, 2), substr($_POST['dia'], 0, 2), substr($_POST['dia'], 6, 4))) {
    header("location: ../pages/agendaPedirCita.php");
    exit;
  }
  $mail = $_SESSION['mail'];
  $dia = $_POST['dia'];
  $hora = $_POST['hora'];
  $citas = $db->getAll("SELECT * FROM citas WHERE dia=?s AND hora=?i" , $_POST['dia'], $_POST['hora']);
  if($citas != null) {
    header("location: ../pages/agendaPedirCita.php");
    exit;
  }
  if($hora<6 || $hora>21) {
    header("location: ../pages/agendaPedirCita.php");
    exit;
  }
  if(!isset($_POST['comentarios'])) {
    $sql = "INSERT INTO citas SET usuario_mail=?s, dia=?s, hora=?s";
    $db->query($sql, $mail, $dia, $hora);
  } else {
    $asunto = strip_tags($_POST['comentarios']);
    $sql = "INSERT INTO citas SET usuario_mail=?s, dia=?s, hora=?s, asunto=?s";
    $db->query($sql, $mail, $dia, $hora, $asunto);
  }
  header("location: ../pages/agenda.php");
  exit;
} else {
  header("location: ../pages/agendaPedirCita.php");
  exit;
}
?>
