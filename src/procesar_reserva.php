<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $erroresReserva = [];
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $tipoHabitacion = isset($_POST['tipoHabitacion']) ? $_POST['tipoHabitacion'] : null;


    // Recibir datos del formulario
    $numeroPersonas = $_POST['numeroPersonas'];
    $diaEntrada = $_POST['diaEntrada'];
    $diaSalida = $_POST['diaSalida'];
    $comentarios = $_POST['comentarios'];

    // Validaciones
    if ($numeroPersonas <= 0) {
        $erroresReserva['numeroPersonas'] = 'El número de personas debe ser mínimo 1.';
    }

    if (empty($diaEntrada)) {
        $erroresReserva['diaEntrada'] = 'Selecciona un día de entrada.';
    }

    if (empty($diaSalida)) {
        $erroresReserva['diaSalida'] = 'Selecciona un día de salida.';
    }

    if (strtotime($diaEntrada) < strtotime(date('Y-m-d'))) {
        $erroresReserva['diaEntrada'] = 'El día de entrada no puede ser menor que el día actual.';
    }

    if ($diaSalida < $diaEntrada) {
        $erroresReserva['diaSalida'] = 'El día de salida no puede ser menor que el día de entrada.';
    }

    // Verificar si tipoHabitacion está definido antes de obtener la capacidad
    if (!empty($tipoHabitacion)) {
        $capacidadHabitacion = $database->getCapacidadHabitacion($tipoHabitacion);

        if ($numeroPersonas > $capacidadHabitacion) {
            $erroresReserva['numeroPersonas'] = 'No existe una habitación de este tipo con esta capacidad.';
        }
    }

    if (!empty($erroresReserva)) {
        $_SESSION['erroresReserva'] = $erroresReserva;
        $_SESSION['reservaData'] = $_POST;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        $numeroHabitacion = $database->getNumeroHabitacionDisponible($numeroPersonas, $tipoHabitacion);


        if (!$numeroHabitacion) {
            $erroresReserva['general'] = 'No hay habitaciones disponibles en este momento.';
            $_SESSION['erroresReserva'] = $erroresReserva;
            $_SESSION['reservaData'] = $_POST; // Guardar datos del formulario
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        } else {
            // Si no hay errores, procede con la inserción de la reserva
            $estado = 'pendiente';
            $marcaTiempo = date('Y-m-d H:i:s');

            if ($database->insertaNuevaReserva($userId, $numeroHabitacion, $numeroPersonas, $comentarios, $diaEntrada, $diaSalida, $estado, $marcaTiempo)) {
                $database->setHabitacionReservada($numeroHabitacion);
                $database->registrarEvento('Reserva añadida', $userId);
                // Limpiar errores y datos de formulario si la reserva es exitosa
                unset($_SESSION['erroresReserva']);
                unset($_SESSION['reservaData']);
                header('Location: ' . $_SERVER['HTTP_REFERER']); 
                exit;
            } else {
                $database->registrarEvento('Fallo al realizar una reserva', $userId);
                $erroresReserva['general'] = 'Error al realizar la reserva.';
                $_SESSION['erroresReserva'] = $erroresReserva;
                $_SESSION['reservaData'] = $_POST; 
                header('Location: ' . $_SERVER['HTTP_REFERER']); 
                exit;
            }
        }
    }
}
