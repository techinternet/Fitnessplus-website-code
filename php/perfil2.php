<?php
// Conexión a la base de datos
$servername = "sql303.infinityfree.com";
$username = "if0_37661971";
$password = "G9TDSpyWVWwj";
$dbname = "if0_37661971_FORMULARIO";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Comprobar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $nacimiento = $_POST['nacimiento'];
    $tipo_documento = $_POST['tipo_documento'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $ciudad = $_POST['ciudad'];
    $direccion = $_POST['direccion'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $rol = $_POST['rol'];

    // Encriptar la contraseña si se proporciona una nueva
    if (!empty($contrasena)) {
        $contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
    }

    // Subir la foto
    $foto = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // Asegurarse de que el archivo sea una imagen
        $target_dir = "uploads/";  // Directorio donde se guardarán las fotos
        $target_file = $target_dir . basename($_FILES["foto"]["name"]);
        // Mover el archivo subido al directorio de destino
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto = $target_file;  // Guardar la ruta del archivo
        }
    }

    // Consulta SQL para actualizar los datos del usuario
    $sql = "UPDATE personas SET 
            nombre='$nombre', 
            apellido='$apellido', 
            nacimiento='$nacimiento', 
            tipo_documento='$tipo_documento', 
            documento='$documento', 
            telefono='$telefono', 
            ciudad='$ciudad', 
            direccion='$direccion', 
            correo='$correo', 
            contrasena='$contrasena', 
            rol='$rol', 
            foto='$foto' 
            WHERE correo='$correo'";  // Usamos el correo como identificador

    if ($conn->query($sql) === TRUE) {
        echo "Datos actualizados correctamente";
    } else {
        echo "Error al actualizar los datos: " . $conn->error;
    }

    // Cerrar la conexión
    $conn->close();
}
?>
