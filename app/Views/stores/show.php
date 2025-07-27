<h1>Store: <?= htmlspecialchars($store->name) ?></h1>

<p>
  <strong>Address:</strong>
  <?= htmlspecialchars($store->address_line1) ?>,<br>
  <?= htmlspecialchars($store->city) ?>, <?= htmlspecialchars($store->state_region) ?>,
  <?= htmlspecialchars($store->country) ?>
</p>
<p>
  <strong>Phone:</strong> <?= htmlspecialchars($store->phone) ?><br>
  <strong>Email:</strong> <?= htmlspecialchars($store->email) ?>
</p>
<p>
  <a class="a-btn" href="/stores/pdf/<?= $store->id ?>" target="_blank">Print PDF</a> 
</p>

<h2>Weapons in this Store</h2>
<?php if (count($weapons) === 0): ?>
  <p>No weapons found.</p>
<?php else: ?>
  <table id="store-weapons-table">
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
<?php endif; ?>
