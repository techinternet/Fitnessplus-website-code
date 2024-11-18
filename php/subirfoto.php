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

// Recuperar la ruta de la imagen de perfil desde la base de datos
$correo = "usuario@ejemplo.com"; // Este valor debe ser el correo del usuario autenticado
$sql = "SELECT foto FROM personas WHERE correo='$correo'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Obtener la ruta de la imagen
    $row = $result->fetch_assoc();
    $foto = $row['foto'];
} else {
    // Si no se encuentra una imagen, usar la imagen predeterminada
    $foto = 'img/perfil.svg';
}

$conn->close();
?>
