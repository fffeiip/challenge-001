<?php
// Helper function to generate sort URLs for weapons
function getWeaponSortUrl($column, $current_sort_by, $current_sort_dir, $current_params, $store_id) {
    $params = $current_params;
    $params['sort_by'] = $column;
    
    // Toggle direction if same column, otherwise default to ASC
    if ($current_sort_by === $column) {
        $params['sort_dir'] = ($current_sort_dir === 'ASC') ? 'DESC' : 'ASC';
    } else {
        $params['sort_dir'] = 'ASC';
    }
    
    return '/stores/show/' . $store_id . '?' . http_build_query($params);
}

// Helper function to get sort indicator for weapons
function getWeaponSortIndicator($column, $current_sort_by, $current_sort_dir) {
    if ($current_sort_by !== $column) {
        return ' <span class="sort-indicator">⇅</span>';
    }
    return $current_sort_dir === 'ASC' ? ' <span class="sort-indicator active">↑</span>' : ' <span class="sort-indicator active">↓</span>';
}

// Helper function to generate pagination URL for weapons
function getWeaponPaginationUrl($page, $current_params, $store_id) {
    $params = $current_params;
    $params['page'] = $page;
    return '/stores/show/' . $store_id . '?' . http_build_query($params);
}
?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <a href="/stores" class="btn btn-secondary">Back to All Stores</a>
</div>

<h3>Store Information</h3>
<table class="table-data">
    <tr>
        <th>ID</th>
        <td><?= e($store['id']) ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <td><?= e($store['name']) ?></td>
    </tr>
    <tr>
        <th>Address</th>
        <td>
            <?= e($store['address_line1']) ?><br>
            <?php if (!empty($store['address_line2'])): ?>
                <?= e($store['address_line2']) ?><br>
            <?php endif; ?>
            <?= e($store['city']) ?>, <?= e($store['state_region']) ?><br>
            <?= e($store['country']) ?>
        </td>
    </tr>
    <tr>
        <th>Contact</th>
        <td>Phone: <?= e($store['phone'] ?? 'N/A') ?><br>Email: <?= e($store['email'] ?? 'N/A') ?></td>
    </tr>
</table>

<h3 style="margin-top: 2rem;">Weapons at this Store</h3>

