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

// Obtener los datos del formulario
$idUsuario = $_SESSION['user_id'];
$idHorario = $_POST['hora-servicio'];

// Verificar si el usuario existe en la tabla Usuarios
$sqlVerificarUsuario = "SELECT id_usuario FROM Usuarios WHERE id_usuario = $idUsuario";
$resultadoVerificarUsuario = mysqli_query($conexion, $sqlVerificarUsuario);

if (mysqli_num_rows($resultadoVerificarUsuario) > 0) {
  // Consultar el estado de disponibilidad del horario
  $sqlDisponibilidad = "SELECT disponibilidad FROM Horarios WHERE id_horario = $idHorario";
  $resultadoDisponibilidad = mysqli_query($conexion, $sqlDisponibilidad);

  if ($resultadoDisponibilidad) {
    $fila = mysqli_fetch_assoc($resultadoDisponibilidad);
    $disponibilidad = $fila['disponibilidad'];

    // Verificar si el horario está disponible
    if ($disponibilidad) {
      // Actualizar el estado de disponibilidad del horario
      $sqlActualizarDisponibilidad = "UPDATE Horarios SET disponibilidad = FALSE WHERE id_horario = $idHorario";
      $resultadoActualizar = mysqli_query($conexion, $sqlActualizarDisponibilidad);

      if ($resultadoActualizar) {
        // Insertar la cita en la tabla Citas
        $sqlInsertarCita = "INSERT INTO Citas (id_usuario, id_horario) VALUES ($idUsuario, $idHorario)";
        $resultadoInsertar = mysqli_query($conexion, $sqlInsertarCita);

        if ($resultadoInsertar) {
          // Cita agendada con éxito
          $mensaje = "¡Cita agendada con éxito!";
          echo "<script>alert('" . $mensaje . "');</script>";
        } else {
          // Error al insertar la cita
          $mensaje = "Error al agendar la cita. Por favor, intenta nuevamente.";
          echo "<script>alert('" . $mensaje . "');</script>";
        }
      } else {
        // Error al actualizar la disponibilidad del horario
        $mensaje = "Error al agendar la cita. Por favor, intenta nuevamente.";
        echo "<script>alert('" . $mensaje . "');</script>";
      }
    } else {
      // Horario no disponible
      $mensaje = "El horario seleccionado ya no está disponible. Por favor, elige otro horario.";
      echo "<script>alert('" . $mensaje . "');</script>";
    }
  } else {
    // Error al consultar la disponibilidad del horario
    $mensaje = "Error al agendar la cita. Por favor, intenta nuevamente.";
    echo "<script>alert('" . $mensaje . "');</script>";
  }
} else {
  // El usuario no existe en la tabla Usuarios
  $mensaje = "El usuario no existe. Por favor, inicia sesión nuevamente.";
  echo "<script>alert('" . $mensaje . "');</script>";
}

// Redireccionar al cliente.php
echo "<script>window.location.href = 'cliente.php';</script>";

// Cerrar la conexión a la base de datos
mysqli_close($conexion);
?>
