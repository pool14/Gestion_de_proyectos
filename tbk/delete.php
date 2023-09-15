<?php
session_start();

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
  header("Location: login.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
  // Obtener el ID del horario a eliminar
  $id_horario = $_GET['id'];

  // Conectar a la base de datos
  $servername = "localhost:3306";
  $username = "root";
  $password = "";
  $dbname = "tbk"; // Reemplaza con el nombre de tu base de datos

  $conexion = mysqli_connect($servername, $username, $password, $dbname);

  if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
  }

  // Verificar si el horario existe antes de eliminarlo
  $query = "SELECT * FROM Horarios WHERE id_horario = '$id_horario'";
  $resultado = mysqli_query($conexion, $query);

  if (mysqli_num_rows($resultado) === 0) {
    $_SESSION['error_message'] = "El horario de cita no existe.";
    mysqli_close($conexion);
    header("Location: admin.php");
    exit();
  }

  // Eliminar el horario de cita de la base de datos
  $query = "DELETE FROM Horarios WHERE id_horario = '$id_horario'";
  $resultado = mysqli_query($conexion, $query);

  if ($resultado) {
    echo '<script>alert("Horario Eliminado Correctamente"); window.location.href = "admin.php";</script>';
    exit();
  } else {
    $_SESSION['error_message'] = "Error al actualizar el horario de cita: " . mysqli_error($conexion);
    header("Location: admin-edit.php?id=$id_horario");
    exit();
  }

  mysqli_close($conexion);
}

header("Location: admin.php");
exit();
?>
