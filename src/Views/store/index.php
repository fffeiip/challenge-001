<?php ob_start(); session_start();?>
<?php  use App\Helpers\SortHelper; ?>


<?php if (!empty($_SESSION['success'])): ?>
    <div style="padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 15px; border-radius: 5px;">
        <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

    <div class="action-bar">
        <form method="get" action="store.php">
            <input type="text" name="filter" placeholder="Search..." value="<?= htmlspecialchars($filter) ?>">
            <button type="submit">Filter</button>
            <?php if (!empty($filter)): ?>
                <a href="store.php" class="btn clear">Clear</a>
            <?php endif; ?>
        </form>
        <a class="btn" href="store.php?action=create">+ Add New Store</a>
    </div>
    <p class="store-count">
        Showing <?= count($stores) ?> of <?= $total ?> stores<?= $filter ? " matching \"$filter\"" : '' ?>.
    </p>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=name&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Name<?= SortHelper::sortArrow('name', $sort, $order) ?></a></th>
                <th><a href="?sort=email&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Email<?= SortHelper::sortArrow('email', $sort, $order) ?></a></th>
                <th><a href="?sort=phone&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Phone<?= SortHelper::sortArrow('phone', $sort, $order) ?></a></th>
                <th><a href="?sort=city&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">City<?= SortHelper::sortArrow('city', $sort, $order) ?></a></th>
                <th><a href="?sort=state_region&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">State<?= SortHelper::sortArrow('state_region', $sort, $order) ?></a></th>
                <th><a href="?sort=country&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Country<?= SortHelper::sortArrow('country', $sort, $order) ?></a></th>
                <th><a href="?sort=created_at&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Created<?= SortHelper::sortArrow('created_at', $sort, $order) ?></a></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($stores)): ?>
                <tr><td colspan="8">No stores found.</td></tr>
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
                        <td class="actions">
                            <a title="View Store" class="btn view" href="store.php?action=show&id=<?= $store['id'] ?>">&#128065;</a>
                            <a title="Edit Store" class="btn edit" href="store.php?action=edit&id=<?= $store['id'] ?>">&#9998;</a>
                            <form method="post" action="store.php?action=delete&id=<?= $store['id'] ?>" class="inline" onsubmit="return confirm('Delete this store?')">
                                <button title="Delete Store" class="btn delete" type="submit">&#10006;</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php
$totalPages = max(1, ceil($total / $perPage));
?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&filter=<?= urlencode($filter) ?>">« Prev</a>
    <?php else: ?>
        <span class="disabled">« Prev</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong><?= $i ?></strong>
        <?php else: ?>
            <a href="?page=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>&filter=<?= urlencode($filter) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&sort=<?= $sort ?>&order=<?= $order ?>&filter=<?= urlencode($filter) ?>">Next »</a>
    <?php else: ?>
        <span class="disabled">Next »</span>
    <?php endif; ?>
</div>


<?php
$content = ob_get_clean();
$title = "Stores";
include __DIR__ . '/../layout.php';
