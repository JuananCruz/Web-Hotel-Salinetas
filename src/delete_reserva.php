<?php
session_start();
include 'database.php';

// Obtiene el ID de usuario
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $reservaaeliminar = $_POST['id'];

        // Obtener el número de habitación de la reserva a eliminar
        $numeroHabitacion = $database->getHabitacionReservada($reservaaeliminar);

        if ($numeroHabitacion) {
            // Eliminar la reserva
            $result = $database->eliminarReserva($reservaaeliminar);

            if ($result) {
                // Poner la habitación a disponible
                $database->setHabitacionDisponible($numeroHabitacion);
                // Registrar el evento de eliminación de reserva
                $database->registrarEvento('Reserva eliminada', $userId);


                // Redireccionar si la eliminación fue exitosa
                echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
            }
        }
    }
}

// Redireccionar de vuelta a la página anterior
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
