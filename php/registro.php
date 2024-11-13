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
if (isset($input['nombre'], $input['apellido'], $input['fecha_nacimiento'], $input['numero_documento'], $input['telefono'], $input['direccion'], $input['correo'], $input['contrasena'], $input['tipodocumento'], $input['ciudad'], $input['rol'])) {

    // Comprobar si el usuario ya está registrado
    $check_stmt = $conn->prepare("SELECT 1 FROM personas WHERE TIPODOCUMENTO = ? AND DOCUMENTO = ? AND CORREO = ?");
    if ($check_stmt === false) {
        die("Error en la preparación de la consulta (comprobación): " . $conn->error);
    }
    $check_stmt->bind_param("sss", $input['tipodocumento'], $input['numero_documento'], $input['correo']);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $response['message'] = 'Este usuario ya está registrado.';
    } else {
        // Hashear la contraseña
        $hashed_password = password_hash($input['contrasena'], PASSWORD_DEFAULT);

        // Preparar la inserción
        $stmt = $conn->prepare("INSERT INTO personas (NOMBRE, APELLIDO, NACIMIENTO, TIPODOCUMENTO, DOCUMENTO, TELEFONO, CIUDAD, DIRECCION, CORREO, CONTRASENA, ROL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta (inserción): " . $conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param(
            "sssssssssss",
            $input['nombre'],
            $input['apellido'],
            $input['fecha_nacimiento'],
            $input['tipodocumento'],
            $input['numero_documento'],
            $input['telefono'],
            $input['ciudad'],
            $input['direccion'],
            $input['correo'],
            $hashed_password, // Usar la contraseña hasheada
            $input['rol']
        );

        // Ejecutar la inserción
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Usuario registrado con éxito';
        } else {
            $response['message'] = 'Error al registrar el usuario: ' . $stmt->error;
        }

        $stmt->close();
    }
    $check_stmt->close();

} else {
    $response['message'] = 'Faltan datos requeridos.';
}

$conn->close();

// Envía la respuesta en formato JSON
echo json_encode($response);
?>