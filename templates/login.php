<!-- Modal de Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="src/login.php" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?php echo isset($_SESSION['erroresLogin']['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['loginData']['email'] ?? ''); ?>">
                        <?php if (isset($_SESSION['erroresLogin']['email'])) : ?>
                            <div class="invalid-feedback">
                                <?php echo $_SESSION['erroresLogin']['email']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control <?php echo isset($_SESSION['erroresLogin']['password']) ? 'is-invalid' : ''; ?>" id="password" name="password">
                        <?php if (isset($_SESSION['erroresLogin']['password'])) : ?>
                            <div class="invalid-feedback">
                                <?php echo $_SESSION['erroresLogin']['password']; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Iniciar sesión</button>
                </form>
                <div class="mt-3">
                    <p>¿No tienes una cuenta? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Regístrate aquí</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['erroresLogin'])) : ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {});
            loginModal.show();
        });
    </script>
    <?php
    // Limpiar los errores de la sesión después de mostrarlos
    unset($_SESSION['erroresLogin']);
    unset($_SESSION['loginData']);
    ?>
<?php endif; ?>