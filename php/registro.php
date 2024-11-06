<?php
// Configura las cabeceras para tratar la respuesta como JSON
header('Content-Type: application/json');

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
// Lee la entrada JSON
$inputJSON = file_get_contents('php://input');
//echo $inputJSON;
$input = json_decode($inputJSON, TRUE); // Decodifica el JSON en un array asociativo
//echo $input;

// Inicializa la respuesta
$response = array('success' => false);

// Verifica que los datos necesarios estén presentes
if (isset($input['nombre']) && isset($input['apellido']) && isset($input['fecha_nacimiento'])&& isset($input['numero_documento']) && isset($input['telefono'])&& isset($input['direccion']) && isset($input['correo'])&& isset($input['contrasena']) ) {

    // Aquí puedes insertar la lógica para guardar al usuario en una base de datos.
    // Este es solo un ejemplo y no realiza realmente el almacenamiento.
// Preparar y ejecutar una declaración SQL
$stmt = $conn->prepare("INSERT INTO personas (NOMBRE, APELLIDO, NACIMIENTO,TIPO DOCUMENTO,DOCUMENTO,TELEFONO,CIUDAD,DIRECCION,CORREO,CONTRASENA,ROL) VALUES (?, ?, ?,?,?,?,?,?,?,?,?)");
$stmt->bind_param("sssssssssss", $input['nombre'], $input['apellido'], $input['fecha_nacimiento'],"232",$input['numero_documento'],$input['telefono'],"232",$input['direccion'],$input['correo'],$input['contrasena'],"232");

if ($stmt->execute()) {
    echo "Nuevo registro creado con éxito.";
} else {
    echo "Error: " . $stmt->error;
}
    // Simula que el registro fue exitoso
    $response['success'] = true;
    
    // Agrega un mensaje opcional
    $response['message'] = 'Usuario registrado con éxito';
} else {
    // Si faltan datos, responde con un mensaje de error
    $response['message'] = 'Faltan datos requeridos: username, email y password.';
}

// Envía la respuesta en formato JSON
echo json_encode($response);
?>