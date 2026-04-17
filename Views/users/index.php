<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - PHP MVC Framework</title>
    <link rel="stylesheet" href="<?= asset('styles/main.css') ?>">
</head>

<body>
    <div class="container">
        <h1>Users</h1>

        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-error">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="<?= route('users.create') ?>" class="btn btn-primary">Create New User</a>
        </div>

        <table class="table" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No users found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user->id) ?></td>
                            <td><?= e($user->name) ?></td>
                            <td><?= e($user->email) ?></td>
                            <td><?= e($user->role) ?></td>
                            <td>
                                <a href="<?= route('users.show', ['id' => $user->id]) ?>">View</a>
                                <a href="<?= route('users.edit', ['id' => $user->id]) ?>">Edit</a>

                                <form method="POST" action="<?= route('users.destroy', ['id' => $user->id]) ?>" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <?= method_field('DELETE') ?>
                                    <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>