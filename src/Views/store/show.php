<?php ob_start(); ?>

<p><strong>Name:</strong> <?= htmlspecialchars($store['name']) ?></p>
<p><strong>Slug:</strong> <?= htmlspecialchars($store['slug']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($store['email']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($store['phone']) ?></p>
<p><strong>Address Line 1:</strong> <?= htmlspecialchars($store['address_line1']) ?></p>
<p><strong>Address Line 2:</strong> <?= htmlspecialchars($store['address_line2']) ?></p>
<p><strong>City:</strong> <?= htmlspecialchars($store['city']) ?></p>
<p><strong>State/Region:</strong> <?= htmlspecialchars($store['state_region']) ?></p>
<p><strong>Country:</strong> <?= htmlspecialchars($store['country']) ?></p>
<p><strong>Created At:</strong> <?= date('F j, Y', strtotime($store['created_at'])) ?></p>

<h3>Weapons Available:</h3>

<?php if (empty($store['weapons'])): ?>
    <p>No weapons available.</p>
<?php else: ?>
    <ul>
        <?php foreach ($store['weapons'] as $weapon): ?>
            <li>
                <?= htmlspecialchars($weapon['name']) ?>
                (<?= htmlspecialchars($weapon['type']) ?>)
                
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<p><a href="store.php">Back to Stores</a></p>
<?php
$content = ob_get_clean();
$title = "Store Detail";
include __DIR__ . '/../layout.php';
