<?php
session_start();

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos del formulario
  $fecha = $_POST['fecha'];
  $hora = $_POST['hora'];
  $id_servicio = $_POST['servicio'];
  $disponibilidad = $_POST['disponibilidad'];

  // Conectar a la base de datos
  $servername = "localhost:3306";
  $username = "root";
  $password = "";
  $dbname = "tbk"; // Reemplaza con el nombre de tu base de datos

  $conexion = mysqli_connect($servername, $username, $password, $dbname);

  if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
  }

  // Insertar el nuevo horario de cita en la base de datos
  $query = "INSERT INTO Horarios (id_servicio, fecha, hora, disponibilidad) VALUES ('$id_servicio', '$fecha', '$hora', '$disponibilidad')";
  $resultado = mysqli_query($conexion, $query);

  if ($resultado) {
    echo '<script>alert("Horario Agregado Correctamente"); window.location.href = "admin.php";</script>';
    exit();
  } else {
    $_SESSION['error_message'] = "Error al agregar el horario de cita: " . mysqli_error($conexion);
    header("Location: admin-edit.php?id=$id_horario");
    exit();
  }

  mysqli_close($conexion);

  header("Location: admin.php");
  exit();
}
