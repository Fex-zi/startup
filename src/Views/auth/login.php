<h3 class="text-center mb-4">Welcome Back</h3>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors['general'] as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="login">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" 
               class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
               id="email" 
               name="email" 
               value="<?= htmlspecialchars($old_input['email'] ?? '') ?>"
               required>
        <?php if (isset($errors['email'])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['email'][0]) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" 
               class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
               id="password" 
               name="password" 
               required>
        <?php if (isset($errors['password'])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['password'][0]) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            Remember me
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In
    </button>
</form>

<div class="text-center">
    <p class="mb-2">Don't have an account?</p>
    <a href="register" class="btn btn-outline-primary">
        <i class="fas fa-user-plus me-2"></i>Create Account
    </a>
</div>

<hr class="my-4">

<div class="text-center">
    <small class="text-muted">
        <a href="forgot-password" class="text-decoration-none">Forgot your password?</a>
    </small>
</div>
