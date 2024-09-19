<?php
session_start();

function obtenerUltimoBackup()
{
    // Conexión a la base de datos
    $dsn = 'mysql:host=localhost;dbname=evaanngc2324;charset=utf8mb4';
    $usuario = 'evaanngc2324';
    $contraseña = 'too0WpcZxM00wagU';

    try {
        // Crear una instancia de PDO
        $pdo = new PDO($dsn, $usuario, $contraseña);

        // Obtener la última copia de seguridad
        $stmt = $pdo->query("SELECT archivo, fecha FROM backup ORDER BY fecha DESC LIMIT 1");
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$backup) {
            throw new Exception("No se encontró ninguna copia de seguridad.");
        }

        return $backup;
    } catch (PDOException $e) {
        throw new Exception("Error al conectar con la base de datos: " . $e->getMessage());
    }
}

try {
    $backup = obtenerUltimoBackup();
    $filename = "backup_" . date("Y-m-d_H-i-s", strtotime($backup['fecha'])) . ".sql";
    $sql = $backup['archivo'];

    // Forzar la descarga del archivo
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $sql;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
