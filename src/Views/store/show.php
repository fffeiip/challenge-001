<?php ob_start(); ?>

<a href="store.php" class="btn-back">&larr; Back</a>

<div class="store-detail-container">
    <!-- Left Column: Store Info -->
    <div class="store-info">
        <div class="store-detail-card">
            <h2><?= htmlspecialchars($store['name']) ?></h2>

            <div class="meta">
                <p><strong>Slug:</strong> <?= htmlspecialchars($store['slug']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($store['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($store['phone']) ?></p>
            </div>

            <div class="address">
                <h3>Address</h3>
                <p><strong>Line 1:</strong> <?= htmlspecialchars($store['address_line1']) ?></p>
                <p><strong>Line 2:</strong> <?= htmlspecialchars($store['address_line2']) ?></p>
                <p><strong>City:</strong> <?= htmlspecialchars($store['city']) ?></p>
                <p><strong>State/Region:</strong> <?= htmlspecialchars($store['state_region']) ?></p>
                <p><strong>Country:</strong> <?= htmlspecialchars($store['country']) ?></p>
            </div>

            <p class="created-at"><strong>Created At:</strong> <?= date('F j, Y', strtotime($store['created_at'])) ?></p>
        </div>
    </div>

    <!-- Right Column: Weapons -->
    <div class="weapon-info">
        <div class="store-detail-card">
            <h3>Weapons Available</h3>
            <?php if (empty($store['weapons'])): ?>
                <p>No weapons available.</p>
            <?php else: ?>
                <ul class="weapon-list">
                    <?php foreach ($store['weapons'] as $weapon): ?>
                        <li>
                            <?php if ($weapon['deleted_at']): ?>
                                <span class="deleted">[Deleted]</span>
                            <?php endif; ?>
                            <a href="weapon.php?action=show&id=<?= $weapon['id'] ?>">
                                <?= htmlspecialchars($weapon['name']) ?>
                            </a>
                            <span class="weapon-type">(<?= htmlspecialchars($weapon['type']) ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Store Detail";
include __DIR__ . '/../layout.php';
