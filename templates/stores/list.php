<?php

// Helper function to generate sort URLs
function getSortUrl($column, $current_sort_by, $current_sort_dir, $current_params) {
    $params = $current_params;
    $params['sort_by'] = $column;
    
    // Toggle direction if same column, otherwise default to ASC
    if ($current_sort_by === $column) {
        $params['sort_dir'] = ($current_sort_dir === 'ASC') ? 'DESC' : 'ASC';
    } else {
        $params['sort_dir'] = 'ASC';
    }
    
    return '/stores?' . http_build_query($params);
}

// Helper function to get sort indicator
function getSortIndicator($column, $current_sort_by, $current_sort_dir) {
    if ($current_sort_by !== $column) {
        return ' <span class="sort-indicator">⇅</span>';
    }
    return $current_sort_dir === 'ASC' ? ' <span class="sort-indicator active">↑</span>' : ' <span class="sort-indicator active">↓</span>';
}

// Helper function to generate pagination URL
function getPaginationUrl($page, $current_params) {
    $params = $current_params;
    $params['page'] = $page;
    return '/stores?' . http_build_query($params);
}
?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <a href="/stores/create" class="btn btn-primary">Add New Store</a>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <form method="GET" action="/stores" class="filters-form">
        <!-- Preserve sorting parameters -->
        <input type="hidden" name="sort_by" value="<?= e($sorting['sort_by']) ?>">
        <input type="hidden" name="sort_dir" value="<?= e($sorting['sort_dir']) ?>">
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="search">Search:</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="<?= e($filters['search'] ?? '') ?>" 
                       placeholder="Search stores..."
                       class="form-control">
            </div>
            
            <div class="filter-group">
                <label for="city">City:</label>
                <select id="city" name="city" class="form-control">
                    <option value="">All Cities</option>
                    <?php foreach ($filter_options['cities'] as $city): ?>
                        <option value="<?= e($city) ?>" <?= ($filters['city'] ?? '') === $city ? 'selected' : '' ?>>
                            <?= e($city) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="state_region">State/Region:</label>
                <select id="state_region" name="state_region" class="form-control">
                    <option value="">All States/Regions</option>
                    <?php foreach ($filter_options['states'] as $state): ?>
                        <option value="<?= e($state) ?>" <?= ($filters['state_region'] ?? '') === $state ? 'selected' : '' ?>>
                            <?= e($state) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="country">Country:</label>
                <select id="country" name="country" class="form-control">
                    <option value="">All Countries</option>
                    <?php foreach ($filter_options['countries'] as $country): ?>
                        <option value="<?= e($country) ?>" <?= ($filters['country'] ?? '') === $country ? 'selected' : '' ?>>
                            <?= e($country) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Apply Filters</button>
                <a href="/stores" class="btn btn-outline">Clear All</a>
            </div>
        </div>
    </form>
</div>

<!-- Results Info -->
<div class="results-info">
    <p>
        Showing <?= $pagination['total'] > 0 ? (($pagination['current_page'] - 1) * $pagination['per_page'] + 1) : 0 ?> 
        to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> 
        of <?= $pagination['total'] ?> stores
    </p>
</div>

<?php if (empty($stores)): ?>
    <div class="alert alert-info">
        No stores found matching your criteria. <a href="/stores/create">Create the first one!</a>
    </div>
<?php else: ?>
    <!-- Data Table -->
    <table class="table-data">
        <thead>
            <tr>
                <th>
                    <a href="<?= getSortUrl('name', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Name<?= getSortIndicator('name', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>Address</th>
                <th>
                    <a href="<?= getSortUrl('city', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        City<?= getSortIndicator('city', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('state_region', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        State/Region<?= getSortIndicator('state_region', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('country', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Country<?= getSortIndicator('country', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('phone', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Phone<?= getSortIndicator('phone', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('email', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Email<?= getSortIndicator('email', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stores as $store): ?>
                <tr>
                    <td>
                        <strong>
                            <a href="/stores/show/<?= e($store['id']) ?>">
                                <?= e($store['name']) ?>
                            </a>
                        </strong>
                    </td>
                    <td>
                        <?= e($store['address_line1']) ?><br>
                        <?php if (!empty($store['address_line2'])): ?>
                            <?= e($store['address_line2']) ?><br>
                        <?php endif; ?>
                    </td>
                    <td><?= e($store['city']) ?></td>
                    <td><?= e($store['state_region']) ?></td>
                    <td><?= e($store['country']) ?></td>
                    <td>
                        <?php if (!empty($store['phone'])): ?>
                            <a href="tel:<?= e($store['phone']) ?>"><?= e($store['phone']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($store['email'])): ?>
                            <a href="mailto:<?= e($store['email']) ?>"><?= e($store['email']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <a href="/stores/show/<?= e($store['id']) ?>" class="btn btn-sm btn-primary">View</a>
                        <a href="/stores/edit/<?= e($store['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <a href="/stores/delete/<?= e($store['id']) ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this store?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination-wrapper">
            <nav class="pagination">
                <!-- Previous page -->
                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= getPaginationUrl(1, $filters) ?>" class="page-link">First</a>
                    <a href="<?= getPaginationUrl($pagination['current_page'] - 1, $filters) ?>" class="page-link">Previous</a>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php
                $start = max(1, $pagination['current_page'] - 2);
                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                
                for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $pagination['current_page']): ?>
                        <span class="page-link current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= getPaginationUrl($i, $filters) ?>" class="page-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next page -->
                <?php if ($pagination['has_next']): ?>
                    <a href="<?= getPaginationUrl($pagination['current_page'] + 1, $filters) ?>" class="page-link">Next</a>
                    <a href="<?= getPaginationUrl($pagination['total_pages'], $filters) ?>" class="page-link">Last</a>
                <?php endif; ?>
            </nav>
            
            <!-- Per page selector -->
            <div class="per-page-selector">
                <form method="GET" action="/stores" class="per-page-form">
                    <!-- Preserve current parameters -->
                    <?php foreach ($filters as $key => $value): ?>
                        <?php if ($key !== 'per_page' && $key !== 'page' && !empty($value)): ?>
                            <input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <input type="hidden" name="sort_by" value="<?= e($sorting['sort_by']) ?>">
                    <input type="hidden" name="sort_dir" value="<?= e($sorting['sort_dir']) ?>">
                    
                    <label for="per_page">Show:</label>
                    <select name="per_page" id="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $pagination['per_page'] == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $pagination['per_page'] == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $pagination['per_page'] == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $pagination['per_page'] == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                    <span>per page</span>
                </form>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
