<?php ob_start(); ?>

    <a href="store.php?action=create">+ Add New Store</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($stores)): ?>
                <tr><td colspan="3">No stores found.</td></tr>
            <?php else: ?>
                <?php foreach ($stores as $store): ?>
                    <tr>
                        <td><?= htmlspecialchars($store['name']) ?></td>
                        <td><?= htmlspecialchars($store['email']) ?></td>
                        <td><?= htmlspecialchars($store['phone']) ?></td>
                        <td><?= htmlspecialchars($store['city']) ?></td>
                        <td><?= htmlspecialchars($store['state_region']) ?></td>
                        <td><?= htmlspecialchars($store['country']) ?></td>
                        <td><?= date('F j, Y', strtotime($store['created_at'])) ?></td>
                        <td>
                            <a href="store.php?action=show&id=<?= $store['id'] ?>">View</a> | Edit | Delete
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php
$content = ob_get_clean();
$title = "Stores";
include __DIR__ . '/../layout.php';
