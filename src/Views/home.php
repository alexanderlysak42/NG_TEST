<?php
/** @var string $csrfToken */
/** @var array $errors */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Registration</title>
</head>
<body>
<h1>Registration</h1>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="/register">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

    <label>
        Username
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    </label>

    <label>
        Phone number
        <input type="text" name="phone_number" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" required>
    </label>

    <button type="submit">Register</button>
</form>
</body>
</html>