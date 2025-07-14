<h3 class="text-center mb-4">Create Your Account</h3>

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors['general'] as $error): ?>
            <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="register">
    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" 
                   class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                   id="first_name" 
                   name="first_name" 
                   value="<?= htmlspecialchars($old_input['first_name'] ?? '') ?>"
                   required>
            <?php if (isset($errors['first_name'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['first_name'][0]) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" 
                   class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                   id="last_name" 
                   name="last_name" 
                   value="<?= htmlspecialchars($old_input['last_name'] ?? '') ?>"
                   required>
            <?php if (isset($errors['last_name'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['last_name'][0]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

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
        <label for="user_type" class="form-label">Account Type</label>
        <select class="form-select <?= isset($errors['user_type']) ? 'is-invalid' : '' ?>" 
                id="user_type" 
                name="user_type" 
                required>
            <option value="">Choose your role...</option>
            <option value="startup" <?= ($old_input['user_type'] ?? '') === 'startup' ? 'selected' : '' ?>>
                <i class="fas fa-rocket"></i> Startup Founder
            </option>
            <option value="investor" <?= ($old_input['user_type'] ?? '') === 'investor' ? 'selected' : '' ?>>
                <i class="fas fa-dollar-sign"></i> Investor
            </option>
        </select>
        <?php if (isset($errors['user_type'])): ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['user_type'][0]) ?>
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
        <div class="form-text">Password must be at least 8 characters long.</div>
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input type="password" 
               class="form-control" 
               id="password_confirmation" 
               name="password_confirmation" 
               required>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
        <label class="form-check-label" for="terms">
            I agree to the <a href="terms" target="_blank">Terms of Service</a> and 
            <a href="privacy" target="_blank">Privacy Policy</a>
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="fas fa-user-plus me-2"></i>Create Account
    </button>
</form>

<div class="text-center">
    <p class="mb-0">Already have an account?</p>
    <a href="login" class="btn btn-outline-primary">
        <i class="fas fa-sign-in-alt me-2"></i>Sign In
    </a>
</div>
