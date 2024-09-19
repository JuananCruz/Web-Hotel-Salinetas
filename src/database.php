<?php

$credentials = json_decode(file_get_contents(__DIR__ . '/credenciales.json'), true);


class Database
{
    private $connection;

    public function __construct()
    {
        $this->open_connection();
    }

    private function open_connection()
    {
        global $credentials;
        $this->connection = new mysqli($credentials['DB_SERVER'], $credentials['DB_USERNAME'], $credentials['DB_PASSWORD'], $credentials['DB_NAME']);

        if ($this->connection->connect_error) {
            die("Conexión fallida: " . $this->connection->connect_error);
        }
    }

    public function close_connection()
    {
        if (isset($this->connection)) {
            $this->connection->close();
            unset($this->connection);
        }
    }

    public function query($sql)
    {
        $result = $this->connection->query($sql);
        $this->confirm_query($result);
        return $result;
    }

    private function confirm_query($result)
    {
        if (!$result) {
            die("La consulta a la base de datos falló: " . $this->connection->error);
        }
    }

    public function escape_value($value)
    {
        return $this->connection->real_escape_string($value);
    }

    public function create_database_if_not_exists()
    {
        global $credentials;
        $conn = new mysqli($credentials['DB_SERVER'], $credentials['DB_USERNAME'], $credentials['DB_PASSWORD']);
        $dbname = $credentials['DB_NAME'];

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        if (!mysqli_select_db($conn, $dbname)) {
            $sql = "CREATE DATABASE $dbname";
            if ($conn->query($sql) === TRUE) {
                echo "Base de datos creada correctamente";
            } else {
                echo "Error al crear la base de datos: " . $conn->error;
            }
        }

        $conn->close();
    }

    public function get_last_insert_id()
    {
        return $this->connection->insert_id;
    }

    public function getBackups()
    {
        $sql = "SELECT * FROM backup";
        return $this->query($sql);
    }

    public function getlUsuariosAdmin($userId)
    {
        $sql = "SELECT * FROM usuarios WHERE NOT dni = '$userId'";
        return $this->query($sql);
    }

    public function getlUsuariosRecepcionista()
    {
        $sql = "SELECT * FROM usuarios WHERE rol = 'cliente'";
        return $this->query($sql);
    }

    public function getUsuarioPerfil($userId)
    {
        $sql = "SELECT * FROM usuarios WHERE dni = '$userId'";
        return $this->query($sql);
    }

    public function getUsuarioLogin($email)
    {
        $sql = "SELECT dni, clave, rol FROM usuarios WHERE email = '$email'";
        return $this->query($sql);
    }

    public function getUsuarioByDNI($dni)
    {
        $query = "SELECT * FROM usuarios WHERE dni = '{$this->escape_value($dni)}'";
        return $this->query($query);
    }

    public function getUsuarioByEmail($email)
    {
        $query = "SELECT * FROM usuarios WHERE email = '{$this->escape_value($email)}'";
        return $this->query($query);
    }

    public function getUsuarioByTarjetaCredito($tarjeta_credito)
    {
        $query = "SELECT * FROM usuarios WHERE numero_tarjeta_credito = '{$this->escape_value($tarjeta_credito)}'";
        return $this->query($query);
    }

    public function insertaNuevoUsuario($nombre, $apellidos, $dni, $email, $hashed_password, $credit_card, $rol)
    {
        $sql = "INSERT INTO usuarios (nombre, apellidos, dni, email, clave, numero_tarjeta_credito, rol) VALUES ('$nombre', '$apellidos', '$dni', '$email', '$hashed_password', '$credit_card', '$rol')";
        return $this->query($sql);
    }


    public function modificaUsuario($dni, $email, $credit_card, $usuario_rol, $clave = null, $nombre = null, $apellidos = null, $nuevo_dni = null, $rol = null)
    {
        // Inicializamos la consulta
        $sql = "UPDATE usuarios SET email = '$email', numero_tarjeta_credito = '$credit_card'";

        // Agregamos los campos adicionales según el rol
        if (!empty($clave)) {
            $clave = password_hash($clave, PASSWORD_DEFAULT);
            $sql .= ", clave = '$clave'";
        }

        if ($usuario_rol === 'recepcionista' || $usuario_rol === 'administrador') {
            if (!empty($nombre)) {
                $sql .= ", nombre = '$nombre'";
            }
            if (!empty($apellidos)) {
                $sql .= ", apellidos = '$apellidos'";
            }
            if (!empty($nuevo_dni)) {
                $sql .= ", dni = '$nuevo_dni'";
            }
        }

        if ($usuario_rol === 'administrador' && !empty($rol)) {
            $sql .= ", rol = '$rol'";
        }

        // Condición WHERE
        $sql .= " WHERE dni = '$dni'";

        // Ejecutamos la consulta
        return $this->query($sql);
    }

