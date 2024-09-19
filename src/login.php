<?php
session_start();
include 'database.php';

// Inicializar variables
$erroresLogin = [];
$email = '';


// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $database->escape_value(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Validar el email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroresLogin['email'] = 'El email no es válido.';
    }

    // Validar la contraseña
    if (empty($password)) {
        $erroresLogin['password'] = 'La contraseña no puede estar vacía.';
    }

    if (empty($erroresLogin)) {
        // Obtener el usuario de la base de datos
        $result = $database->getUsuarioLogin($email);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $userId = $row['dni'];
            $hashedPassword = $row['clave'];
            $rol = $row['rol'];

            // Verificar la contraseña
            if (password_verify($password, $hashedPassword)) {
                // Contraseña correcta, iniciar sesión
                $_SESSION['user_id'] = $userId;
                $_SESSION['rol'] = $rol;
                $database->registrarEvento('Nuevo inicio de sesion', $userId);
            } else {
                $database->registrarEvento('Inicio de sesion erroneo', $userId);
                $erroresLogin['password'] = 'Contraseña incorrecta.';
            }
        } else {
            $erroresLogin['email'] = 'No se encontró un usuario con ese email.';
        }
    }
}

// Guardar errores en la sesión
$_SESSION['erroresLogin'] = $erroresLogin;
$_SESSION['loginData'] = ['email' => $email];

// Redireccionar al index principal
echo '<meta http-equiv="refresh" content="0;url='.$_SERVER['HTTP_REFERER'].'" />';
