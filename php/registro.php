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

// Verifica que se envíe como formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])){
    $targetDir = "img/"; // Directorio donde deseas guardar el archivo
    $fileName = basename($_FILES['foto']['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Mover el archivo subido a la ubicación deseada
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFilePath)) {
        $fotoPath = $targetFilePath; // Ruta a almacenar en la base de datos
    } else {
        $response = ["mensaje" => "Error al subir la imagen: " . $_FILES['foto']['name']];
        echo json_encode($response);
        exit();
    }

    // Verifica que los datos necesarios estén presentes
    $requiredFields = ['nombre', 'apellido', 'fecha_nacimiento', 'numero_documento', 'telefono', 'direccion', 'correo', 'contrasena', 'tipodocumento', 'ciudad', 'rol'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field])) {
            $response['message'] = "Falta el campo: $field.";
            echo json_encode($response);
            exit;
        }
    }

    // Comprobar si el usuario ya está registrado
    $check_stmt = $conn->prepare("SELECT 1 FROM personas WHERE TIPODOCUMENTO = ? AND DOCUMENTO = ? AND CORREO = ?");
    if ($check_stmt === false) {
        die("Error en la preparación de la consulta (comprobación): " . $conn->error);
    }
    $check_stmt->bind_param("sss", $_POST['tipodocumento'], $_POST['numero_documento'], $_POST['correo']);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $response['message'] = 'Este usuario ya está registrado.';
    } else {
        // Hashear la contraseña
        $hashed_password = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

        // Preparar la inserción
        $stmt = $conn->prepare("INSERT INTO personas (NOMBRE, APELLIDO, NACIMIENTO, TIPODOCUMENTO, DOCUMENTO, TELEFONO, CIUDAD, DIRECCION, CORREO, CONTRASENA, ROL, FOTO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die("Error en la preparación de la consulta (inserción): " . $conn->error);
        }

        // Vincular parámetros
        $stmt->bind_param(
            "ssssssssssss",
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['fecha_nacimiento'],
            $_POST['tipodocumento'],
            $_POST['numero_documento'],
            $_POST['telefono'],
            $_POST['ciudad'],
            $_POST['direccion'],
            $_POST['correo'],
            $hashed_password,
            $_POST['rol'],
            $fotoPath
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
    $response['message'] = 'Método no permitido o datos incompletos.';
}

$conn->close();

// Envía la respuesta en formato JSON
echo json_encode($response);
?>