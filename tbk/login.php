<?php
session_start();

// Verificar si el usuario ya ha iniciado sesión
if (isset($_SESSION['user_id'])) {
  // Redirigir al usuario a la página correspondiente según su rol
  if ($_SESSION['user_role'] === 'administrador') {
    header("Location: admin.php");
    exit();
  } else {
    header("Location: cliente.php");
    exit();
  }
}

// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Obtener los valores del formulario
  $correo = $_POST['correo'];
  $contrasena = $_POST['contrasena'];

  // Conectar a la base de datos
  $servername = "localhost:3306";
  $username = "root";
  $password = "";
  $dbname = "tbk";

  $conn = new mysqli($servername, $username, $password, $dbname);

  // Verificar la conexión
  if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
  }

  // Consultar el usuario en la base de datos
  $sql = "SELECT id_usuario, nombre, rol FROM Usuarios WHERE correo = '$correo' AND contrasena = '$contrasena'";
  $result = $conn->query($sql);

  if ($result->num_rows === 1) {
    // El usuario ha sido encontrado en la base de datos
    $row = $result->fetch_assoc();

    // Guardar la información del usuario en la sesión
    $_SESSION['user_id'] = $row['id_usuario'];
    $_SESSION['user_name'] = $row['nombre'];
    $_SESSION['user_role'] = $row['rol'];

    // Redirigir al usuario a la página correspondiente según su rol
    if ($row['rol'] === 'administrador') {
      header("Location: admin.php");
      exit();
    } else {
      header("Location: cliente.php");
      exit();
    }
  } else {
    $error_message = "Credenciales inválidas. Por favor, intenta nuevamente.";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Iniciar sesión</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
  rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
  crossorigin="anonymous">
  <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div class="container">
    <h2>Iniciar sesión</h2>

    <?php if (isset($error_message)) : ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="correo">Correo electrónico:</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
      </div>
      <div class="form-group">
        <label for="contrasena">Contraseña:</label>
        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
      </div>
      <button type="submit" class="btn btn-primary">Iniciar sesión</button>
      <a href="register.php" class="btn btn-link">Registrarse</a>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
crossorigin="anonymous"></script>  
</body>
</html>
