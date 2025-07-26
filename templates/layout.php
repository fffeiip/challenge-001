<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Stores & Weapons') ?></title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>
    <header class="main-header">
        <nav class="main-nav">
            <a href="/stores/index" class="nav-brand">S&W Mgmt</a>
            <ul>
                <li><a href="/stores/index">Stores</a></li>
                <li><a href="/weapons/list">Weapons</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php if (isset($flashMessage)): ?>
            <div class="flash-message flash-<?= e($flashMessage['type']) ?>">
                <?= e($flashMessage['message']) ?>
            </div>
        <?php endif; ?>
        <?php
            // This is where the specific page's content will be included
            require PROJECT_ROOT . "/templates/{$view}.php";
        ?>
    </main>

    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> Stores & Weapons Management System</p>
    </footer>

    <script src="/js/main.js"></script>
</body>
</html>

