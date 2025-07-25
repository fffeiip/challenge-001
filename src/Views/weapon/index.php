<?php ob_start(); session_start();?>
<?php  use App\Helpers\SortHelper; ?>


<?php if (!empty($_SESSION['success'])): ?>
    <div style="padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; margin-bottom: 15px; border-radius: 5px;">
        <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

    <div class="action-bar">
        <form method="get" action="weapon.php">
            <input type="text" name="filter" placeholder="Search..." value="<?= htmlspecialchars($filter) ?>">

            <select name="status">
                <option value="">-- Filter by Status --</option>
                <option value="active" <?= isset($_GET['status']) && $_GET['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="discontinued" <?= isset($_GET['status']) && $_GET['status'] === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
                <option value="out_of_stock" <?= isset($_GET['status']) && $_GET['status'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>

            <button type="submit">Filter</button>

            <?php if (!empty($filter) || !empty($_GET['status'])): ?>
                <a href="weapon.php" class="btn clear">Clear</a>
            <?php endif; ?>
        </form>

        <a class="btn" href="weapon.php?action=create">+ Add New Weapon</a>
    </div>

    <p class="weapon-count">
        Showing <?= count($weapons) ?> of <?= $total ?> weapons<?= $filter ? " matching \"$filter\"" : '' ?>.
    </p>
    <table>
        <thead>
            <tr>
                <th><a href="?sort=name&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Name<?= SortHelper::sortArrow('name', $sort, $order) ?></a></th>
                <th><a href="?sort=type&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Type<?= SortHelper::sortArrow('type', $sort, $order) ?></a></th>
                <th><a href="?sort=caliber&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Caliber<?= SortHelper::sortArrow('caliber', $sort, $order) ?></a></th>
                <th><a href="?sort=serial_number&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Serial no<?= SortHelper::sortArrow('serial_number', $sort, $order) ?></a></th>
                <th><a href="?sort=price&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Price<?= SortHelper::sortArrow('price', $sort, $order) ?></a></th>
                <th><a href="?sort=status&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Status<?= SortHelper::sortArrow('status', $sort, $order) ?></a></th>
                <th><a href="?sort=created_at&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>&filter=<?= urlencode($filter) ?>">Created<?= SortHelper::sortArrow('created_at', $sort, $order) ?></a></th>
                <th>Store</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($weapons)): ?>
                <tr><td colspan="9">No weapons found.</td></tr>
            <?php else: ?>
                <?php foreach ($weapons as $weapon): ?>
                    <tr>
                        <td><?= htmlspecialchars($weapon['name']) ?></td>
                        <td><?= htmlspecialchars($weapon['type']) ?></td>
                        <td><?= htmlspecialchars($weapon['caliber']) ?></td>
                        <td><?= htmlspecialchars($weapon['serial_number']) ?></td>
                        <td><?= htmlspecialchars($weapon['price']) ?></td>
                        <td><?= htmlspecialchars($weapon['status']) ?></td>
                        <td><?= date('F j, Y', strtotime($weapon['created_at'])) ?></td>
                        <td> <a href="store.php?action=show&id=<?= $weapon['store_id'] ?>"><?= htmlspecialchars($weapon['store_name']) ?></a></td>
                        <td class="actions">
                            <a  class="btn view" href="weapon.php?action=show&id=<?= $weapon['id'] ?>">View</a>
                            <a  class="btn edit" href="weapon.php?action=edit&id=<?= $weapon['id'] ?>">Edit</a> 
                            <form method="post" action="weapon.php?action=delete&id=<?= $weapon['id'] ?>" class="inline" onsubmit="return confirm('Delete this weapon?')">
                                <button class="btn delete" type="submit">Delete</button>
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
$title = "Weapons";
include __DIR__ . '/../layout.php';
