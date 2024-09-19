<!DOCTYPE html>
<html lang="es">

<?php include 'templates/head.php'; ?>


<body>
    <?php include 'templates/header.php'; ?>
    <?php include 'src/mostrar_info.php'; ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <section class="descripcion mb-4">
                    <h2>MIL Y UNA COMODIDADES EN UN GRAN HOTEL</h2>
                    <p>Vivir unas vacaciones llenas de lujo, cargadas de diversión y aderezadas con un toque exótico es
                        ahora posible en el Hotel Salinetas</a>.</p>
                    <p>Este hotel de 5 estrellas en Salinetas (Gran Canaria) ofrece a los clientes más selectos un
                        ambiente que en todo evoca a los países africanos; desde el aspecto de naturaleza salvaje de su
                        flora, hasta el color de sus edificios, terroso y fascinante como las laderas del Kilimanjaro.
                    </p>
                    <p>A veces todo lo que necesitamos es un entorno sugestivo y cautivador, sin renunciar a la
                        comodidad y al descanso que todos precisamos en nuestro día a día.</p>
                    <p>Por todo esto, este exclusivo resort de Gran Canaria ha conseguido posicionarse como uno de los
                        complejos hoteleros más originales, impactantes y espectaculares de la isla.</p>
                    <section class="galeria mb-4">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 5"></button>
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="5" aria-label="Slide 6"></button>
                            </div>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="img/deluxePiscina.jpg" class="d-block w-100" alt="Imagen del hotel">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/estadarVista.jpg" class="d-block w-100" alt="Imagen Habitación 1">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/general.jpeg" class="d-block w-100" alt="Imagen Habitación 2">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/hall.jpg" class="d-block w-100" alt="Imagen Habitación 3">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/principal2.jpg" class="d-block w-100" alt="Imagen Habitación 4">
                                </div>
                                <div class="carousel-item">
                                    <img src="img/suite.jpg" class="d-block w-100" alt="Imagen Habitación 5">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </section>
                </section>
            </div>
            <div class="col-lg-4">
                <section class="info-secundaria mb-4">
                    <h2>Información del Hotel</h2>
                    <ul class="list-group">
                        <li class="list-group-item">Número total de habitaciones: <span id="total-habitaciones"><?php echo $infoHotel['totalHabitaciones']; ?></span></li>
                        <li class="list-group-item">Número de habitaciones libres: <span id="habitaciones-libres"><?php echo $infoHotel['habitacionesLibres']; ?></span></li>
                        <li class="list-group-item">Capacidad total del hotel: <span id="capacidad-total"><?php echo $capacidadTotal; ?></span>
                            huéspedes</li>
                        <li class="list-group-item">Número de huéspedes alojados: <span id="huespedes-alojados"><?php echo $infoHotel['huéspedesAlojados']; ?></span></li>
                    </ul>
                </section>
                <section class="eventos mb-4">
                    <h2>Eventos de interés</h2>
                    <ul class="list-group">
                        <li class="list-group-item">
                            <h5 class="list-group-item-heading">Horario Cafetería</h5>
                            <ul class="list-unstyled ms-3">
                                <li>Lunes a viernes: 8:00 - 20:00</li>
                                <li>Sábado y domingo: 9:00 - 18:00</li>
                            </ul>
                        </li>
                        <li class="list-group-item">
                            <h5 class="list-group-item-heading">Horario Spa</h5>
                            <ul class="list-unstyled ms-3">
                                <li>Lunes a domingo: 10:00 - 22:00</li>
                            </ul>
                        </li>
                        <li class="list-group-item">
                            <h5 class="list-group-item-heading">Horario Gimnasio</h5>
                            <ul class="list-unstyled ms-3">
                                <li>Lunes a domingo: 6:00 - 23:00</li>
                            </ul>
                        </li>
                        <li class="list-group-item">
                            <h5 class="list-group-item-heading">Eventos Sociales</h5>
                            <ul class="list-unstyled ms-3">
                                <li>Varía según el evento, consulte nuestro calendario de eventos</li>
                            </ul>
                        </li>
                    </ul>
                </section>
            </div>
        </div>
    </main>

    <?php include 'templates/login.php'; ?>
    <?php include 'templates/registro.php'; ?>
    <?php include 'templates/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>