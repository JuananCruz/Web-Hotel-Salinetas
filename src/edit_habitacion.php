<?php
session_start();
include 'database.php';

$erroresEditHabitacion = [];

// Obtiene el ID de usuario
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_habitacion = $database->escape_value(trim($_POST['numero_habitacion']));
    $nuevo_numero_habitacion = $database->escape_value(trim($_POST['nuevo_numero_habitacion']));
    $precio_por_noche = floatval($_POST['precio_por_noche']);
    $tipo = $database->escape_value($_POST['tipo']);

    // Validaciones
    if (empty($numero_habitacion)) {
        $erroresEditHabitacion['numero_habitacion'] = 'El número de habitación no puede estar vacío.';
    }

    if (empty($nuevo_numero_habitacion)) {
        $erroresEditHabitacion['nuevo_numero_habitacion'] = 'El nuevo número de habitación no puede estar vacío.';
    }

    if ($precio_por_noche <= 0) {
        $erroresEditHabitacion['precio_por_noche'] = 'El precio por noche debe ser mayor que cero.';
    }

    if (empty($tipo)) {
        $erroresEditHabitacion['tipo'] = 'El tipo de habitación no puede estar vacío.';
    }

    // Puedes agregar más validaciones según tus requisitos

    if (empty($erroresEditHabitacion)) {
        // Actualizar la habitación en la base de datos
        $result = $database->modificaHabitacion($numero_habitacion, $nuevo_numero_habitacion, $precio_por_noche, $tipo);

        if ($result) {
            $database->registrarEvento('Habitacion editada', $userId);
            // Redireccionar
            echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
        } else {
            $database->registrarEvento('Fallo al editar habitacion', $userId);
            $erroresEditHabitacion['general'] = 'Error al editar la habitación.';
        }
    }
}

// Guardar errores en la sesión
$_SESSION['erroresEditHabitacion'] = $erroresEditHabitacion;

// Redireccionar
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';