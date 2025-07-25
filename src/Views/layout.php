<!-- src/Views/layout.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Home' ?> | Weapons Store App</title>
    <link rel="stylesheet" href="./assets/styles.css?v=1.1">
    <style>
       body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
        }

        nav {
            background-color: #fff;
            padding: 16px 24px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 24px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        nav a {
            font-size: 16px;
            color: #555;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }

        nav a:hover {
            background-color: #e8e8f8;
            color: #3c3cbb;
        }

        nav a.active {
            font-weight: 600;
            background-color: #3c3cbb;
            color: #fff;
        }

        .page-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        h1 {
            margin-top: 0;
            color: #333;
            font-size: 28px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f0f0f5;
            font-weight: bold;
        }

        form.inline {
            display: inline;
        }
    </style>
</head>
<body>

<nav>
    <a href="/store.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'store.php') !== false ? 'active' : '' ?>">Stores</a>
    <a href="/weapon.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'weapon.php') !== false ? 'active' : '' ?>">Weapon</a>
</nav>

<div class="page-container">
<?php if (!empty($title)): ?>
    <h1><?= htmlspecialchars($title) ?></h1>
<?php endif; ?>

<?= $content ?>
</div>

</body>
</html>
