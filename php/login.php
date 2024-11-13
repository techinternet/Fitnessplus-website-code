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

// Lee la entrada JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

// Inicializa la respuesta
$response = array('success' => false);

// Verifica que los datos necesarios estén presentes
if (isset($input['correo'], $input['contrasena'])) {

    // Preparar la consulta para obtener el hash de la contraseña del usuario
    $stmt = $conn->prepare("SELECT CONTRASENA FROM personas WHERE CORREO = ?");
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $input['correo']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // El usuario existe, obtener el hash de la contraseña
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($input['contrasena'], $hashed_password)) {
            // Contraseña correcta
            $response['success'] = true;
            $response['message'] = 'Inicio de sesión exitoso';
        } else {
            // Contraseña incorrecta
            $response['message'] = 'Contraseña incorrecta';
        }
    } else {
        // El usuario no existe
        $response['message'] = 'Correo no encontrado';
    }

    $stmt->close();

} else {
    // Faltan datos requeridos
    $response['message'] = 'Faltan datos requeridos.';
}

$conn->close();

// Envía la respuesta en formato JSON
echo json_encode($response);
?>