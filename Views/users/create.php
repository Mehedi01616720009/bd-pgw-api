<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - PHP MVC Framework</title>
    <link rel="stylesheet" href="<?= asset('styles/main.css') ?>">
</head>

<body>
    <div class="container">
        <h1>Create New User</h1>

        <form method="POST" action="<?= route('users.store') ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= old('name') ?>"
                    class="<?= hasError('name') ? 'error' : '' ?>">
                <?php if (hasError('name')): ?>
                    <span class="error-message"><?= errors('name') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= old('email') ?>"
                    class="<?= hasError('email') ? 'error' : '' ?>">
                <?php if (hasError('email')): ?>
                    <span class="error-message"><?= errors('email') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="<?= hasError('password') ? 'error' : '' ?>">
                <?php if (hasError('password')): ?>
                    <span class="error-message"><?= errors('password') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation">
            </div>

            <div class="form-group">
                <label for="age">Age</label>
                <input
                    type="number"
                    id="age"
                    name="age"
                    value="<?= old('age') ?>"
                    class="<?= hasError('age') ? 'error' : '' ?>">
                <?php if (hasError('age')): ?>
                    <span class="error-message"><?= errors('age') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="<?= hasError('role') ? 'error' : '' ?>">
                    <option value="">Select Role</option>
                    <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= old('role') === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="guest" <?= old('role') === 'guest' ? 'selected' : '' ?>>Guest</option>
                </select>
                <?php if (hasError('role')): ?>
                    <span class="error-message"><?= errors('role') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="<?= route('users') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>