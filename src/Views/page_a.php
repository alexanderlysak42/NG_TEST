<?php
/** @var array $registration */
/** @var string $csrfToken */
/** @var array|null $lastResult */
/** @var array|null $history */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Page A</title>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($registration['username']) ?></h1>
<p>Link active until: <?= htmlspecialchars($registration['expires_at']) ?></p>

<form method="post" action="/p/<?= htmlspecialchars($registration['token']) ?>/regenerate" style="display:inline">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <button type="submit">Regenerate link</button>
</form>

<form method="post" action="/p/<?= htmlspecialchars($registration['token']) ?>/deactivate" style="display:inline">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <button type="submit">Deactivate</button>
</form>

<form method="post" action="/p/<?= htmlspecialchars($registration['token']) ?>/play" style="display:inline">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    <button type="submit">Imfeelinglucky</button>
</form>

<form method="get" action="/p/<?= htmlspecialchars($registration['token']) ?>/history" style="display:inline">
    <button type="submit">History</button>
</form>

<?php if ($lastResult !== null): ?>
    <h2>Result</h2>
    <p>Number: <?= (int) $lastResult['number'] ?></p>
    <p>Result: <?= htmlspecialchars(ucfirst($lastResult['result'])) ?></p>
    <p>Win amount: <?= number_format((float) $lastResult['amount'], 2) ?></p>
<?php endif; ?>

<?php if ($history !== null): ?>
    <h2>History (last 3)</h2>
    <ul>
        <?php foreach ($history as $item): ?>
            <li>
                <?= htmlspecialchars($item['created_at']) ?> —
                number <?= (int) $item['number'] ?>,
                <?= htmlspecialchars(ucfirst($item['result'])) ?>,
                win amount <?= number_format((float) $item['amount'], 2) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</body>
</html>