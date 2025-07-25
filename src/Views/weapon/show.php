<?php ob_start(); ?>

<a href="weapon.php" class="btn-back">&larr; Back</a>

<div class="store-detail-container">
    <!-- Left Column: Store Info -->
    <div class="store-info">
        <div class="store-detail-card">
            <h2><?= htmlspecialchars($weapon['name']) ?></h2>

            <div class="meta">
                <p><strong>Type:</strong> <?= htmlspecialchars($weapon['type']) ?></p>
                <p><strong>Caliber:</strong> <?= htmlspecialchars($weapon['caliber']) ?></p>
                <p><strong>Serial Number:</strong> <?= htmlspecialchars($weapon['serial_number']) ?></p>
            </div>

            <div class="address">
                <h3>Stock</h3>
                <p><strong>In stock:</strong> <?= htmlspecialchars($weapon['in_stock']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($weapon['status']) ?></p>
                <p><strong>Price:</strong> <?= htmlspecialchars($weapon['price']) ?></p>
            </div>

            <p class="created-at"><strong>Created At:</strong> <?= date('F j, Y', strtotime($weapon['created_at'])) ?></p>
        </div>
    </div>

    <!-- Right Column: Weapons -->
    <div class="weapon-info">
        <div class="store-detail-card">
            <h3>Store</h3>
            <?php if (empty($weapon['store_id'])): ?>
                <p>No store available.</p>
            <?php else: ?>
                <ul class="weapon-list">
                     <li>
                        <a href="store.php?action=show&id=<?= $weapon['store_id'] ?>">
                            <?= htmlspecialchars($weapon['store_name']) ?>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Weapon Detail";
include __DIR__ . '/../layout.php';
