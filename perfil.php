<!DOCTYPE html>
<html lang="es">
<?php include 'templates/head.php'; ?>

<body>
    <?php include 'templates/header.php'; ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Mi Perfil</h2>
                <?php
                require 'src/database.php';

                $rol = $_SESSION['rol'] ?? 'anónimo';
                $userId = $_SESSION['user_id'] ?? null;
                $erroresEdit = $_SESSION['erroresEdit'] ?? [];

                $resultado = $database->getUsuarioPerfil($userId);
                $usuarioLogueado = $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;

                if (!empty($usuarioLogueado)) {
                    // Verificar si el usuario tiene reservas activas
                    $clienteConReserva = $database->getClienteConReserva($usuarioLogueado['dni']);
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $usuarioLogueado['nombre'] . ' ' . $usuarioLogueado['apellidos']; ?>
                            </h5>
                            <p class="card-text">Correo electrónico: <?php echo $usuarioLogueado['email']; ?></p>
                            <p class="card-text">DNI: <?php echo $usuarioLogueado['dni']; ?></p>
                            <p class="card-text">Número de Tarjeta: **** **** ****
                                <?php echo substr($usuarioLogueado['numero_tarjeta_credito'], -4); ?>
                            </p>
                            <?php if ($rol === 'cliente'): ?>
                                <?php if ($clienteConReserva) { ?>
                                    <div class="alert alert-danger mt-4 py-2" role="alert">
                                        No se puede editar la información mientras tengas una reserva activa.
                                    </div>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-primary edit-button" data-bs-toggle="modal"
                                        data-bs-target="#editarDatosPersonalesModal"
                                        data-usuario='<?php echo json_encode($usuarioLogueado); ?>'>
                                        Editar Mi Perfil
                                    </button>
                                <?php } ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                } else {
                    echo '<p class="text-muted">No se encontró información de tu perfil.</p>';
                }
                ?>
            </div>
            <?php if ($rol === 'recepcionista' || $rol === 'cliente'): ?>
                <div class="col-md-6">
                    <h2 class="mb-4">Gestión de Reservas</h2>
                    <form method="GET" action="perfil.php" class="mb-4">
                        <!-- Agregar campo oculto para indicar filtro aplicado -->
                        <input type="hidden" name="filtro_aplicado" value="true">
                        <div class="mb-3">
                            <label for="filtro_comentarios" class="form-label">Filtrar por comentarios</label>
                            <input type="text" class="form-control" id="filtro_comentarios" name="filtro_comentarios"
                                value="<?php echo htmlspecialchars($_GET['filtro_comentarios'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="filtro_fecha_inicio" class="form-label">Fecha de entrada (desde)</label>
                            <input type="date" class="form-control" id="filtro_fecha_inicio" name="filtro_fecha_inicio"
                                value="<?php echo htmlspecialchars($_GET['filtro_fecha_inicio'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="filtro_fecha_fin" class="form-label">Fecha de entrada (hasta)</label>
                            <input type="date" class="form-control" id="filtro_fecha_fin" name="filtro_fecha_fin"
                                value="<?php echo htmlspecialchars($_GET['filtro_fecha_fin'] ?? '', ENT_QUOTES); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    </form>
                    <div class="list-group">
                        <?php
                        // Verificar si se ha aplicado un filtro
                        $filtroAplicado = isset($_GET['filtro_aplicado']) && $_GET['filtro_aplicado'] === 'true';

                        // Obtener los filtros
                        $filtroComentarios = $_GET['filtro_comentarios'] ?? '';
                        $filtroFechaInicio = $_GET['filtro_fecha_inicio'] ?? '';
                        $filtroFechaFin = $_GET['filtro_fecha_fin'] ?? '';

                        // Verificar si se han proporcionado filtros
                        $filtrosVacios = empty($filtroComentarios) && empty($filtroFechaInicio) && empty($filtroFechaFin);

                        // Obtener las reservas según si se aplicó un filtro o no
                        if ($rol === 'cliente') {
                            if ($filtroAplicado && !$filtrosVacios) {
                                $resultado = $database->getClienteReservasFiltradas($userId, $filtroComentarios, $filtroFechaInicio, $filtroFechaFin);
                            } else {
                                $resultado = $database->getClienteReservas($userId);
                            }
                        } else {
                            if ($filtroAplicado && !$filtrosVacios) {
                                $resultado = $database->getAllReservasFiltradas($filtroComentarios, $filtroFechaInicio, $filtroFechaFin);
                            } else {
                                $resultado = $database->getAllReservas();
                            }
                        }

                        if ($resultado !== null && $resultado->num_rows > 0) {
                            while ($fila = $resultado->fetch_assoc()) {
                                ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="mb-1">Reserva <?php echo $fila['id']; ?></h5>
                                        <small>Estancia: <?php echo $fila['dia_entrada']; ?> |
                                            <?php echo $fila['dia_salida']; ?></small>
                                    </div>
                                    <p class="mb-1">Cliente ID: <?php echo $fila['cliente_dni']; ?></p>
                                    <p class="mb-1">Habitacion: <?php echo $fila['numero_habitacion']; ?></p>
                                    <p class="mb-1">Numero Personas: <?php echo $fila['numero_personas']; ?></p>
                                    <small>Comentarios: <?php echo $fila['comentarios']; ?></small>
                                    <?php if ($rol === 'cliente' || $rol === 'recepcionista') { ?>
                                        <div class="d-flex mt-2">
                                            <button type="button" class="btn btn-primary edit-reservation-button me-2"
                                                data-bs-toggle="modal" data-bs-target="#editarReservaModal"
                                                data-reserva-id="<?php echo $fila['id']; ?>">
                                                Editar Reserva
                                            </button>
                                            <button type="button" class="btn btn-danger cancel-reservation-button"
                                                data-bs-toggle="modal" data-bs-target="#eliminarReservaModal"
                                                data-reserva-id="<?php echo $fila['id']; ?>">
                                                Cancelar Reserva
                                            </button>

                                        </div>
                                    <?php } ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<p class="text-muted">No hay reservas disponibles.</p>';
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-md-6">
                <?php if ($rol === 'recepcionista' || $rol === 'administrador'): ?>
                    <h2>Información de los Usuarios</h2>
                    <?php

                    if ($rol === 'recepcionista') {
                        $resultado = $database->getlUsuariosRecepcionista();
                    } elseif ($rol === 'administrador') {
                        $resultado = $database->getlUsuariosAdmin($userId);
                    }

                    if ($resultado->num_rows > 0) {
                        $usuarios = [];
                        while ($usuario = $resultado->fetch_assoc()) {
                            $usuarios[] = $usuario;
                        }
                        ?>
                        <?php foreach ($usuarios as $usuario) {
                            $clienteConReserva = $database->getClienteConReserva($usuario['dni']);

                            ?>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $usuario['nombre'] . ' ' . $usuario['apellidos']; ?></h5>
                                    <p class="card-text">Correo electrónico: <?php echo $usuario['email']; ?></p>
                                    <p class="card-text">DNI: <?php echo $usuario['dni']; ?></p>
                                    <p class="card-text">Número de Tarjeta: **** **** ****
                                        <?php echo substr($usuario['numero_tarjeta_credito'], -4); ?>
                                    </p>
                                    <?php if ($clienteConReserva) { ?>
                                        <div class="alert alert-danger mt-4 py-2" role="alert">
                                            No se puede eliminar/editar un usuario con una reserva asignada. Elimine primero la reserva.
                                        </div>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary edit-button" data-bs-toggle="modal"
                                            data-bs-target="#editarDatosPersonalesModal"
                                            data-usuario='<?php echo json_encode($usuario); ?>'>
                                            Editar Datos Personales
                                        </button>
                                        <?php if (($rol === 'recepcionista' && $usuario['rol'] === 'cliente') || $rol === 'administrador') { ?>
                                            <button type="button" class="btn btn-danger delete-button" data-bs-toggle="modal"
                                                data-bs-target="#modalDeleteUsuario" data-dni="<?php echo $usuario['dni']; ?>">
                                                Borrar Usuario
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                    } else {
                        echo '<p class="text-muted">No se encontró información de usuarios.</p>';
                    }
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'templates/footer.php'; ?>

    <!-- Modal para editar datos personales -->
    <div class="modal fade" id="editarDatosPersonalesModal" tabindex="-1"
        aria-labelledby="editarDatosPersonalesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <img src="icon/file4.webp" alt="Editar Datos Personales" class="img-fluid"
                        style="max-width: 20px; margin-right: 5px;">
                    <h5 class="modal-title" id="editarDatosPersonalesModalLabel">Editar Datos Personales</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editarDatosPersonalesForm" action="src/update_user.php" method="POST" novalidate>
                        <div id="form-fields"></div>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        var rol = "<?php echo $rol; ?>";
        var usuarioLogueado = <?php echo json_encode($usuarioLogueado); ?>;
        var formFieldsContainer = document.getElementById('form-fields');
        var erroresEdit = <?php echo json_encode($erroresEdit); ?>;
        var usuarioEdit = <?php echo json_encode($_SESSION['usuarioEdit'] ?? null); ?>;

        const fields = {
            cliente: [{
                id: 'dni',
                type: 'hidden'
            },
            {
                id: 'email',
                label: 'Correo electrónico',
                type: 'email'
            },
            {
                id: 'clave',
                label: 'Clave',
                type: 'password'
            },
            {
                id: 'numero_tarjeta_credito',
                label: 'Número de Tarjeta',
                type: 'text'
            }
            ],
            recepcionista: [{
                id: 'nombre',
                label: 'Nombre',
                type: 'text'
            },
            {
                id: 'apellidos',
                label: 'Apellidos',
                type: 'text'
            },
            {
                id: 'dni',
                label: 'DNI',
                type: 'text'
            },
            {
                id: 'email',
                label: 'Correo electrónico',
                type: 'email'
            },
            {
                id: 'clave',
                label: 'Clave',
                type: 'password'
            },
            {
                id: 'numero_tarjeta_credito',
                label: 'Número de Tarjeta',
                type: 'text'
            }
            ],
            administrador: [{
                id: 'nombre',
                label: 'Nombre',
                type: 'text'
            },
            {
                id: 'apellidos',
                label: 'Apellidos',
                type: 'text'
            },
            {
                id: 'dni',
                label: 'DNI',
                type: 'text'
            },
            {
                id: 'email',
                label: 'Correo electrónico',
                type: 'email'
            },
            {
                id: 'clave',
                label: 'Clave',
                type: 'password'
            },
            {
                id: 'numero_tarjeta_credito',
                label: 'Número de Tarjeta',
                type: 'text'
            },
            {
                id: 'rol',
                label: 'Rol',
                type: 'select',
                options: ['cliente', 'recepcionista', 'administrador']
            }
            ]
        };

        const generateFormFields = (role, usuario, erroresEdit) => {
            formFieldsContainer.innerHTML = '';
            fields[role].forEach(field => {
                const div = document.createElement('div');
                div.className = 'mb-3';
                const error = erroresEdit[field.id] ? `<div class="text-danger">${erroresEdit[field.id]}</div>` : '';
                const value = erroresEdit[field.id] ? '' : (usuario && usuario[field.id] ? usuario[field.id] : '');
                if (field.type === 'select') {
                    let optionsHTML = '';
                    field.options.forEach(option => {
                        optionsHTML += `<option value="${option}" ${value === option ? 'selected' : ''}>${option}</option>`;
                    });
                    div.innerHTML = `
                        <label for="${field.id}" class="form-label">${field.label}</label>
                        <select class="form-control" id="${field.id}" name="${field.id}">
                            ${optionsHTML}
                        </select>
                        ${error}
                    `;
                } else {
                    if (field.id === 'dni' && role === 'cliente') {
                        div.innerHTML = `<input type="hidden" id="${field.id}" name="${field.id}" value="${value}">`;
                    } else {
                        div.innerHTML = `
                            <label for="${field.id}" class="form-label">${field.label}</label>
                            <input type="${field.type}" class="form-control" id="${field.id}" name="${field.id}" value="${field.id === 'clave' ? '' : value}">
                            ${error}
                        `;
                    }
                }
                formFieldsContainer.appendChild(div);
            });
        };

        generateFormFields(rol, usuarioEdit || usuarioLogueado, erroresEdit);

        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function () {
                const usuario = JSON.parse(this.getAttribute('data-usuario'));
                generateFormFields(rol, usuario, erroresEdit);
                for (let key in usuario) {
                    if (document.getElementById(key) && key !== 'clave') {
                        document.getElementById(key).value = usuario[key];
                    }
                }
                document.getElementById('clave').value = '';
            });
        });

        <?php if (!empty($erroresEdit)): ?>
            var editModal = new bootstrap.Modal(document.getElementById('editarDatosPersonalesModal'), {});
            editModal.show();
            <?php unset($_SESSION['erroresEdit']); ?>
        <?php endif; ?>

        // Limpieza de los datos y errores al cerrar el modal manualmente
        var modalElement = document.getElementById('editarDatosPersonalesModal');
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Limpiar el contenido del formulario
            formFieldsContainer.innerHTML = '';
            // Generar los campos vacíos para el formulario
            generateFormFields(rol, {}, {});
        });
    </script>




    <!-- Modal de Borrar Usuario -->
    <div class="modal fade" id="modalDeleteUsuario" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Borrar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas borrar a este usuario?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteUsuarioForm" method="POST" action="src/delete_usuario.php">
                        <input type="hidden" name="dni" id="dni_to_delete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Borrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para configurar el modal de borrado
        function setupDeleteModal(dni) {
            document.getElementById('dni_to_delete').value = dni;
        }

        // Agregar un evento de clic a todos los botones de borrado
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Obtener el DNI del usuario de los atributos de datos
                    const dni = button.dataset.dni;
                    // Configurar el DNI en el formulario de borrado
                    setupDeleteModal(dni);
                });
            });

            // Asegurarse de que el modal tiene el DNI correcto antes de mostrarse
            $('#modalDeleteUsuario').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Botón que activó el modal
                var dni = button.data('dni'); // Extraer info de los atributos de datos
                setupDeleteModal(dni);
            });
        });
    </script>



    <!-- Modal de Editar Reserva -->
    <div class="modal fade" id="editarReservaModal" tabindex="-1" aria-labelledby="editarReservaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarReservaModalLabel">Editar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="src/edit_reserva.php" novalidate>
                        <div class="mb-3">
                            <label for="edit_reserva_id" class="form-label">ID de Reserva</label>
                            <input type="text" class="form-control" id="edit_reserva_id" name="id" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="edit_reserva_comentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="edit_reserva_comentarios" name="comentarios"></textarea>
                            <?php if (!empty($_SESSION['erroresEditReserva']['id'])): ?>
                                <div class="alert alert-danger mt-2" role="alert">
                                    <?php echo $_SESSION['erroresEditReserva']['id']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para configurar el modal de edición de reserva
        function setupEditarReservaModal(reserva) {
            // Rellenar los campos del formulario de edición con los datos de la reserva
            document.getElementById('edit_reserva_id').value = reserva.id;
            // Mostrar los comentarios actuales de la reserva en el campo de comentarios
            document.getElementById('edit_reserva_comentarios').value = reserva.comentarios;
        }

        // Escuchar el evento cuando se carga el DOM para configurar los botones de editar reserva
        document.addEventListener('DOMContentLoaded', function () {
            const editarReservaButtons = document.querySelectorAll('.edit-reservation-button');
            editarReservaButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Obtener los datos de la reserva desde el atributo data-reserva del botón
                    const reservaId = this.getAttribute('data-reserva-id');
                    const comentarios = this.getAttribute('data-reserva-comentarios');
                    // Configurar el modal de edición de reserva con los datos de la reserva
                    setupEditarReservaModal({
                        id: reservaId,
                        comentarios: comentarios
                    });
                });
            });
        });
    </script>



    <!-- Modal de Eliminar Reserva -->
    <div class="modal fade" id="eliminarReservaModal" tabindex="-1" aria-labelledby="eliminarReservaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarReservaModalLabel">Eliminar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta reserva?</p>
                </div>
                <div class="modal-footer">
                    <form id="eliminarReservaForm" method="POST" action="src/delete_reserva.php">
                        <input type="hidden" name="id" id="reserva_id_to_delete">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para configurar el modal de eliminación de reserva
        function setupEliminarReservaModal(id) {
            document.getElementById('reserva_id_to_delete').value = id;
        }

        // Agregar un evento de clic a todos los botones de eliminación de reserva
        document.addEventListener('DOMContentLoaded', function () {
            const eliminarReservaButtons = document.querySelectorAll('.cancel-reservation-button'); // Cambio de clase
            eliminarReservaButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Obtener el ID de la reserva de los atributos de datos
                    const id = button.dataset.reservaId; // Cambio en el nombre del atributo
                    // Configurar el ID de la reserva en el formulario de eliminación
                    setupEliminarReservaModal(id);
                });
            });

            // Asegurarse de que el modal tenga el ID de la reserva correcto antes de mostrarse
            $('#eliminarReservaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Botón que activó el modal
                var id = button.data('reservaId'); // Extraer info de los atributos de datos
                setupEliminarReservaModal(id);
            });
        });
    </script>




</body>

</html>