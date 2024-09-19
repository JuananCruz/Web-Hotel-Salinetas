<?php
session_start();
include 'database.php';

$userId = $_SESSION['user_id'] ?? null;
$editUserId = $_POST['dni'] ?? null;
$rol = $_SESSION['rol'] ?? null;

$_SESSION['erroresEdit'] = [];

$nombre = $_POST['nombre'] ?? '';
$apellidos = $_POST['apellidos'] ?? '';
$dni = $_POST['dni'] ?? '';
$nuevoDni = $_POST['nuevo_dni'] ?? '';
$email = $_POST['email'] ?? '';
$clave = $_POST['clave'] ?? '';
$numero_tarjeta_credito = $_POST['numero_tarjeta_credito'] ?? '';
$rolNuevo = $_POST['rol'] ?? '';

// Validar el nombre
if ($rol === 'recepcionista' || $rol === 'administrador') {
    if (empty($nombre)) {
        $_SESSION['erroresEdit']['nombre'] = 'El nombre es obligatorio.';
    }
}

// Validar el apellido
if ($rol === 'recepcionista' || $rol === 'administrador') {
    if (empty($apellidos)) {
        $_SESSION['erroresEdit']['apellidos'] = 'Los apellidos son obligatorios.';
    }
}

// Validar el DNI
if ($rol === 'recepcionista' || $rol === 'administrador' || $rol === 'cliente') {
    if (empty($dni)) {
        $_SESSION['erroresEdit']['dni'] = 'El DNI es obligatorio.';
    }
}

// Validar el email
if (empty($email)) {
    $_SESSION['erroresEdit']['email'] = 'El correo electrónico es obligatorio.';
}

if(!empty($clave)){
    if (!preg_match('/\w{5,}/', $clave)) {
        $_SESSION['erroresEdit']['clave'] = 'La contraseña debe tener al menos 5 caracteres alfanuméricos.';
    }
}

// Validar la tarjeta de crédito
if (empty($numero_tarjeta_credito)) {
    $_SESSION['erroresEdit']['numero_tarjeta_credito'] = 'El número de tarjeta de crédito es obligatorio.';
}

// Validar el rol
if ($rol === 'administrador') {
    if (empty($rolNuevo)) {
        $_SESSION['erroresEdit']['rol'] = 'El rol es obligatorio.';
    }
}

if (!empty($_SESSION['erroresEdit'])) {
    $_SESSION['usuarioEdit'] = $_POST;
    header("Location: ../perfil.php");
    exit();
}

$modificaUsuarioParams = [
    'dni' => $dni,
    'email' => $email,
    'credit_card' => $numero_tarjeta_credito,
    'usuario_rol' => $rol,
    'clave' => $clave,
    'nombre' => $nombre,
    'apellidos' => $apellidos,
    'nuevo_dni' => $nuevoDni,
    'rol' => $rolNuevo,
];

$result = $database->modificaUsuario(...array_values($modificaUsuarioParams));

if ($result) {
    $database->registrarEvento('Usuario editado', $dni);
    header("Location: ../perfil.php");
} else {
    $database->registrarEvento('Fallo al editar un usuario', $dni);
    $_SESSION['erroresEdit']['general'] = 'No se pudo actualizar el perfil. Intente de nuevo.';
    header("Location: ../perfil.php");
}

