<?php
session_start();
include 'database.php';

// Guardar el DNI del usuario antes de destruir la sesión
$usuario_dni = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';

// Destruir la sesión
session_unset();
session_destroy();




$database->registrarEvento('Cerrar sesion', $usuario_dni);

// Redirigir al usuario a la página de inicio
echo '<script>window.location.href = "../index.php";</script>';
