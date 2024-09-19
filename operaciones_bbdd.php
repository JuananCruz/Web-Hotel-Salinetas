<!DOCTYPE html>
<html lang="es">

<head>
    <?php include 'templates/head.php'; ?>
</head>

<body class="d-flex flex-column min-vh-100">
    <?php include 'templates/header.php'; ?>
    <?php include 'src/database.php'; ?>

    <main class="flex-grow-1">
        <div class="container mt-5">
            <h1 class="mb-4">Operaciones de Base de Datos</h1>

            <!-- Mostrar errores -->
            <?php if (isset($_SESSION['erroresBBDD']) && !empty($_SESSION['erroresBBDD'])) : ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($_SESSION['erroresBBDD'] as $error) : ?>
                            <?php echo htmlspecialchars($error); ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['erroresBBDD']); ?>
            <?php endif; ?>

            <!-- Mostrar mensaje de éxito -->
            <?php if (isset($_SESSION['success'])) : ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <div class="row">
                <!-- Mostrar los backups disponibles -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">Copias de seguridad disponibles</h3>
                                <div class="d-flex justify-content-between mb-3">
                                    <form action="src/funciones_bbdd.php" method="post">
                                        <input type="hidden" name="operacion" value="obtener_copia_seguridad">
                                        <button type="submit" class="btn btn-primary">Crear copia de seguridad</button>
                                    </form>
                                    <button type="button" class="btn btn-warning" id="reiniciarBtn" data-bs-toggle="modal" data-bs-target="#confirmarReinicio">Reiniciar base de datos del sistema</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Fecha</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Consultar la tabla de backups
                                            $backups = $database->getBackups(); // Esta función debería obtener los backups de la base de datos

                                            // Mostrar cada backup en una fila de la tabla
                                            foreach ($backups as $backup) {
                                                echo "<tr>";
                                                echo "<td>{$backup['id']}</td>";
                                                echo "<td>{$backup['fecha']}</td>";
                                                echo "<td>
                                                    <form action=\"src/funciones_bbdd.php\" method=\"post\" style=\"display:inline;\">
                                                        <input type=\"hidden\" name=\"operacion\" value=\"restaurar_desde_copia\">
                                                        <input type=\"hidden\" name=\"id\" value=\"{$backup['id']}\">
                                                        <button type=\"submit\" class=\"btn btn-dark\">Restaurar</button>
                                                    </form>
                                                </td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmarReinicio" tabindex="-1" role="dialog" aria-labelledby="confirmarReinicioLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarReinicioLabel">Confirmar reinicio de base de datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="fw-bold">¿Está seguro de que desea reiniciar la base de datos? </p>
                    <p>Esta acción borrará toda la información almacenada en el sistema y dejará las tablas necesarias para la aplicación creadas pero sin ninguna tupla almacenada.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form action="src/funciones_bbdd.php" method="post">
                        <input type="hidden" name="operacion" value="reiniciar_base_datos">
                        <button type="submit" class="btn btn-danger">Reiniciar base de datos</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>
    <?php include 'templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        document.getElementById('reiniciarBtn').addEventListener('click', function() {
            $('#confirmarReinicio').modal('show');
        });
    </script>
</body>

</html>