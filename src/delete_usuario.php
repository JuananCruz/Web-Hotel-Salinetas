<?php
session_start();
include 'database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dni'])) {
        $usuarioaeliminar = $_POST['dni'];

        // Eliminar la habitación
        $result = $database->eliminarUsuario($usuarioaeliminar);

        if ($result) {
            $database->registrarEvento('Usuario eliminado', $dni);
            // Redireccionar si la eliminación fue exitosa
            echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
        }
    }
}

// Redireccionar de vuelta a la página anterior
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
