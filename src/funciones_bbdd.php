<?php
session_start();
$erroresBBDD = [];

// Función para obtener una copia de seguridad de la base de datos
function obtenerCopiaSeguridad()
{
    global $erroresBBDD;
    // Conexión a la base de datos
    $dsn = 'mysql:host=localhost;dbname=evaanngc2324;charset=utf8mb4';
    $usuario = 'evaanngc2324';
    $contraseña = 'too0WpcZxM00wagU';

    try {
        // Crear una instancia de PDO
        $pdo = new PDO($dsn, $usuario, $contraseña);

        // Obtener las tablas existentes en la base de datos
        $consultaTablas = $pdo->query("SHOW TABLES");
        $tablas = $consultaTablas->fetchAll(PDO::FETCH_COLUMN);

        // Inicializar el contenido del archivo de copia de seguridad
        $backupSQL = '';

        // Iterar sobre cada tabla y obtener su estructura y datos
        foreach ($tablas as $tabla) {
            if ($tabla !== 'backup' && $tabla !== 'tipo_habitaciones') {
                // Obtener los datos de la tabla
                if ($tabla === 'usuarios') {
                    $consultaDatos = $pdo->query("SELECT * FROM $tabla WHERE rol <> 'administrador'");
                } else {
                    $consultaDatos = $pdo->query("SELECT * FROM $tabla");
                }
                $datosTabla = $consultaDatos->fetchAll(PDO::FETCH_ASSOC);

                // Agregar los datos de la tabla al contenido del backup
                foreach ($datosTabla as $fila) {
                    $columnas = implode(", ", array_map(function ($valor) use ($pdo) {
                        return $pdo->quote($valor);
                    }, $fila));
                    $backupSQL .= "INSERT INTO $tabla VALUES ($columnas);\n";
                }

                $backupSQL .= "\n";
            }
        }

        // Guardar la información de la copia de seguridad en la tabla backup
        $timestamp = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("INSERT INTO backup (archivo, fecha) VALUES (:archivo, :fecha)");
        $stmt->bindParam(':archivo', $backupSQL, PDO::PARAM_LOB);
        $stmt->bindParam(':fecha', $timestamp);
        $stmt->execute();

        $_SESSION['success'] = "Se ha guardado una copia de seguridad en la base de datos.";
    } catch (PDOException $e) {
        $erroresBBDD[] = "Error al obtener la copia de seguridad: ";
        $_SESSION['erroresBBDD'] = $erroresBBDD;
    }
}

// Función para restaurar la base de datos a partir de una copia de seguridad

function restaurarDesdeCopiaSeguridad($backupID)
{
    global $erroresBBDD;

    // Conexión a la base de datos
    $dsn = 'mysql:host=localhost;dbname=evaanngc2324;charset=utf8mb4';
    $usuario = 'evaanngc2324';
    $contraseña = 'too0WpcZxM00wagU';

    try {
        // Crear una instancia de PDO
        $pdo = new PDO($dsn, $usuario, $contraseña);

        // Obtener el archivo SQL desde la base de datos
        $stmt = $pdo->prepare("SELECT archivo FROM backup WHERE id = :id");
        $stmt->bindParam(':id', $backupID, PDO::PARAM_INT);
        $stmt->execute();
        $backup = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$backup) {
            $erroresBBDD[] = "Error: No se encontró el backup con ID $backupID.";
            $_SESSION['erroresBBDD'] = $erroresBBDD;
            return;
        }

        $sql = $backup['archivo'];

        // Desactivar las verificaciones de claves foráneas
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        // Ejecutar las consultas SQL
        $pdo->exec($sql);

        // Activar las verificaciones de claves foráneas
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

        $_SESSION['success'] = "La restauración se ha completado con éxito.";
    } catch (PDOException $e) {
        $erroresBBDD[] = "Error al restaurar la base de datos: " . $e->getMessage();
        $_SESSION['erroresBBDD'] = $erroresBBDD;
    }
}


// Función para reiniciar la base de datos
function reiniciarBaseDeDatos()
{
    global $erroresBBDD;
    // Conexión a la base de datos (reemplaza con tus propios datos)
    $dsn = 'mysql:host=localhost;dbname=evaanngc2324;charset=utf8mb4';
    $usuario = 'evaanngc2324';
    $contraseña = 'too0WpcZxM00wagU';

    try {
        // Crear una instancia de PDO
        $pdo = new PDO($dsn, $usuario, $contraseña);

        if (!existeAdmin($pdo)) {
            $erroresBBDD[] = "Error: No hay administradores en la base de datos.";
            $_SESSION['erroresBBDD'] = $erroresBBDD;
            return;
        }

        // Obtener todos los usuarios administradores
        $stmt = $pdo->prepare("SELECT dni FROM usuarios WHERE rol = 'administrador'");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Eliminar todos los datos de las tablas excepto los usuarios administradores en la tabla usuarios
        $tablas = ['logs', 'reservas', 'habitaciones'];
        foreach ($tablas as $tabla) {
            $pdo->exec("DELETE FROM $tabla");
        }

        // Eliminar todos los usuarios que no sean administradores
        $pdo->exec("DELETE FROM usuarios WHERE rol <> 'administrador'");

        $_SESSION['success'] = "La base de datos ha sido reiniciada con éxito, excepto los datos de los administradores.";
    } catch (PDOException $e) {
        $erroresBBDD[] = "Error al reiniciar la base de datos: ";
        $_SESSION['erroresBBDD'] = $erroresBBDD;
    }
}

// Lógica para verificar si existe al menos un administrador en la base de datos
function existeAdmin($pdo)
{
    try {
        // Consulta para verificar si hay al menos un administrador
        $sql = "SELECT COUNT(*) FROM usuarios WHERE rol = 'administrador'";
        $resultado = $pdo->query($sql);

        // Obtener el resultado
        $cantidad = $resultado->fetchColumn();

        // Devolver true si hay al menos un administrador, de lo contrario false
        return $cantidad > 0;
    } catch (PDOException $e) {
        global $erroresBBDD;
        $erroresBBDD[] = "Error al verificar la existencia de administradores: ";
        $_SESSION['erroresBBDD'] = $erroresBBDD;
        return false;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar qué operación se está solicitando
    if (isset($_POST["operacion"])) {
        $operacion = $_POST["operacion"];

        // Realizar la operación correspondiente
        switch ($operacion) {
            case "obtener_copia_seguridad":
                obtenerCopiaSeguridad();
                break;
            case "restaurar_desde_copia":
                if (isset($_POST["id"])) {
                    $backupID = $_POST["id"];
                    restaurarDesdeCopiaSeguridad($backupID);
                } else {
                    $erroresBBDD[] = "Error: ID de backup no proporcionado.";
                    $_SESSION['erroresBBDD'] = $erroresBBDD;
                }
                break;
            case "reiniciar_base_datos":
                reiniciarBaseDeDatos();
                break;
            default:
                $erroresBBDD[] = "Error: Operación no válida.";
                $_SESSION['erroresBBDD'] = $erroresBBDD;
        }
    } else {
        $erroresBBDD[] = "Error: Operación no especificada.";
        $_SESSION['erroresBBDD'] = $erroresBBDD;
    }
    // Redirigir a la página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
