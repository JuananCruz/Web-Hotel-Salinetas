<?php
session_start();
include 'database.php';

$erroresAddHabitacion = [];

// Obtiene el ID de usuario
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_habitacion = $database->escape_value(trim($_POST['numero_habitacion']));
    $precio_por_noche = floatval($_POST['precio_por_noche']);
    $tipo = $database->escape_value($_POST['tipo']);

    // Validaciones
    if (empty($numero_habitacion)) {
        $erroresAddHabitacion['numero_habitacion'] = 'El número de habitación no puede estar vacío.';
    }

    if (empty($precio_por_noche)) {
        $erroresAddHabitacion['precio_por_noche'] = 'El precio por noche no puede estar vacio';
    }

    // Validar si el número de habitación ya existe en la base de datos
    if ($database->habitacionExiste($numero_habitacion)) {
        $erroresAddHabitacion['numero_habitacion'] = 'El número de habitación ya existe.';
    }

    if (empty($erroresAddHabitacion)) {
        // Insertar la habitación en la base de datos
        $result = $database->insertaHabitacion($numero_habitacion, $precio_por_noche, $tipo);

        if ($result) {
            $database->registrarEvento('Nueva habitacion', $userId);
            // Redireccionar
            echo '<meta http-equiv="refresh" content="0;url='.$_SERVER['HTTP_REFERER'].'" />';
        } else {
            $database->registrarEvento('Fallo al añadir nueva habitacion', $userId);
            $erroresAddHabitacion['general'] = 'Error al añadir la habitación.';
        }
    }


}

// Guardar errores en la sesión
$_SESSION['erroresAddHabitacion'] = $erroresAddHabitacion;
$_SESSION['datosFormulario'] = $_POST;

// Redireccionar
echo '<meta http-equiv="refresh" content="0;url='.$_SERVER['HTTP_REFERER'].'" />';