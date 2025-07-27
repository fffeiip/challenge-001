<h1>User: <?= htmlspecialchars($user->username) ?></h1>
<p><strong>Role:</strong> <?= htmlspecialchars($user->role) ?></p>
<?php if ($user->store_id): ?>
  <p>
    <strong>Store:</strong>
    <a href="/stores/show/<?= $user->store_id ?>">
      <?= htmlspecialchars((new App\Models\Store())->find($user->store_id)->name) ?>
    </a>
  </p>
<?php endif; ?>

<?php if (!empty($weapons)): ?>
  <h2>Weapons Available to This User</h2>
  <table id="user-weapons-table">
    <thead>
      <tr>
        <th class="sortable">ID</th>
        <th class="sortable">Name</th>
        <th class="sortable">Type</th>
        <th class="sortable">Caliber</th>
        <th class="sortable">Serial Number</th>
        <th class="sortable">Price</th>
        <th class="sortable">In Stock</th>
        <th class="sortable">Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($weapons as $w): ?>
      <tr>
        <td data-label="ID"><?= $w->id ?></td>
        <td data-label="Name">
          <a href="/weapons/show/<?= $w->id ?>"><?= htmlspecialchars($w->name) ?></a>

        </td>
        <td data-label="Type"><?= htmlspecialchars($w->type) ?></td>
        <td data-label="Caliber"><?= htmlspecialchars($w->caliber) ?></td>
        <td data-label="Serial Number"><?= htmlspecialchars($w->serial_number) ?></td>
        <td data-label="Price"><?= htmlspecialchars($w->price) ?></td>
        <td data-label="In Stock"><?= htmlspecialchars($w->in_stock) ?></td>
        <td data-label="Status"><?= htmlspecialchars($w->status) ?></td>
        <td data-label="Actions">
            <a href="/weapons/edit/<?= $w->id ?>" title="Edit">
                <span class="icon-edit" aria-hidden="true">&#9998;</span>
            </a> | 
            <a href="/weapons/show/<?= $w->id ?>" title="View">
                <span class="icon-view" aria-hidden="true">&#128065;</span>
            </a> | 
            <a href="/weapons/pdf/<?= $w->id ?>" target="_blank" title="Print PDF">
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
