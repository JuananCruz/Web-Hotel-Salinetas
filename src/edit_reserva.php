<?php
session_start();
include 'database.php';

$erroresEditReserva = [];

// Obtiene el ID de usuario
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserva_id = $database->escape_value(trim($_POST['id']));
    $nuevos_comentarios = $database->escape_value(trim($_POST['comentarios']));

    if (empty($erroresEditReserva)) {
        // Actualizar los comentarios de la reserva en la base de datos
        $result = $database->editarComentariosReserva($reserva_id, $nuevos_comentarios);

        if ($result) {
            $database->registrarEvento('Reserva editada', $userId);
            // Redireccionar
            echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
        } else {
            $database->registrarEvento('Fallo al editar reserva', $userId);
            $erroresEditReserva['general'] = 'Error al editar los comentarios de la reserva.';
        }
    }
}

// Guardar errores en la sesión
$_SESSION['erroresEditReserva'] = $erroresEditReserva;

// Redireccionar
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
?>
