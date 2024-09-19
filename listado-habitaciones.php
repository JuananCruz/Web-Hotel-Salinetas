<!DOCTYPE html>
<html lang="es">

<?php include 'templates/head.php'; ?>

<body>

    <?php include 'templates/header.php'; ?>

    <main class="flex-grow-1">
        <div class="container mt-5">
            <h2>Habitaciones Disponibles</h2>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php
                require 'src/database.php';
                $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'anónimo';
                if ($rol === 'recepcionista') {
                    $habitaciones = $database->getAllHabitaciones();
                } else {
                    $habitaciones = $database->getHabitacionesDisponibles();
                }

                if ($habitaciones->num_rows > 0) {
                    while ($row = $habitaciones->fetch_assoc()) {
                        $numero_habitacion = $row["numero_habitacion"];
                        $resultadoTipo = $database->getTipoHabitacionNum($numero_habitacion);
                        if ($resultadoTipo->num_rows > 0) {
                            $tipoRow = $resultadoTipo->fetch_assoc();
                            $tipo_habitacion = $tipoRow['tipo'];
                            $resultadoInfoTipo = $database->getInfoDelTipoHabitacion($tipo_habitacion);
                            if ($resultadoInfoTipo->num_rows > 0) {
                                $habitacion_del_tipo = $resultadoInfoTipo->fetch_assoc();
                                echo '<div class="col">';
                                echo '<div class="card h-100 shadow-sm">';
                                echo '<div class="card-body">';
                                echo '<h5 class="card-title">' . $row["numero_habitacion"] . '</h5>';
                                echo '<hr>';
                                echo '<p class="card-text"><strong>Estado:</strong> ' . $row["estado"] . '</p>';
                                echo '<p class="card-text"><strong>Precio por Noche:</strong> ' . $row["precio_por_noche"] . '&euro; </p>';
                                echo '<p class="card-text"><strong>Capacidad:</strong> ' . $habitacion_del_tipo['capacidad'] . '</p>';
                                echo '<p class="card-text"><strong>Descripción:</strong> ' . $habitacion_del_tipo['descripcion'] . '</p>';
                                echo '<p class="card-text"><strong>Tipo Habitación:</strong> ' . $habitacion_del_tipo['nombre'] . '</p>';
                                $fotografias = explode(",", $habitacion_del_tipo['fotografias']);
                                if (!empty($fotografias[0])) {
                                    $carouselId = 'carousel' . $numero_habitacion;
                                    echo '<div id="' . $carouselId . '" class="carousel slide" data-bs-ride="carousel">';
                                    echo '<div class="carousel-inner">';
                                    foreach ($fotografias as $index => $imagen_blob) {
                                        $active_class = $index === 0 ? 'active' : '';
                                        echo '<div class="carousel-item ' . $active_class . '">';
                                        echo '<img src="' . $imagen_blob . '" class="d-block w-100" alt="Imagen ' . ($index + 1) . '">';
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '<button class="carousel-control-prev" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="prev">';
                                    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                                    echo '<span class="visually-hidden">Previous</span>';
                                    echo '</button>';
                                    echo '<button class="carousel-control-next" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="next">';
                                    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                                    echo '<span class="visually-hidden">Next</span>';
                                    echo '</button>';
                                    echo '</div>';
                                }
                                if ($row['estado'] === 'reservada') {
                                    echo '<div class="alert alert-danger mt-4 py-2" role="alert">No se puede eliminar/editar una habitación reservada.</div>';
                                } elseif ($rol === 'recepcionista') {
                                    echo '<div class="d-flex justify-content-between align-items-center mt-3">';
                                    echo '<button class="btn btn-outline-primary btn-md me-2 edit-button btn-edit" data-bs-toggle="modal" data-bs-target="#editRoomModal" data-habitacion="' . htmlspecialchars(json_encode($row)) . '">Editar</button>';
                                    echo '<button class="btn btn-outline-danger btn-md delete-room-button" data-bs-toggle="modal" data-bs-target="#modalDeleteHabitacion" data-numero_habitacion="' . $numero_habitacion . '">Borrar</button>';
                                    echo '</div>';
                                }

                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<p class="text-muted">No se encontró información del tipo de habitación.</p>';
                            }
                        } else {
                            echo '<p class="text-muted">No se encontró el tipo de habitación para el número de habitación: ' . $numero_habitacion . '.</p>';
                        }
                    }
                } else {
                    echo "<div class='text-center'>No hay habitaciones disponibles.</div>";
                }
                ?>
            </div>

            <?php
            if ($rol === 'recepcionista') {
                echo '<div class="text-center mt-4">';
                echo '<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">Añadir Habitación</button>';
                echo '</div>';
            }
            ?>
        </div>
    </main>



    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>

    <?php include 'templates/footer.php'; ?>

    <!-- Modal de Añadir Habitación -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Añadir Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="src/add_habitacion.php" novalidate>
                        <div class="mb-3">
                            <label for="numero_habitacion" class="form-label">Número de Habitación</label>
                            <input type="text" class="form-control <?php echo isset($_SESSION['erroresAddHabitacion']['numero_habitacion']) ? 'is-invalid' : ''; ?>" id="numero_habitacion" name="numero_habitacion" value="">
                            <?php if (isset($_SESSION['erroresAddHabitacion']['numero_habitacion'])) : ?>
                                <div class="invalid-feedback">
                                    <?php echo $_SESSION['erroresAddHabitacion']['numero_habitacion']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="precio_por_noche" class="form-label">Precio por Noche</label>
                            <input type="text" class="form-control <?php echo isset($_SESSION['erroresAddHabitacion']['precio_por_noche']) ? 'is-invalid' : ''; ?>" id="precio_por_noche" name="precio_por_noche" value="">
                            <?php if (isset($_SESSION['erroresAddHabitacion']['precio_por_noche'])) : ?>
                                <div class="invalid-feedback">
                                    <?php echo $_SESSION['erroresAddHabitacion']['precio_por_noche']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Habitación</label>
                            <select class="form-control" id="tipo" name="tipo">
                                <?php
                                $tipos_habitacion = $database->getTipoHabitacion();
                                while ($tipo = $tipos_habitacion->fetch_assoc()) {
                                    $selected = (isset($_SESSION['datosFormulario']['tipo']) && $_SESSION['datosFormulario']['tipo'] == $tipo['nombre']) ? 'selected' : '';
                                    echo '<option value="' . $tipo['nombre'] . '" ' . $selected . '>' . $tipo['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Añadir Habitación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (!empty($_SESSION['erroresAddHabitacion'])) : ?>
            document.addEventListener('DOMContentLoaded', function() {
                var addRoomModal = new bootstrap.Modal(document.getElementById('addRoomModal'), {});
                addRoomModal.show();
            });
            <?php
            // Limpiar los errores de la sesión después de mostrarlos
            unset($_SESSION['erroresAddHabitacion']);
            ?>
        <?php endif; ?>
        // Limpiar datos del formulario al abrir el modal
        $('#addRoomModal').on('show.bs.modal', function(e) {
            $('#numero_habitacion').val('');
            $('#precio_por_noche').val('');
            $('#tipo').val('');
        });
    </script>





    <!-- Modal de Editar Habitación -->
    <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoomModalLabel">Editar Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="src/edit_habitacion.php" novalidate>
                        <div class="mb-3">
                            <label for="edit_numero_habitacion" class="form-label">Número de Habitación Actual</label>
                            <input type="text" class="form-control" id="edit_numero_habitacion" name="numero_habitacion" readonly>
                        </div>
                        <!-- Mostrar error para el número de habitación -->
                        <?php if (isset($_SESSION['erroresEditHabitacion']['numero_habitacion'])) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['erroresEditHabitacion']['numero_habitacion']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="edit_nuevo_numero_habitacion" class="form-label">Nuevo Número de Habitación</label>
                            <input type="text" class="form-control <?php echo isset($_SESSION['erroresEditHabitacion']['nuevo_numero_habitacion']) ? 'is-invalid' : ''; ?>" id="edit_nuevo_numero_habitacion" name="nuevo_numero_habitacion">
                            <!-- Mostrar error para el nuevo número de habitación -->
                            <?php if (isset($_SESSION['erroresEditHabitacion']['nuevo_numero_habitacion'])) : ?>
                                <div class="invalid-feedback">
                                    <?php echo $_SESSION['erroresEditHabitacion']['nuevo_numero_habitacion']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="edit_precio_por_noche" class="form-label">Precio por Noche</label>
                            <input type="text" class="form-control <?php echo isset($_SESSION['erroresEditHabitacion']['precio_por_noche']) ? 'is-invalid' : ''; ?>" id="edit_precio_por_noche" name="precio_por_noche">
                            <!-- Mostrar error para el precio por noche -->
                            <?php if (isset($_SESSION['erroresEditHabitacion']['precio_por_noche'])) : ?>
                                <div class="invalid-feedback">
                                    <?php echo $_SESSION['erroresEditHabitacion']['precio_por_noche']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo" class="form-label">Tipo de Habitación</label>
                            <select class="form-control <?php echo isset($_SESSION['erroresEditHabitacion']['tipo']) ? 'is-invalid' : ''; ?>" id="edit_tipo" name="tipo">
                                <?php
                                $tipos_habitacion = $database->getTipoHabitacion();
                                while ($tipo = $tipos_habitacion->fetch_assoc()) {
                                    $selected = (isset($_SESSION['datosFormulario']['tipo']) && $_SESSION['datosFormulario']['tipo'] == $tipo['nombre']) ? 'selected' : '';
                                    echo '<option value="' . $tipo['nombre'] . '" ' . $selected . '>' . $tipo['nombre'] . '</option>';
                                }
                                ?>
                            </select>
                            <!-- Mostrar error para el tipo de habitación -->
                            <?php if (isset($_SESSION['erroresEditHabitacion']['tipo'])) : ?>
                                <div class="invalid-feedback">
                                    <?php echo $_SESSION['erroresEditHabitacion']['tipo']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Editar Habitación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para configurar el modal de edición
        function setupEditModal(habitacion) {
            // Rellenar los campos del formulario de edición con los datos de la habitación
            document.getElementById('edit_numero_habitacion').value = habitacion.numero_habitacion;
            document.getElementById('edit_precio_por_noche').value = habitacion.precio_por_noche;
            document.getElementById('edit_tipo').value = habitacion.tipo;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const habitacion = JSON.parse(this.getAttribute('data-habitacion'));
                    setupEditModal(habitacion);
                });
            });
        });
    </script>


    <!-- Modal de Borrar Habitación -->
    <div class="modal fade" id="modalDeleteHabitacion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Borrar Habitación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas borrar esta habitación?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteHabitacionForm" method="POST" action="src/delete_habitacion.php">
                        <input type="hidden" name="numero_habitacion" id="numero_habitacion_to_delete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Borrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para configurar el modal de borrado
        function setupDeleteModal(numero_habitacion) {
            document.getElementById('numero_habitacion_to_delete').value = numero_habitacion;
        }

        // Agregar un evento de clic a todos los botones de borrado
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-room-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Obtener el número de habitación de los atributos de datos
                    const numero_habitacion = button.dataset.numero_habitacion;
                    // Configurar el número de habitación en el formulario de borrado
                    setupDeleteModal(numero_habitacion);
                });
            });

            // Asegurarse de que el modal tiene el número de habitación correcto antes de mostrarse
            $('#modalDeleteHabitacion').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Botón que activó el modal
                var numero_habitacion = button.data('numero_habitacion'); // Extraer info de los atributos de datos
                setupDeleteModal(numero_habitacion);
            });
        });
    </script>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


</body>

</html>