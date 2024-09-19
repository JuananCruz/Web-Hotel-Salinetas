<?php
session_start();
include 'database.php';

$erroresRegistro = [];

function validar_dni($dni)
{
    $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $numero = substr($dni, 0, 8);
    $letra = substr($dni, 8, 1);
    return $letra === $letras[$numero % 23];
}

function validar_tarjeta_credito($number)
{
    $number = strrev($number);
    $sum = 0;

    for ($i = 0, $len = strlen($number); $i < $len; $i++) {
        $digit = $number[$i];
        if ($i % 2 === 1) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
    }

    return $sum % 10 === 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $database->escape_value(trim($_POST['nombre']));
    $apellidos = $database->escape_value(trim($_POST['apellidos']));
    $dni = strtoupper(trim($_POST['dni']));
    $email = $database->escape_value(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirmPassword']);
    $credit_card = trim($_POST['credit_card']);
    $rol = isset($_POST['rol']) ? $database->escape_value(trim($_POST['rol'])) : 'cliente';

    // Validaciones
    if (empty($nombre)) {
        $erroresRegistro['nombre'] = 'El nombre no puede estar vacío.';
    }

    if (empty($apellidos)) {
        $erroresRegistro['apellidos'] = 'Los apellidos no pueden estar vacíos.';
    }

    if (!preg_match('/^\d{8}[A-Z]$/', $dni) || !validar_dni($dni)) {
        $erroresRegistro['dni'] = 'El DNI no es válido.';
    } else {
        // Verificar si el DNI ya existe en la base de datos
        $result = $database->getUsuarioByDNI($dni);
        if ($result->num_rows > 0) {
            $erroresRegistro['dni'] = 'Este DNI ya está registrado.';
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erroresRegistro['email'] = 'El email no es válido.';
    } else {
        // Verificar si el email ya existe en la base de datos
        $result = $database->getUsuarioByEmail($email);
        if ($result->num_rows > 0) {
            $erroresRegistro['email'] = 'El email ya existe en la base de datos.';
        }
    }

    if (!preg_match('/\w{5,}/', $password)) {
        $erroresRegistro['password'] = 'La contraseña debe tener al menos 5 caracteres alfanuméricos.';
    }

    if ($password !== $confirm_password) {
        $erroresRegistro['confirmPassword'] = 'Las contraseñas no coinciden.';
    }

    if (!preg_match('/^\d{16}$/', $credit_card) || !validar_tarjeta_credito($credit_card)) {
        $erroresRegistro['credit_card'] = 'La tarjeta de crédito no es válida.';
    }

    if (empty($erroresRegistro)) {

        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insertar el usuario en la base de datos
        $result = $database->insertaNuevoUsuario($nombre, $apellidos, $dni, $email, $hashed_password, $credit_card, $rol);

        if ($result) {
            $_SESSION['user_id'] = $dni;
            $_SESSION['rol'] = $rol;

            $database->registrarEvento('Nuevo usuario registrado', $dni);
        } else {
            $database->registrarEvento('Fallo al registrar un usuario', $dni);
            $erroresRegistro['general'] = 'Error al registrar el usuario.';
        }
    }
}

// Guardar errores en la sesión
$_SESSION['erroresRegistro'] = $erroresRegistro;
$_SESSION['RegisterData'] = ['email' => $email, 'nombre' => $nombre, 'apellidos' => $apellidos, 'dni' => $dni, 'credit_card' => $credit_card];

// Redireccionar al index principal
echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['HTTP_REFERER'] . '" />';
