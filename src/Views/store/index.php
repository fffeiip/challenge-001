<?php ob_start(); ?>

    <form method="get" action="store.php">
        <input type="text" name="filter" placeholder="Search..." value="<?= htmlspecialchars($filter) ?>">
        <button type="submit">Filter</button>
    </form>

    <a href="store.php?action=create">+ Add New Store</a>

    <table>
        <thead>
            <tr>
                <th><a href="?sort=name&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Name</a></th>
                <th><a href="?sort=email&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Email</a></th>
                <th><a href="?sort=phone&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Phone</a></th>
                <th><a href="?sort=city&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">City</a></th>
                <th><a href="?sort=state_region&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">State</a></th>
                <th><a href="?sort=country&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Country</a></th>
                <th><a href="?sort=created_at&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Created</a></th>
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
                            <a href="store.php?action=show&id=<?= $store['id'] ?>">View</a> | 
                            <a href="store.php?action=edit&id=<?= $store['id'] ?>">Edit</a> | 
                            <form method="post" action="store.php?action=delete&id=<?= $store['id'] ?>" class="inline" onsubmit="return confirm('Delete this store?')">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php
$totalPages = ceil($total / $perPage);
if ($totalPages > 1): ?>
    <div>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <strong><?= $i ?></strong>
            <?php else: ?>
                <a href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&filter=<?= urlencode($filter) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$title = "Stores";
include __DIR__ . '/../layout.php';