    public function eliminarUsuario($dni)
    {
        $query = "DELETE FROM usuarios WHERE dni = '$dni'";
        return $this->query($query);
    }

    public function setHabitacionReservada($numeroHabitacion)
    {
        $sql = "UPDATE habitaciones SET estado = 'reservada' WHERE numero_habitacion = '$numeroHabitacion'";
        return $this->query($sql);
    }

    public function setHabitacionDisponible($numeroHabitacion)
    {
        $sql = "UPDATE habitaciones set estado = 'disponible' WHERE numero_habitacion = '$numeroHabitacion'";
        return $this->query($sql);
    }

    public function getHabitacionReservada($reserva = null)
    {
        if ($reserva) {
            $sql = "SELECT numero_habitacion FROM reservas WHERE id = '$reserva'";
        } else {
            $sql = "SELECT numero_habitacion FROM reservas";
        }
        $result = $this->query($sql);

        if ($result) {
            // Si se proporciona un ID, devolvemos una sola fila
            if ($reserva) {
                $row = $result->fetch_assoc();
                return $row['numero_habitacion'];
            } else {
                // Si no se proporciona un ID, devolvemos todas las habitaciones reservadas
                $habitaciones = [];
                while ($row = $result->fetch_assoc()) {
                    $habitaciones[] = $row['numero_habitacion'];
                }
                return $habitaciones;
            }
        }
        return false;
    }

    public function insertaNuevaReserva($userId, $numeroHabitacion, $numeroPersonas, $comentarios, $diaEntrada, $diaSalida, $estado, $marcaTiempo)
    {
        $sql = "INSERT INTO reservas (cliente_dni, numero_habitacion, numero_personas, comentarios, dia_entrada, dia_salida, estado, marca_tiempo) VALUES ('$userId', '$numeroHabitacion', '$numeroPersonas', '$comentarios', '$diaEntrada', '$diaSalida', '$estado', '$marcaTiempo')";
        return $this->query($sql);
    }

    public function getAllReservas()
    {
        $sql = "SELECT * FROM reservas";
        return $this->query($sql);
    }

    public function getIdReserva($clientedni)
    {
        $sql = "SELECT id  FROM reservas WHERE cliente_dni = $clientedni";
        return $this->query($sql);
    }

    public function getClienteReservas($userId)
    {
        $sql = "SELECT * FROM reservas WHERE cliente_dni = '$userId'";
        return $this->query($sql);
    }

