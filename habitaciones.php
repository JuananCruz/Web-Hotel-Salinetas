<!DOCTYPE html>
<html lang="es">

<?php include 'templates/head.php'; ?>

<body>
    <?php include 'templates/header.php'; ?>


    <?php $rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'anónimo'; ?>
    <main class="container my-5">
        <section id="habitaciones">
            <h2 class="mb-4">Tipos de Habitaciones</h2>
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div id="carouselStandard" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/habitacion1.jpg" class="d-block w-100" alt="Habitación Estándar cama">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/estadarVista.jpg" class="d-block w-100" alt="Habitación Estándar vista">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/estandarBaño.jpg" class="d-block w-100" alt="Habitación Estándar baño">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselStandard" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselStandard" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Estándar</h5>
                            <p class="card-text"><strong>Capacidad:</strong> 3 personas</p>
                            <p class="card-text">Confortable habitación con todas las comodidades básicas, baño privado y una cama doble.</p>
                            <?php if ($rol !== 'administrador') : ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="<?php echo ($rol === 'cliente' || $rol === 'recepcionista') ? '#reservaModal' : '#loginModal'; ?>" data-tipo="Estandar">Reservar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div id="carouselDeluxe" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/habDeluxe.jpg" class="d-block w-100" alt="Habitación Doble Deluxe cama">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/deluxePiscina.jpg" class="d-block w-100" alt="Habitación Doble Deluxe con piscina">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/banioDoble.jpg" class="d-block w-100" alt="Habitación Doble Deluxe baño">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselDeluxe" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselDeluxe" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Doble Deluxe</h5>
                            <p class="card-text"><strong>Capacidad:</strong> 3 personas</p>
                            <p class="card-text">Amplia habitación con zona de estar, cama doble, baño privado y balcón con piscina privada.</p>
                            <?php if ($rol !== 'administrador') : ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="<?php echo ($rol === 'cliente' || $rol === 'recepcionista') ? '#reservaModal' : '#loginModal'; ?>" data-tipo="Doble Deluxe">Reservar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-4">
                    <div class="card">
                        <div id="carouselSuite" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/suite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe cama">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/SalonSuite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe salón">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/CocinaSuite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe cocina">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/baneraSuite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe bañera de piedra">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/BanioSuite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe baño">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/terrazaSuite.jpg" class="d-block w-100" alt="Habitación Suite Deluxe terraza con jacuzzi">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselSuite" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselSuite" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Suite Deluxe</h5>
                            <p class="card-text"><strong>Capacidad:</strong> 5 personas</p>
                            <p class="card-text">Lujosa suite con salón propio, dormitorio principal, sala de estar, bañera de piedra, baño doble y terraza con jacuzzi privado.</p>
                            <?php if ($rol !== 'administrador') : ?>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="<?php echo ($rol === 'cliente' || $rol === 'recepcionista') ? '#reservaModal' : '#loginModal'; ?>" data-tipo="Suite Deluxe">Reservar</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>
    <?php include 'templates/footer.php'; ?>

    <!-- Modal de Reserva -->
    <div class="modal fade" id="reservaModal" tabindex="-1" aria-labelledby="reservaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservaModalLabel">Realizar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($_SESSION['erroresReserva']['general'])) : ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['erroresReserva']['general']; ?>
                        </div>
                    <?php endif; ?>
                    <form id="reservaForm" method="POST" action="src/procesar_reserva.php" novalidate>
                        <input type="hidden" id="tipoHabitacion" name="tipoHabitacion">
                        <div class="mb-3">
                            <label for="numeroPersonas" class="form-label">Número de Personas</label>
                            <input type="number" class="form-control <?php echo isset($_SESSION['erroresReserva']['numeroPersonas']) ? 'is-invalid' : ''; ?>" id="numeroPersonas" name="numeroPersonas" value="<?php echo $_SESSION['reservaData']['numeroPersonas'] ?? ''; ?>">
                            <?php if (isset($_SESSION['erroresReserva']['numeroPersonas'])) : ?>
                                <div class="invalid-feedback"><?php echo $_SESSION['erroresReserva']['numeroPersonas']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="diaEntrada" class="form-label">Día de Entrada</label>
                            <input type="date" class="form-control <?php echo isset($_SESSION['erroresReserva']['diaEntrada']) ? 'is-invalid' : ''; ?>" id="diaEntrada" name="diaEntrada" value="<?php echo $_SESSION['reservaData']['diaEntrada'] ?? ''; ?>" required>
                            <?php if (isset($_SESSION['erroresReserva']['diaEntrada'])) : ?>
                                <div class="invalid-feedback"><?php echo $_SESSION['erroresReserva']['diaEntrada']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="diaSalida" class="form-label">Día de Salida</label>
                            <input type="date" class="form-control <?php echo isset($_SESSION['erroresReserva']['diaSalida']) ? 'is-invalid' : ''; ?>" id="diaSalida" name="diaSalida" value="<?php echo $_SESSION['reservaData']['diaSalida'] ?? ''; ?>" required>
                            <?php if (isset($_SESSION['erroresReserva']['diaSalida'])) : ?>
                                <div class="invalid-feedback"><?php echo $_SESSION['erroresReserva']['diaSalida']; ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="comentarios" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="comentarios" name="comentarios"><?php echo $_SESSION['reservaData']['comentarios'] ?? ''; ?></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Reservar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var reservaModal = new bootstrap.Modal(document.getElementById('reservaModal'));
            <?php if (isset($_SESSION['erroresReserva']) && !empty($_SESSION['erroresReserva'])) : ?>
                reservaModal.show();
                var tipoHabitacion = localStorage.getItem('tipoHabitacion');
                if (tipoHabitacion) {
                    document.getElementById('tipoHabitacion').value = tipoHabitacion;
                }
                <?php unset($_SESSION['erroresReserva']); ?>
            <?php endif; ?>

            // Asignar tipo de habitación al modal de reserva
            var reservaButtons = document.querySelectorAll('button[data-tipo]');
            reservaButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var tipo = button.getAttribute('data-tipo');
                    document.getElementById('tipoHabitacion').value = tipo;
                    localStorage.setItem('tipoHabitacion', tipo);
                });
            });

            // Limpiar localStorage y los datos de reserva al cerrar el modal manualmente
            var modalElement = document.getElementById('reservaModal');
            modalElement.addEventListener('hidden.bs.modal', function() {
                localStorage.removeItem('tipoHabitacion');
                // Limpia los datos del formulario si se cierra el modal manualmente
                document.getElementById('reservaForm').reset();
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>