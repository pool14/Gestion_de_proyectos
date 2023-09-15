<?php
session_start();

// Verificar si el usuario ha iniciado sesión como administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrador') {
  header("Location: login.php");
  exit();
}

// Obtener el ID del horario de cita a editar
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

// Consultar el horario de cita a editar desde la base de datos
$query = "SELECT * FROM Horarios WHERE id_horario = $id_horario";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
  $horario = mysqli_fetch_assoc($resultado);
} else {
  die("Error al consultar el horario de cita: " . mysqli_error($conexion));
}

// Consultar los servicios desde la base de datos
$query = "SELECT * FROM Servicios";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
  $servicios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
} else {
  die("Error al consultar los servicios: " . mysqli_error($conexion));
}

mysqli_close($conexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los datos del formulario
  $fecha = $_POST['fecha'];
  $hora = $_POST['hora'];
  $id_servicio = $_POST['servicio'];
  $disponibilidad = $_POST['disponibilidad'];

  // Conectar a la base de datos
  $conexion = mysqli_connect($servername, $username, $password, $dbname);

  if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
  }

  // Actualizar el horario de cita en la base de datos
  $query = "UPDATE Horarios SET fecha = '$fecha', hora = '$hora', id_servicio = $id_servicio, disponibilidad = $disponibilidad WHERE id_horario = $id_horario";
  $resultado = mysqli_query($conexion, $query);

  if ($resultado) {
    echo '<script>alert("Horario Actualizado Correctamente"); window.location.href = "admin.php";</script>';
    exit();
  } else {
    $_SESSION['error_message'] = "Error al actualizar el horario de cita: " . mysqli_error($conexion);
    header("Location: admin-edit.php?id=$id_horario");
    exit();
  }

  mysqli_close($conexion);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar horario de cita</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
    rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
    crossorigin="anonymous">
  <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/styles.css">
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h3>Editar horario de cita</h3>
        <?php if (isset($_SESSION['error_message'])) : ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; ?>
          </div>
          <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form action="admin-edit.php?id=<?php echo $id_horario; ?>" method="POST">
          <div class="mb-3">
            <label for="fecha" class="form-label">Fecha:</label>
            <input type="date" id="fecha" name="fecha" class="form-control" required value="<?php echo $horario['fecha']; ?>">
          </div>
          <div class="mb-3">
            <label for="hora" class="form-label">Hora:</label>
            <input type="time" id="hora" name="hora" class="form-control" required value="<?php echo $horario['hora']; ?>">
          </div>
          <div class="mb-3">
            <label for="servicio" class="form-label">Servicio:</label>
            <select id="servicio" name="servicio" class="form-control" required>
              <?php foreach ($servicios as $servicio) : ?>
                <option value="<?php echo $servicio['id_servicio']; ?>" <?php if ($servicio['id_servicio'] === $horario['id_servicio']) echo 'selected'; ?>>
                  <?php echo $servicio['nombre']; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="disponibilidad" class="form-label">Disponibilidad:</label>
            <select id="disponibilidad" name="disponibilidad" class="form-control" required>
              <option value="1" <?php if ($horario['disponibilidad'] == 1) echo 'selected'; ?>>Disponible</option>
              <option value="0" <?php if ($horario['disponibilidad'] == 0) echo 'selected'; ?>>No disponible</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
          <a href="admin.php" class="btn btn-secondary">Cancelar</a>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
</body>

</html>
