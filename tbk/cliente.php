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

// Obtener los servicios disponibles
$sqlServicios = "SELECT id_servicio, nombre FROM Servicios";
$resultadoServicios = mysqli_query($conexion, $sqlServicios);

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página de cliente</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
    crossorigin="anonymous">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand navbar-brand-full" href="#">Página de cliente</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Cerrar sesión</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container-fluid ">
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <div class="left-container">
        <h3 class="text-left">Agendar cita</h3>
        <form action="agendar-cita.php" method="POST" class="add-form">
          <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
          </div>
          <div class="mb-3">
            <label for="hora-servicio" class="form-label">Hora & Servicio</label>
            <select class="form-control" id="hora-servicio" name="hora-servicio" required>
              <option value="" selected disabled>Seleccione una fecha primero</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Agendar</button>
        </form>
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-md-6 offset-md-3 text-center">
      <a href="citas-agendadas.php" class="btn btn-primary">Tus citas aquí</a>
    </div>
  </div>
</div>



  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>


  <script>
    jQuery(document).ready(function () {
      $("#fecha").on("change", function () {
        console.log("Cambio de fecha detectado"); // Verificar si se ejecuta este mensaje en la consola

        var selectedDate = $(this).val();
        $.ajax({
          url: "get-horarios.php",
          method: "POST",
          data: {
            fecha: selectedDate
          },
          dataType: "json"
        })
          .done(function (response) {
            var options = "";
            if (Array.isArray(response) && response.length > 0) {
              response.forEach(function (horario) {
                options += `<option value="${horario.id_horario}">${horario.hora} - ${horario.nombre_servicio}</option>`;
              });
            } else {
              options = "<option value='' selected disabled>No hay horarios disponibles</option>";
            }
            $("#hora-servicio").html(options);
          })
          .fail(function (xhr, status, error) {
            console.error(xhr.responseText);
          });
      });
    });
  </script>
  <script>
function mostrarAlerta(mensaje) {
  alert(mensaje);
}
</script>
    
    
</body>

</html>
