<?php
// Conectar a la base de datos
$servername = "localhost:3306";
$username = "root";
$password = "";
$dbname = "tbk"; // Reemplaza con el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Obtener la fecha seleccionada
$fecha = $_POST['fecha'];

// Consultar los horarios con sus nombres de servicio para la fecha seleccionada
$sql = "SELECT h.id_horario, h.hora, s.nombre FROM Horarios h
        INNER JOIN Servicios s ON h.id_servicio = s.id_servicio
        WHERE h.fecha = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $fecha); // "s" indica que la variable es de tipo cadena (string)
$stmt->execute();

// Obtener los resultados
$result = $stmt->get_result();

$horarios = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $horario = array(
            "id_horario" => $row['id_horario'],
            "hora" => $row['hora'],
            "nombre_servicio" => $row['nombre']
        );
        $horarios[] = $horario;
    }
}

// Devolver los horarios como respuesta JSON
header('Content-Type: application/json');
echo json_encode($horarios);

$stmt->close();
$conn->close();
?>