    public function getClienteConReserva($userId)
    {
        $sql = "SELECT DISTINCT cliente_dni FROM reservas WHERE cliente_dni = ?;";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['cliente_dni'];
        } else {
            return null;
        }
    }

    public function getDniFromUserId($userId)
    {
        $sql = "SELECT dni FROM usuarios WHERE dni = ?";
        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        return $usuario['dni'] ?? null;
    }

    public function getClienteReservasFiltradas($userId, $filtroComentarios = '', $filtroFechaInicio = '', $filtroFechaFin = '')
    {
        $dni = $this->getDniFromUserId($userId);
        if (!$dni) {
            return null;
        }

        $sql = "SELECT * FROM reservas WHERE cliente_dni = ?";

        $params = [$dni];
        $types = 's';

        if (!empty($filtroComentarios)) {
            $sql .= " AND comentarios LIKE ?";
            $params[] = '%' . $filtroComentarios . '%';
            $types .= 's';
        }
        if (!empty($filtroFechaInicio)) {
            $sql .= " AND dia_entrada >= ?";
            $params[] = $filtroFechaInicio;
            $types .= 's';
        }
        if (!empty($filtroFechaFin)) {
            $sql .= " AND dia_entrada <= ?";
            $params[] = $filtroFechaFin;
            $types .= 's';
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAllReservasFiltradas($filtroComentarios = '', $filtroFechaInicio = '', $filtroFechaFin = '')
    {
        $sql = "SELECT * FROM reservas WHERE 1 = 1";

        $params = [];
        $types = ''; // Inicializar la cadena de tipos de parámetros

        if (!empty($filtroComentarios)) {
            $sql .= " AND comentarios LIKE ?";
            $params[] = '%' . $filtroComentarios . '%';
            $types .= 's'; // Agregar el tipo de parámetro para una cadena
        }
        if (!empty($filtroFechaInicio)) {
            $sql .= " AND dia_entrada >= ?";
            $params[] = $filtroFechaInicio;
            $types .= 's'; // Agregar el tipo de parámetro para una cadena
        }
        if (!empty($filtroFechaFin)) {
            $sql .= " AND dia_entrada <= ?";
            $params[] = $filtroFechaFin;
            $types .= 's'; // Agregar el tipo de parámetro para una cadena
        }

        // Verificar que al menos un tipo de parámetro esté presente
        if (empty($types)) {
            return null; // No hay filtros, no se puede ejecutar la consulta
        }

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            // Error en la preparación de la consulta
            return null;
        }

        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result) {
            // Error al obtener el resultado
            return null;
        }

        return $result;
    }







    public function editarComentariosReserva($reserva_id, $comentarios)
    {
        $sql = "UPDATE reservas SET comentarios = '$comentarios' WHERE id = '$reserva_id'";
        return $this->query($sql);
    }

    public function eliminarReserva($reserva_id)
    {
        $query = "DELETE FROM reservas WHERE id = '$reserva_id'";
        return $this->query($query);
    }

    public function setReserva($idReserva)
    {
        $sql = "UPDATE reservas SET estado = 'reservada' WHERE id = '$idReserva'";
        return $this->query($sql);
    }

    // Método en tu clase database para obtener una habitación disponible con la capacidad necesaria
    public function getNumeroHabitacionDisponible($capacidadNecesaria, $tipoHabitacion)
    {
        // Realiza la consulta para encontrar una habitación disponible con la capacidad necesaria
        $query = "SELECT h.numero_habitacion 
          FROM habitaciones h
          INNER JOIN tipo_habitaciones th ON h.tipo = th.nombre
          WHERE h.estado = 'disponible' 
          AND th.capacidad >= $capacidadNecesaria 
          AND h.numero_habitacion NOT IN (
              SELECT numero_habitacion 
              FROM reservas 
              WHERE estado = 'pendiente' OR estado = 'reservada'
          ) 
          AND th.nombre = '$tipoHabitacion'
              LIMIT 1";

        $result = $this->query($query);


        // Verifica si se encontró una habitación disponible
        if ($result && $result->num_rows > 0) {
            // Retorna el número de habitación disponible
            $row = $result->fetch_assoc();
            return $row['numero_habitacion'];
        } else {
            return null;
        }
    }

    public function getCapacidadTotalPorHabitaciones()
    {
        // Consulta para obtener todas las habitaciones y sus tipos
        $queryHabitaciones = "SELECT tipo FROM habitaciones";

        // Ejecutamos la consulta para obtener todas las habitaciones
        $resultHabitaciones = $this->query($queryHabitaciones);

        // Inicializamos la capacidad total
        $capacidadTotal = 0;

        // Verificamos si se obtuvieron resultados
        if ($resultHabitaciones && $resultHabitaciones->num_rows > 0) {
            // Recorremos todas las habitaciones
            while ($rowHabitacion = $resultHabitaciones->fetch_assoc()) {
                $tipoHabitacion = $rowHabitacion['tipo'];

                // Para cada tipo de habitación, obtenemos su capacidad
                $capacidadHabitacion = $this->getCapacidadHabitacion($tipoHabitacion);

                // Si se encontró la capacidad, la sumamos al total
                if ($capacidadHabitacion !== null) {
                    $capacidadTotal += $capacidadHabitacion;
                }
            }
        }

        // Retornamos la capacidad total
        return $capacidadTotal;
    }

    public function getCapacidadHabitacion($tipoHabitacion)
    {
        // Realiza la consulta para encontrar la capacidad de una habitación de un tipo específico
        $query = "SELECT capacidad FROM tipo_habitaciones WHERE nombre = '$tipoHabitacion'";

        $result = $this->query($query);

        // Verifica si se encontró el tipo de habitación
        if ($result && $result->num_rows > 0) {
            // Retorna la capacidad del tipo de habitación
            $row = $result->fetch_assoc();
            return $row['capacidad'];
        } else {
            return null;
        }
    }

    public function getTipoHabitacionNum($numero_habitacion)
    {
        $query = "SELECT tipo FROM habitaciones WHERE numero_habitacion = '$numero_habitacion'";
        return $this->query($query);
    }

    public function getAllHabitaciones()
    {
        $query = "SELECT * FROM habitaciones";
        return $this->query($query);
    }

    public function habitacionExiste($numero_habitacion)
    {
        // Consulta para verificar si la habitación ya existe en la base de datos
        $query = "SELECT COUNT(*) AS total FROM habitaciones WHERE numero_habitacion = '$numero_habitacion'";
        $result = $this->query($query);

        // Verifica si se encontró algún resultado
        if ($result && $result->num_rows > 0) {
            // Obtiene el número de filas devueltas por la consulta
            $row = $result->fetch_assoc();
            $total = $row['total'];

            // Si el total es mayor que cero, la habitación existe
            return $total > 0;
        } else {
            // Si no se encontraron resultados, la habitación no existe
            return false;
        }
    }


    public function getTipoHabitacion()
    {
        $query = "SELECT nombre from tipo_habitaciones";
        return $this->query($query);
    }

    public function getInfoDelTipoHabitacion($tipo)
    {
        $query = "SELECT * FROM tipo_habitaciones WHERE nombre = '$tipo'";
        return $this->query($query);
    }

    public function getDatosHabitacion($numero_habitacion)
    {
        $sql = "SELECT * FROM habitaciones WHERE numero_habitacion = '$numero_habitacion'";
        return $this->query($sql);
    }

    public function getEstadoHabitacion($numero_habitacion)
    {
        $sql = "SELECT estado FROM habitaciones WHERE numero_habitacion = '$numero_habitacion'";
        return $this->query($sql);
    }


    public function getHabitacionesDisponibles()
    {
        $query = "SELECT * FROM habitaciones WHERE estado = 'disponible'";
        return $this->query($query);
    }

    public function insertaHabitacion($numero_habitacion, $precio_por_noche, $tipo)
    {

        $query = "INSERT INTO habitaciones (numero_habitacion, precio_por_noche, tipo) VALUES ('$numero_habitacion', $precio_por_noche, '$tipo')";
        return $this->query($query);
    }

    public function eliminarHabitacion($numero_habitacion)
    {
        $query = "DELETE FROM habitaciones WHERE numero_habitacion = '$numero_habitacion'";
        return $this->query($query);
    }

    public function modificaHabitacion($numero_habitacion, $nuevo_numero_habitacion = null, $precio_por_noche = null, $tipo = null)
    {
        // Inicializamos la consulta
        $sql = "UPDATE habitaciones SET numero_habitacion = '$nuevo_numero_habitacion', precio_por_noche = $precio_por_noche, tipo = '$tipo' WHERE numero_habitacion = '$numero_habitacion'";

        // Ejecutamos la consulta
        return $this->query($sql);
    }

    public function getNumHabitacionesTotal()
    {
        // Inicializamos la consulta
        $sql = "SELECT COUNT(*) AS total FROM habitaciones";
        // Ejecutamos la consulta
        return $this->query($sql);
    }

    public function getNumHabitacionesLibres()
    {
        // Inicializamos la consulta
        $sql = "SELECT COUNT(*) AS libres FROM habitaciones WHERE estado = 'disponible'";
        // Ejecutamos la consulta
        return $this->query($sql);
    }

    public function getHuespedesTotales()
    {
        // Inicializamos la consulta
        $sql = "SELECT SUM(numero_personas) AS alojados FROM reservas WHERE dia_salida > NOW()";
        // Ejecutamos la consulta
        return $this->query($sql);
    }

    public function insertarRegistroLogs($accion, $usuario_dni, $fecha)
    {
        $accion = $this->escape_value($accion);
        $usuario_dni = $this->escape_value($usuario_dni);
        $fecha = $this->escape_value($fecha);

        $sql = "INSERT INTO logs (accion, usuario_dni, fecha) VALUES ('$accion', '$usuario_dni', '$fecha')";

        $result = $this->query($sql);

        if ($result) {
            // Inserción exitosa
            return true;
        } else {
            // Error al insertar el registro
            return false;
        }
    }

    function registrarEvento($accion, $usuario_dni)
    {
        global $database;

        // Obtener la fecha y hora actual
        $fecha_actual = date('Y-m-d H:i:s');

        // Insertar el registro en la tabla logs
        $result = $database->insertarRegistroLogs($accion, $usuario_dni, $fecha_actual);

        // Verificar si la inserción fue exitosa
        if ($result) {
            // Registro exitoso
            return true;
        } else {
            // Error al registrar el evento
            return false;
        }
    }


    public function getLogs()
    {
        $sql = "SELECT * FROM logs ORDER BY fecha DESC";
        return $this->query($sql);
    }
}

$database = new Database();
