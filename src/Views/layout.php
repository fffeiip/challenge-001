<!-- src/Views/layout.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Home' ?> | Weapons Store App</title>
    <link rel="stylesheet" href="./assets/styles.css?v=1.0">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        nav a { margin-right: 10px; text-decoration: none; }
        nav a.active { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th a { text-decoration: none; color: inherit; }
        form.inline { display: inline; }
    </style>
</head>
<body>

<nav>
    <a href="/store.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'store.php') !== false ? 'active' : '' ?>">Stores</a>
    <a href="/weapon.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'weapon.php') !== false ? 'active' : '' ?>">Weapon</a>
</nav>

<hr>
<div class="page-container">
<?php if (!empty($title)): ?>
    <h1><?= htmlspecialchars($title) ?></h1>
<?php endif; ?>

<?= $content ?>
</div>

</body>
</html>
