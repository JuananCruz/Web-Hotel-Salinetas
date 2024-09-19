<?php
session_start();
$rol = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'anónimo';
?>

<div class="header-nav-container">
    <header class="header p-3 text-center">
    <img src="icon/logoHotelSalinetas.png" alt="logoSalinetas" class="img-fluid" style="max-width: 200px;">
    </header>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <img src="icon/home.webp" alt="Home" class="img-fluid" style="max-width: 20px;">
                            Página Principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="habitaciones.php">
                            <img src="icon/habitaciones.webp" alt="Habitaciones" class="img-fluid" style="max-width: 20px;">
                            Habitaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="servicios.php">
                            <img src="icon/servicios.webp" alt="Servicios" class="img-fluid" style="max-width: 20px;">
                            Servicios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="listado-habitaciones.php">
                            <img src="icon/listaHabitaciones.webp" alt="Listado Habitaciones" class="img-fluid" style="max-width: 20px;">
                            Listado Habitaciones
                        </a>
                    </li>
                    <?php if ($rol === 'administrador') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logs.php">
                                <img src="icon/logs.webp" alt="Logs" class="img-fluid" style="max-width: 20px;">
                                Ver Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="operaciones_bbdd.php">
                                <img src="icon/backup.webp" alt="BBDD" class="img-fluid" style="max-width: 20px;">
                                Operaciones sobre la BBDD
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($rol === 'anónimo') { ?>
                        <li class="nav-item" id="login-btn">
                            <button class="btn btn-primary btn-separation mb-2 mb-lg-0" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Iniciar sesión
                            </button>
                        </li>
                        <li class="nav-item" id="register-btn">
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#registerModal">
                                Registro
                            </button>
                        </li>
                    <?php } else { ?>
                        <?php if ($rol === 'recepcionista' || $rol === 'administrador') { ?>
                            <li class="nav-item">
                                <button class="btn btn-primary btn-separation mb-2 mb-lg-0" data-bs-toggle="modal" data-bs-target="#registerModal">
                                    <?php echo $rol === 'recepcionista' ? ' Nuevo Cliente' : 'Nuevo Usuario'; ?>
                                </button>
                            </li>
                        <?php } ?>
                        <li class="nav-item" id="profile-btn">
                            <a href="perfil.php" class="nav-link" style="display: inline-flex; align-items: center;">
                                <img src="icon/perfil.webp" alt="Perfil" class="img-fluid" style="max-width: 40px;">
                            </a>
                        </li>
                        <li class="nav-item" id="logout-btn">
                            <a href="src/logout.php" class="nav-link" style="display: inline-flex; align-items: center; margin-left: -10px;">
                                <img src="icon/logout.webp" alt="Logout" class="img-fluid" style="max-width: 40px;">
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</div>
