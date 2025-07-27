<h1>Weapons</h1>
<div class="actions-btn">
    <a href="/weapons/create">+ New Weapon</a>
    <a href="/stores">View Stores</a>
    <a class="btn" href="/weapons/pdf" target="_blank">Print List in PDF</a>
</div>
<table id="weapons-table">
    <?= Core\ViewHelper::renderFilterForm($meta, '', ['type' => array_column($weapons, 'type', 'type'), 'status' => array_column($weapons, 'status', 'status')]) ?>
    <thead>
        <tr>
            <th>#</th>
            <th class="sortable" data-column="name"><?= Core\ViewHelper::sortLink($meta, 'name','Name') ?></th>
            <th class="sortable" data-column="type"><?= Core\ViewHelper::sortLink($meta, 'type','Type') ?></th>
            <th class="sortable" data-column="caliber"><?= Core\ViewHelper::sortLink($meta, 'caliber','Caliber') ?></th>
            <th class="sortable" data-column="serial_number"><?= Core\ViewHelper::sortLink($meta, 'serial_number','Serial Number') ?></th>
            <th class="sortable" data-column="price"><?= Core\ViewHelper::sortLink($meta, 'price','Price') ?></th>
            <th class="sortable" data-column="in_stock"><?= Core\ViewHelper::sortLink($meta, 'in_stock', 'In Stock') ?></th>
            <th class="sortable" data-column="status"><?= Core\ViewHelper::sortLink($meta, 'status', 'Status') ?></th>
            <th class="sortable" data-column="store"><?= Core\ViewHelper::sortLink($meta, 'store_id', 'Store') ?></th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($weapons as $w): ?>
            <tr>
                <td><?= $w->id ?></td>
                <td data-label="Name"><a href="/weapons/show/<?= $w->id ?>"><?= htmlspecialchars($w->name) ?></a></td>
                <td><?= htmlspecialchars($w->type) ?></td>
                <td><?= htmlspecialchars($w->caliber) ?></td>
                <td><?= htmlspecialchars($w->serial_number) ?></td>
                <td><?= htmlspecialchars($w->price) ?></td>
                <td><?= htmlspecialchars($w->in_stock) ?></td>
                <td><?= htmlspecialchars($w->status) ?></td>
                <td>
                    <a href="/stores/show/<?= $w->store_id ?>"><?= isset($stores[$w->store_id]) ? htmlspecialchars($stores[$w->store_id]->name) : '&mdash;' ?></a>
                </td>
                <td>
                    <a href="/weapons/edit/<?= $w->id ?>" title="Edit">
                        <span class="icon-edit" aria-hidden="true">&#9998;</span>
                    </a> | 
                    <a href="/weapons/show/<?= $w->id ?>" title="View">
                        <span class="icon-view" aria-hidden="true">&#128065;</span>
                    </a> |
                    <a class="btn" href="/weapons/pdf/<?= $w->id ?>" target="_blank" title="Print PDF">
                        <span class="icon-print" aria-hidden="true">&#128424;</span>
                    </a> |
                    <form method="post" action="/weapons/delete/<?= $w->id ?>" style="display:inline;">
                        <?= $this->csrfField() ?>
                        <button class="btn-delete" type="submit" title="Delete">
                            <span class="icon-delete" aria-hidden="true">&#128465;</span>
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= Core\ViewHelper::renderPagination($meta) ?>