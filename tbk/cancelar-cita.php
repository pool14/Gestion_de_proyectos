<?php
session_start();

// Verificar si el usuario ha iniciado sesión como cliente
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'cliente') {
  header("Location: login.php");
  exit();
}

// Conectar a la base de datos
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "tbk";

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Obtener el ID de la cita a cancelar
$id_cita = $_POST['id_cita'];

// Obtener el ID del horario asociado a la cita
$sqlHorario = "SELECT id_horario FROM Citas WHERE id_cita = $id_cita";
$resultadoHorario = mysqli_query($conexion, $sqlHorario);
$rowHorario = mysqli_fetch_assoc($resultadoHorario);
$id_horario = $rowHorario['id_horario'];

// Actualizar la disponibilidad del horario a "disponible"
$sqlUpdateHorario = "UPDATE Horarios SET disponibilidad = 'disponible' WHERE id_horario = $id_horario";
mysqli_query($conexion, $sqlUpdateHorario);

// Eliminar la cita
$sqlEliminarCita = "DELETE FROM Citas WHERE id_cita = $id_cita";
mysqli_query($conexion, $sqlEliminarCita);

// Redireccionar a citas-agendadas.php
header("Location: citas-agendadas.php");
exit();
?>
