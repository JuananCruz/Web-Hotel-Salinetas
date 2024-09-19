<?php
session_start();
include 'database.php';

// Obtiene el ID de usuario
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['numero_habitacion'])) {
        $habitacionaeliminar = $_POST['numero_habitacion'];

        // Eliminar la habitación
        $result = $database->eliminarHabitacion($habitacionaeliminar);

        if ($result) {
            $database->registrarEvento('Habitacion eliminada', $userId);
            // Redireccionar si la eliminación fue exitosa
            echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
        }
    }
}

// Redireccionar de vuelta a la página anterior
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
