<h1>Stores</h1>
<div class="actions-btn">
    <?php if ($this->currentUser->role === 'super_admin'): ?>
        <a class="a-btn" href="/stores/create">+ New Store</a>
    <?php endif; ?>
    <a href="/weapons">View Weapons</a> 
    <a class="btn" href="/stores/pdf" target="_blank">Print List in PDF</a>
</div>

<table id="stores-table">
    <?= Core\ViewHelper::renderFilterForm($meta, '', ['city' => array_column($stores, 'city', 'city'), 'country' => array_column($stores, 'country', 'country'), 'state_region' => array_column($stores, 'state_region', 'state_region')]) ?>
    <thead>
        <tr>
            <th>#</th>
            <th class="sortable" data-column="name"><?= Core\ViewHelper::sortLink($meta, 'name','Name') ?></th>
            <th class="sortable" data-column="slug"><?= Core\ViewHelper::sortLink($meta, 'slug','Slug') ?></th>
            <th class="sortable" data-column="address_line1"><?= Core\ViewHelper::sortLink($meta, 'address_line1','Address') ?></th>
            <th class="sortable" data-column="city"><?= Core\ViewHelper::sortLink($meta, 'city','City') ?></th>
            <th class="sortable" data-column="state_region"><?= Core\ViewHelper::sortLink($meta, 'state_region','State/Region') ?></th>
            <th class="sortable" data-column="country"><?= Core\ViewHelper::sortLink($meta, 'country','Country') ?></th>
            <th class="sortable" data-column="phone"><?= Core\ViewHelper::sortLink($meta, 'phone','Phone') ?></th>
            <th class="sortable" data-column="email"><?= Core\ViewHelper::sortLink($meta, 'email','Email') ?></th>
            <th class="sortable" data-column="total_weapons"><?= Core\ViewHelper::sortLink($meta, 'total_weapons','Total Weapons') ?></th>
            <?php if ($this->currentUser->role === 'super_admin'): ?>
                <th>Actions</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stores as $store): ?>
            <tr>
                <td><?= $store->id ?></td>
                <td><a href="/stores/show/<?= $store->id ?>"><?= htmlspecialchars($store->name) ?></a></td>
                <td><?= htmlspecialchars($store->slug) ?></td>
                <td><?= htmlspecialchars($store->address_line1) ?></td>
                <td><?= htmlspecialchars($store->city) ?></td>
                <td><?= htmlspecialchars($store->state_region) ?></td>
                <td><?= htmlspecialchars($store->country) ?></td>
                <td><?= htmlspecialchars($store->phone) ?></td>
                <td><?= htmlspecialchars($store->email) ?></td>
                <td><?= $totalWeapons[$store->id] ?? 0 ?></td>
                <?php if ($this->currentUser->role === 'super_admin'): ?>
                    <td>
                        <a href="/stores/edit/<?= $store->id ?>" title="Edit">
                            <span class="icon-edit" aria-hidden="true">&#9998;</span>
                        </a> |  
                        <a href="/stores/show/<?= $store->id ?>" title="View">
                            <span class="icon-view" aria-hidden="true">&#128065;</span>
                        </a> | 
                        <a class="btn" href="/stores/pdf/<?= $store->id ?>" target="_blank" title="Print PDF">
                            <span class="icon-print" aria-hidden="true">&#128424;</span>
                        </a> | 
                        <form method="post" action="/stores/delete/<?= $store->id ?>" style="display:inline;">
                            <?= $this->csrfField() ?>
                            <button class="btn-delete" type="submit" title="Delete">
                                <span class="icon-delete" aria-hidden="true">&#128465;</span>
                            </button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= Core\ViewHelper::renderPagination($meta) ?>