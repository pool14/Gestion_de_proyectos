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

// Obtener las citas agendadas del usuario
$user_id = $_SESSION['user_id'];
$sqlCitas = "SELECT Citas.id_cita, Horarios.fecha, Horarios.hora FROM Citas JOIN Horarios ON Citas.id_horario = Horarios.id_horario WHERE Citas.id_usuario = $user_id";
$resultadoCitas = mysqli_query($conexion, $sqlCitas);

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

  <div class="container mt-5">
    <h2>Citas agendadas</h2>
    <?php if (mysqli_num_rows($resultadoCitas) > 0) : ?>
      <?php while ($rowCita = mysqli_fetch_assoc($resultadoCitas)) : ?>
        <div class="cita">
          <p>Cita agendada el <?php echo $rowCita['fecha']; ?> a las <?php echo $rowCita['hora']; ?></p>
          <form action="cancelar-cita.php" method="POST">
            <input type="hidden" name="id_cita" value="<?php echo $rowCita['id_cita']; ?>">
            <button type="submit" class="btn btn-danger">Cancelar</button>
          </form>
        </div>
      <?php endwhile; ?>
    <?php else : ?>
      <div class="container mt-5">
        <h4>No tienes citas agendadas</h4>
        <a href="cliente.php" class="btn btn-primary">Agendar cita aquí</a>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>
