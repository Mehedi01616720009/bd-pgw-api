<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="<?= asset('styles/main.css') ?>">
</head>

<body>
    <div class="container">
        <h1>Edit User</h1>

        <form method="POST" action="<?= route('users.update', ['id' => $user->id]) ?>">
            <?= csrf_field() ?>
            <?= method_field('PUT') ?>

            <div class="form-group">
                <label for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?= old('name', $user->name) ?>"
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
                    value="<?= old('email', $user->email) ?>"
                    class="<?= hasError('email') ? 'error' : '' ?>">
                <?php if (hasError('email')): ?>
                    <span class="error-message"><?= errors('email') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="<?= hasError('role') ? 'error' : '' ?>">
                    <option value="">Select Role</option>
                    <option value="admin" <?= old('role', $user->role) === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="user" <?= old('role', $user->role) === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="guest" <?= old('role', $user->role) === 'guest' ? 'selected' : '' ?>>Guest</option>
                </select>
                <?php if (hasError('role')): ?>
                    <span class="error-message"><?= errors('role') ?></span>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="<?= route('users.index') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>

</html>