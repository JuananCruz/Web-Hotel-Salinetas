<!DOCTYPE html>
<html lang="es">

<?php include 'templates/head.php'; ?>

<body class="d-flex flex-column min-vh-100">

    <?php include 'templates/header.php'; ?>

    <main class="flex-grow-1">
        <div class="container mt-5">
            <?php
            include 'src/database.php';

            function mostrarLogs()
            {
                global $database;

                $logs = $database->getLogs();

                if ($logs && $logs->num_rows > 0) {
                    echo "<h2>Registros del Sistema</h2>";
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-striped'>";
                    echo "<thead class='thead-dark'>";
                    echo "<tr><th>Fecha y Hora</th><th>Usuario</th><th>Descripción</th></tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    while ($row = $logs->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['fecha'] . "</td>";
                        echo "<td>" . $row['usuario_dni'] . "</td>";
                        echo "<td>" . $row['accion'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo "<p>No hay registros de eventos en el sistema.</p>";
                }
            }

            mostrarLogs();
            ?>
        </div>
    </main>


    <?php include 'templates/footer.php'; ?>

    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>
