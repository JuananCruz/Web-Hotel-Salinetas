<!-- Modal Registro -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <img src="icon/file4.webp" alt="Registro" class="img-fluid" style="max-width: 20px; margin-right: 5px;">
                <h5 class="modal-title" id="registerModalLabel">Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="registerForm" action="src/register.php" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="registerNombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="registerNombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['RegisterData']['nombre'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresRegistro']['nombre'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['nombre'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerApellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="registerApellidos" name="apellidos" value="<?php echo htmlspecialchars($_SESSION['RegisterData']['apellidos'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresRegistro']['apellidos'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['apellidos'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerDNI" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="registerDNI" name="dni" value="<?php echo htmlspecialchars($_SESSION['RegisterData']['dni'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresRegistro']['dni'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['dni'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerEmail" class="form-label">Correo electrónico</label>
                        <input type="email" class="form-control" id="registerEmail" name="email" value="<?php echo htmlspecialchars($_SESSION['RegisterData']['email'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresRegistro']['email'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerPassword" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="registerPassword" name="password">
                        <?php if (isset($_SESSION['erroresRegistro']['password'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['password'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerConfirmPassword" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="registerConfirmPassword" name="confirmPassword">
                        <?php if (isset($_SESSION['erroresRegistro']['confirm_password'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['confirmPassword'] ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="registerCreditCard" class="form-label">Tarjeta de Crédito</label>
                        <input type="text" class="form-control" id="registerCreditCard" name="credit_card" value="<?php echo htmlspecialchars($_SESSION['RegisterData']['credit_card'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresRegistro']['credit_card'])) : ?>
                            <div class="text-danger"><?= $_SESSION['erroresRegistro']['credit_card'] ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($rol === 'administrador') { ?>
                        <div class="mb-3">
                            <label for="registerRol" class="form-label">Rol</label>
                            <select class="form-control" id="registerRol" name="rol">
                                <option value="cliente">Cliente</option>
                                <option value="recepcionista">Recepcionista</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    <?php } ?>
                    <?php if (isset($_SESSION['erroresRegistro']['general'])) : ?>
                        <div class="text-danger"><?= $_SESSION['erroresRegistro']['general'] ?></div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['erroresRegistro'])) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var registerModal = new bootstrap.Modal(document.getElementById('registerModal'), {});
            registerModal.show();
        });
    </script>
    <?php
    // Limpiar los errores de la sesión después de mostrarlos
    unset($_SESSION['erroresRegistro']);
    unset($_SESSION['RegisterData']);
    ?>
<?php endif; ?>