<?php
$servername = "localhost";
$username = "root"; // Cambia esto si tu usuario de MySQL es diferente
$password = ""; // Cambia esto si tu contraseña de MySQL es diferente
$dbname = "teatro";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el estado de los asientos
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM asientos";
    $result = $conn->query($sql);

    $asientos = [];
    while ($row = $result->fetch_assoc()) {
        $asientos[] = $row;
    }
    echo json_encode($asientos);
}

// Actualizar el estado de los asientos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    parse_str(file_get_contents("php://input"), $post_vars);
    $updates = [];
    
    foreach ($post_vars as $key => $value) {
        // Validate input
        if (strpos($key, 'id_') === 0 && isset($post_vars["estado_" . substr($key, 3)])) {
            $id = substr($key, 3);
            $estado = $post_vars["estado_" . $id];
            if (in_array($estado, ['libre', 'ocupado'])) {
                $updates[] = "UPDATE asientos SET estado='$estado' WHERE id=$id";
            }
        }
    }

    foreach ($updates as $update) {
        if ($conn->query($update) !== TRUE) {
            echo "Error al actualizar el asiento: " . $conn->error;
            exit;
        }
    }
    
    echo "Asientos actualizados";
}

$conn->close();
?>
