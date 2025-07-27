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
    
    return '/weapons?' . http_build_query($params);
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
    return '/weapons?' . http_build_query($params);
}
?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <a href="/weapons/create" class="btn btn-primary">Add New Weapon</a>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <form method="GET" action="/weapons" class="filters-form">
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
                       placeholder="Search weapons..."
                       class="form-control">
            </div>
            
            <div class="filter-group">
                <label for="type">Type:</label>
                <select id="type" name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach ($filter_options['types'] as $type): ?>
                        <option value="<?= e($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>>
                            <?= e($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="caliber">Caliber:</label>
                <select id="caliber" name="caliber" class="form-control">
                    <option value="">All Calibers</option>
                    <?php foreach ($filter_options['calibers'] as $caliber): ?>
                        <option value="<?= e($caliber) ?>" <?= ($filters['caliber'] ?? '') === $caliber ? 'selected' : '' ?>>
                            <?= e($caliber) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="store_id">Store:</label>
                <select id="store_id" name="store_id" class="form-control">
                    <option value="">All Stores</option>
                    <?php foreach ($filter_options['stores'] as $store): ?>
                        <option value="<?= e($store['id']) ?>" <?= ($filters['store_id'] ?? '') == $store['id'] ? 'selected' : '' ?>>
                            <?= e($store['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status">Status:</label>
                <select id="status" name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach ($filter_options['statuses'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="in_stock">In Stock:</label>
                <select id="in_stock" name="in_stock" class="form-control">
                    <option value="">All</option>
                    <option value="1" <?= ($filters['in_stock'] ?? '') === '1' ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= ($filters['in_stock'] ?? '') === '0' ? 'selected' : '' ?>>No</option>
                </select>
            </div>
        </div>
        
        <div class="filter-row">
            <div class="filter-group">
                <label for="price_min">Min Price:</label>
                <input type="number" 
                       id="price_min" 
                       name="price_min" 
                       value="<?= e($filters['price_min'] ?? '') ?>" 
                       placeholder="0.00"
                       step="0.01"
                       min="0"
                       class="form-control">
            </div>
            
            <div class="filter-group">
                <label for="price_max">Max Price:</label>
                <input type="number" 
                       id="price_max" 
                       name="price_max" 
                       value="<?= e($filters['price_max'] ?? '') ?>" 
                       placeholder="9999.99"
                       step="0.01"
                       min="0"
                       class="form-control">
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn btn-secondary">Apply Filters</button>
                <a href="/weapons" class="btn btn-outline">Clear All</a>
            </div>
        </div>
    </form>
</div>

<!-- Results Info -->
<div class="results-info">
    <p>
        Showing <?= $pagination['total'] > 0 ? (($pagination['current_page'] - 1) * $pagination['per_page'] + 1) : 0 ?> 
        to <?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total']) ?> 
        of <?= $pagination['total'] ?> weapons
    </p>
</div>

<?php if (empty($weapons)): ?>
    <div class="alert alert-info">
        No weapons found matching your criteria. <a href="/weapons/create">Create the first one!</a>
    </div>
<?php else: ?>
    <!-- Bulk Actions Section -->
    <div class="bulk-actions-section">
        <form id="bulk-export-form" method="POST" action="/weapons/bulk-pdf">
            <div class="bulk-actions-controls">
                <div class="selection-controls">
                    <label class="checkbox-label">
                        <input type="checkbox" id="select-all" class="form-checkbox"> 
                        Select All
                    </label>
                    <span id="selection-count" class="selection-count">0 selected</span>
                </div>
                <div class="bulk-buttons">
                    <button type="submit" id="bulk-export-btn" class="btn btn-info" disabled>
                        📄 Export Selected PDFs
                    </button>
                </div>
            </div>
            
            <!-- Hidden input container for selected weapon IDs -->
            <div id="selected-weapons-container"></div>
        </form>
    </div>

    <!-- Data Table -->
    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 40px;">
                   <input type="checkbox" id="header-select-all" class="form-checkbox">
                </th>
                <th>
                    <a href="<?= getSortUrl('name', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Name<?= getSortIndicator('name', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('type', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Type<?= getSortIndicator('type', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('serial_number', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Serial Number<?= getSortIndicator('serial_number', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('price', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Price<?= getSortIndicator('price', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('store_name', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Store<?= getSortIndicator('store_name', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('in_stock', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        In Stock<?= getSortIndicator('in_stock', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>
                    <a href="<?= getSortUrl('status', $sorting['sort_by'], $sorting['sort_dir'], $filters) ?>" class="sort-link">
                        Status<?= getSortIndicator('status', $sorting['sort_by'], $sorting['sort_dir']) ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($weapons as $weapon): ?>
                <tr>
                    <td>
                        <input type="checkbox" 
                               class="weapon-checkbox form-checkbox" 
                               value="<?= e($weapon['id']) ?>"
                               data-weapon-name="<?= e($weapon['name']) ?>">
                    </td>
                    <td>
                        <strong><?= e($weapon['name']) ?></strong><br>
                        <small class="text-muted">Caliber: <?= e($weapon['caliber']) ?></small>
                    </td>
                    <td><?= e($weapon['type']) ?></td>
                    <td><?= e($weapon['serial_number']) ?></td>
                    <td>$<?= e(number_format($weapon['price'], 2)) ?></td>
                    <td>
                        <a href="/stores/show/<?= e($weapon['store_id']) ?>"><?= e($weapon['store_name']) ?></a>
                    </td>
                    <td>
                        <?php if ($weapon['in_stock']): ?>
                            <span class="badge badge-success">Yes</span>
                        <?php else: ?>
                            <span class="badge badge-danger">No</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-<?= $weapon['status'] === 'active' ? 'success' : ($weapon['status'] === 'sold' ? 'danger' : 'warning') ?>">
                            <?= e(ucfirst($weapon['status'])) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <a href="/weapons/edit/<?= e($weapon['id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <a href="/weapons/pdf/<?= e($weapon['id']) ?>" class="btn btn-sm btn-info" target="_blank" title="Export to PDF">
                            <span class="pdf-icon">📄</span> PDF
                        </a>
                        <a href="/weapons/delete/<?= e($weapon['id']) ?>" 
                           class="btn btn-sm btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this weapon?');">Delete</a>
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
                <form method="GET" action="/weapons" class="per-page-form">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllMain = document.getElementById('select-all');
    const selectAllHeader = document.getElementById('header-select-all');
    const weaponCheckboxes = document.querySelectorAll('.weapon-checkbox');
    const bulkExportBtn = document.getElementById('bulk-export-btn');
    const selectionCount = document.getElementById('selection-count');
    const bulkExportForm = document.getElementById('bulk-export-form');
    const selectedWeaponsContainer = document.getElementById('selected-weapons-container');

    function updateHiddenInputs() {
        // Clear existing hidden inputs
        selectedWeaponsContainer.innerHTML = '';
        
        // Add hidden inputs for selected weapons
        const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
        checkedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'weapon_ids[]';
            hiddenInput.value = checkbox.value;
            selectedWeaponsContainer.appendChild(hiddenInput);
        });
    }

    function updateSelectionState() {
        const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
        const totalBoxes = weaponCheckboxes.length;
        const checkedCount = checkedBoxes.length;

        // Update selection count
        selectionCount.textContent = `${checkedCount} selected`;

        // Update bulk export button state
        bulkExportBtn.disabled = checkedCount === 0;

        // Update select all checkboxes
        const allChecked = checkedCount === totalBoxes && totalBoxes > 0;
        const someChecked = checkedCount > 0 && checkedCount < totalBoxes;

        selectAllMain.checked = allChecked;
        selectAllHeader.checked = allChecked;
        selectAllMain.indeterminate = someChecked;
        selectAllHeader.indeterminate = someChecked;

        // Update hidden inputs for form submission
        updateHiddenInputs();
    }

    function toggleAllCheckboxes(checked) {
        weaponCheckboxes.forEach(checkbox => {
            checkbox.checked = checked;
        });
        updateSelectionState();
    }

    // Event listeners for select all checkboxes
    if (selectAllMain) {
        selectAllMain.addEventListener('change', function() {
            toggleAllCheckboxes(this.checked);
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', function() {
            toggleAllCheckboxes(this.checked);
        });
    }

    // Event listeners for individual checkboxes
    weaponCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectionState);
    });

    // Form submission handler
    if (bulkExportForm) {
        bulkExportForm.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.weapon-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one weapon to export.');
                return;
            }

            // Show loading state
            bulkExportBtn.disabled = true;
            bulkExportBtn.innerHTML = '⏳ Generating PDFs...';
            
            // Create a confirmation message with selected weapon names
            const selectedNames = Array.from(checkedBoxes)
                .map(cb => cb.getAttribute('data-weapon-name'))
                .slice(0, 5); // Show first 5 names
            
            let confirmMessage = `Export ${checkedBoxes.length} weapon(s) to PDF?\n\n`;
            confirmMessage += selectedNames.join('\n');
            if (checkedBoxes.length > 5) {
                confirmMessage += `\n... and ${checkedBoxes.length - 5} more`;
            }
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                // Reset button state
                bulkExportBtn.disabled = checkedBoxes.length === 0;
                bulkExportBtn.innerHTML = '📄 Export Selected PDFs';
                return;
            }
            
            // Reset button after delay (in case of errors or slow response)
            setTimeout(() => {
                bulkExportBtn.disabled = checkedBoxes.length === 0;
                bulkExportBtn.innerHTML = '📄 Export Selected PDFs';
            }, 10000); // 10 seconds timeout
        });
    }

    // Initial state update
    updateSelectionState();

    // Handle row clicks to toggle checkbox (optional enhancement)
    const tableRows = document.querySelectorAll('.table-data tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger on clicks to links, buttons, or the checkbox itself
            if (e.target.tagName === 'A' || 
                e.target.tagName === 'BUTTON' || 
                e.target.type === 'checkbox' ||
                e.target.closest('a') || 
                e.target.closest('button')) {
                return;
            }
            
            const checkbox = row.querySelector('.weapon-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                updateSelectionState();
            }
        });
    });
});
</script>
