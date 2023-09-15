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
    echo '<script>alert("Horario Actualizado Correctamente"); window.location.href = "admin.php";</script>';
    exit();
  } else {
    $_SESSION['error_message'] = "Error al agregar el horario de cita: " . mysqli_error($conexion);
    header("Location: admin-edit.php?id=$id_horario");
    exit();
  }

  mysqli_close($conexion);

  header("Location: admin-action.php");
  exit();
}

// Obtener los horarios de cita desde la base de datos
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "tbk"; // Reemplaza con el nombre de tu base de datos

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
  die("Error al conectar a la base de datos: " . mysqli_connect_error());
}

// Consultar los horarios de cita desde la base de datos
$query = "SELECT Horarios.*, Servicios.nombre AS nombre_servicio FROM Horarios INNER JOIN Servicios ON Horarios.id_servicio = Servicios.id_servicio";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
  $horarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
} else {
  die("Error al consultar los horarios de cita: " . mysqli_error($conexion));
}

$query = "SELECT * FROM Servicios";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
  $servicios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
} else {
  die("Error al consultar los servicios: " . mysqli_error($conexion));
}

mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Página de administrador</title>
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
    <a class="navbar-brand navbar-brand-full" href="#">Página de administrador</a>
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



<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <div class="left-container">
        <?php if (isset($_SESSION['success_message'])) : ?>
          <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success_message']; ?>
          </div>
          <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; ?>
          </div>
          <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h3>Agregar nuevo horario de cita</h3>
        <form action="admin-action.php" method="POST" class="add-form">
          <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
          </div>
          <div class="mb-3">
            <label for="hora" class="form-label">Hora</label>
            <input type="time" class="form-control" id="hora" name="hora" required>
          </div>
          <div class="mb-3">
            <label for="servicio" class="form-label">Servicio</label>
            <select class="form-control" id="servicio" name="servicio" required>
              <?php foreach ($servicios as $servicio) : ?>
                <option value="<?php echo $servicio['id_servicio']; ?>"><?php echo $servicio['nombre']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="disponibilidad" class="form-label">Disponibilidad</label>
            <select class="form-control" id="disponibilidad" name="disponibilidad" required>
              <option value="1">Disponible</option>
              <option value="0">No disponible</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="right-container">
        <br>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Servicio</th>
                <th>Disponibilidad</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($horarios)) : ?>
                <?php foreach ($horarios as $row) : ?>
                  <tr>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['hora']; ?></td>
                    <td><?php echo $row['nombre_servicio']; ?></td>
                    <td><?php echo $row['disponibilidad'] ? 'Disponible' : 'No disponible'; ?></td>
                    <td>
                      <a href='admin-edit.php?id=<?php echo $row['id_horario']; ?>' class='btn btn-sm btn-primary btn-action'><i class='bi bi-pencil'></i></a>
                      <a href='delete.php?id=<?php echo $row['id_horario']; ?>' class='btn btn-sm btn-danger btn-action'><i class='bi bi-trash'></i></a>

                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else : ?>
                <tr>
                  <td colspan='5'>No hay horarios de cita disponibles.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>
</body>

</html>