<!-- Weapon Filters Section -->
<div class="filters-section">
    <form method="GET" action="/stores/show/<?= e($store['id']) ?>" class="filters-form">
        <!-- Preserve sorting parameters -->
        <input type="hidden" name="sort_by" value="<?= e($weapon_sorting['sort_by']) ?>">
        <input type="hidden" name="sort_dir" value="<?= e($weapon_sorting['sort_dir']) ?>">
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="search">Search:</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="<?= e($weapon_filters['search'] ?? '') ?>" 
                       placeholder="Search weapons..."
                       class="form-control">
            </div>
            
            <div class="filter-group">
                <label for="type">Type:</label>
                <select id="type" name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($weapon_filter_options['types'] as $type): ?>
                        <option value="<?= e($type) ?>" <?= ($weapon_filters['type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= e($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="caliber">Caliber:</label>
                <select id="caliber" name="caliber" class="form-control">
                    <option value="">All Calibers</option>
                    <?php foreach ($weapon_filter_options['calibers'] as $caliber): ?>
                        <option value="<?= e($caliber) ?>" <?= ($weapon_filters['caliber'] ?? '') === $caliber ? 'selected' : '' ?>>
                            <?= e($caliber) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="in_stock">Stock:</label>
                <select id="in_stock" name="in_stock" class="form-control">
                    <option value="">All Stock Levels</option>
                    <option value="available" <?= ($weapon_filters['in_stock'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="out_of_stock" <?= ($weapon_filters['in_stock'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="price_min">Price Range:</label>
                <div style="display: flex; gap: 5px;">
                    <input type="number" 
                           id="price_min" 
                           name="price_min" 
                           value="<?= e($weapon_filters['price_min'] ?? '') ?>" 
                           placeholder="Min"
                           step="0.01"
                           class="form-control">
                    <input type="number" 
                           id="price_max" 
                           name="price_max" 
                           value="<?= e($weapon_filters['price_max'] ?? '') ?>" 
                           placeholder="Max"
                           step="0.01"
                           class="form-control">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Apply Filters</button>
                <a href="/stores/show/<?= e($store['id']) ?>" class="btn btn-outline">Clear All</a>
            </div>
        </div>
    </form>
</div>

<!-- Weapon Results Info -->
<div class="results-info">
    <p>
        Showing <?= $weapon_pagination['total'] > 0 ? (($weapon_pagination['current_page'] - 1) * $weapon_pagination['per_page'] + 1) : 0 ?> 
        to <?= min($weapon_pagination['current_page'] * $weapon_pagination['per_page'], $weapon_pagination['total']) ?> 
        of <?= $weapon_pagination['total'] ?> weapons
    </p>
</div>

<?php if (empty($weapons)): ?>
    <div class="alert alert-info">This store has no weapons matching your criteria.</div>
<?php else: ?>
    <!-- Weapons Data Table -->
    <table class="table-data">
        <thead>
            <tr>
                <th>
                    <a href="<?= getWeaponSortUrl('name', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir'], $weapon_filters, $store['id']) ?>" class="sort-link">
                        Name<?= getWeaponSortIndicator('name', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getWeaponSortUrl('type', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir'], $weapon_filters, $store['id']) ?>" class="sort-link">
                        Type<?= getWeaponSortIndicator('type', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getWeaponSortUrl('caliber', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir'], $weapon_filters, $store['id']) ?>" class="sort-link">
                        Caliber<?= getWeaponSortIndicator('caliber', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getWeaponSortUrl('price', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir'], $weapon_filters, $store['id']) ?>" class="sort-link">
                        Price<?= getWeaponSortIndicator('price', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getWeaponSortUrl('in_stock', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir'], $weapon_filters, $store['id']) ?>" class="sort-link">
                        Stock<?= getWeaponSortIndicator('in_stock', $weapon_sorting['sort_by'], $weapon_sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weapons as $weapon): ?>
                <tr>
                    <td>
                        <strong>
                            <a href="/weapons/edit/<?= e($weapon['id']) ?>">
                                <?= e($weapon['name']) ?>
                            </a>
                        </strong>
                    </td>
                    <td><?= e($weapon['type']) ?></td>
                    <td><?= e($weapon['caliber']) ?></td>
                    <td>$<?= e(number_format($weapon['price'], 2)) ?></td>
                    <td>
                        <span class="<?= $weapon['in_stock'] > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= e($weapon['in_stock']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="/weapons/edit/<?= e($weapon['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Weapon Pagination -->
    <?php if ($weapon_pagination['total_pages'] > 1): ?>
        <div class="pagination-wrapper">
            <nav class="pagination">
                <!-- Previous page -->
                <?php if ($weapon_pagination['has_prev']): ?>
                    <a href="<?= getWeaponPaginationUrl(1, $weapon_filters, $store['id']) ?>" class="page-link">First</a>
                    <a href="<?= getWeaponPaginationUrl($weapon_pagination['current_page'] - 1, $weapon_filters, $store['id']) ?>" class="page-link">Previous</a>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php
                $start = max(1, $weapon_pagination['current_page'] - 2);
                $end = min($weapon_pagination['total_pages'], $weapon_pagination['current_page'] + 2);
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $weapon_pagination['current_page']): ?>
                        <span class="page-link current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= getWeaponPaginationUrl($i, $weapon_filters, $store['id']) ?>" class="page-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next page -->
                <?php if ($weapon_pagination['has_next']): ?>
                    <a href="<?= getWeaponPaginationUrl($weapon_pagination['current_page'] + 1, $weapon_filters, $store['id']) ?>" class="page-link">Next</a>
                    <a href="<?= getWeaponPaginationUrl($weapon_pagination['total_pages'], $weapon_filters, $store['id']) ?>" class="page-link">Last</a>
                <?php endif; ?>
            </nav>
            
            <!-- Per page selector -->
            <div class="per-page-selector">
                <form method="GET" action="/stores/show/<?= e($store['id']) ?>" class="per-page-form">
                    <!-- Preserve current parameters -->
                    <?php foreach ($weapon_filters as $key => $value): ?>
                        <?php if ($key !== 'per_page' && $key !== 'page' && !empty($value)): ?>
                            <input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input type="hidden" name="sort_by" value="<?= e($weapon_sorting['sort_by']) ?>">
                    <input type="hidden" name="sort_dir" value="<?= e($weapon_sorting['sort_dir']) ?>">
                    
                    <label for="per_page">Show:</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $weapon_pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $weapon_pagination['per_page'] == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $weapon_pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $weapon_pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <span>per page</span>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>