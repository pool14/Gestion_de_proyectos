<?php
// Conexión a la base de datos
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "tbk";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Obtener los datos del formulario de registro
if (isset($_POST['nombre']) && isset($_POST['correo']) && isset($_POST['contrasena'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = 'cliente'; // Establecer el rol como "cliente" automáticamente

    // Insertar los datos en la tabla Usuarios
    $sql = "INSERT INTO Usuarios (nombre, correo, contrasena, rol) VALUES ('$nombre', '$correo', '$contrasena', '$rol')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Usuario registrado exitosamente como cliente.'); window.location.href = 'login.php';</script>";
        exit;
    } else {
        echo "Error al registrar el usuario: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registro de usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
  rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ"
  crossorigin="anonymous">
  <link rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="css/styles.css">
<body>
  <div class="container mt-5">
    
      <div class="card-body">
        <h2 class="card-title">Registro de usuario</h2>
        <form action="register.php" method="POST">
          <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>
          <div class="form-group">
            <label for="correo">Correo electrónico:</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
          </div>
          <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" class="form-control" id="contrasena" name="contrasena" required>
          </div>
          <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
crossorigin="anonymous"></script>  
  
</body>
</html>
