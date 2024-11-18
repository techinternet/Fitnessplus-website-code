<?php
// Configura las cabeceras para tratar la respuesta como JSON
header('Content-Type: application/json');

// Habilita el informe de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Datos de conexión a la base de datos
$servername = "sql303.infinityfree.com";
$username = "if0_37661971";
$password = "G9TDSpyWVWwj";
$dbname = "if0_37661971_FORMULARIO";

// Crear una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
$conn->set_charset("utf8mb4");

// Inicializa la respuesta
$response = array('success' => false);

// Preparar la consulta para obtener todos los usuarios con rol 'Usuario'
$stmt = $conn->prepare("SELECT NOMBRE, TELEFONO FROM personas WHERE ROL = 'Usuario'");
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Si hay resultados
if ($result->num_rows > 0) {
    $usuarios = array();
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;  // Almacena los resultados en el array $usuarios
    }

    $response['success'] = true;
    $response['usuarios'] = $usuarios;  // Devuelve la lista de usuarios
} else {
    $response['message'] = 'No se encontraron usuarios con el rol de Usuario.';
}

$stmt->close();
$conn->close();

// Envía la respuesta en formato JSON
echo json_encode($response);
?>
