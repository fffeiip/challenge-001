<?php

?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <a href="/weapons/list" class="btn btn-secondary">Back to List</a>
</div>

<form action="<?= e($action) ?>" method="POST">
    <div class="form-group">
        <label for="name">Weapon Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($weapon['name'] ?? '') ?>" required>
    </div>

    <!-- Store Autocomplete Section -->
    <div class="form-group store-autocomplete" id="store-section">
        <label for="store_search">Store <span class="required">*</span></label>
        <div class="autocomplete-container">
            <input type="text" 
                   id="store_search" 
                   class="form-control" 
                   placeholder="Search for a store..." 
                   autocomplete="off"
                   value="<?= isset($weapon['store_id']) ? e($stores[array_search($weapon['store_id'], array_column($stores, 'id'))]['name'] ?? '') : '' ?>">
            <input type="hidden" 
                   id="store_id" 
                   name="store_id" 
                   value="<?= e($weapon['store_id'] ?? '') ?>" 
                   required>
            <div class="autocomplete-dropdown" id="store-dropdown"></div>
            <div class="selected-store" id="selected-store" style="display: <?= isset($weapon['store_id']) ? 'block' : 'none' ?>">
                <div class="store-info">
                    <span class="store-name" id="selected-store-name">
                        <?= isset($weapon['store_id']) ? e($stores[array_search($weapon['store_id'], array_column($stores, 'id'))]['name'] ?? '') : '' ?>
                    </span>
                    <span class="store-details" id="selected-store-details"></span>
                </div>
                <button type="button" class="clear-selection" id="clear-store">×</button>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="type">Type</label>
        <input type="text" id="type" name="type" class="form-control" value="<?= e($weapon['type'] ?? '') ?>" placeholder="e.g., Rifle, Handgun" required>
    </div>

    <div class="form-group">
        <label for="caliber">Caliber</label>
        <input type="text" id="caliber" name="caliber" class="form-control" value="<?= e($weapon['caliber'] ?? '') ?>" placeholder="e.g., 9mm, .223" required>
    </div>

    <div class="form-group">
        <label for="serial_number">Serial Number</label>
        <input type="text" id="serial_number" name="serial_number" class="form-control" value="<?= e($weapon['serial_number'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" id="price" name="price" class="form-control" value="<?= e($weapon['price'] ?? '0.00') ?>" step="0.01" min="0" required>
    </div>

    <div class="form-group">
        <label for="in_stock">In Stock (Quantity)</label>
        <input type="number" id="in_stock" name="in_stock" class="form-control" value="<?= e($weapon['in_stock'] ?? '0') ?>" min="0" required>
    </div>

    <div class="form-group">
        <label for="status">Status</label>
        <select id="status" name="status" class="form-control" required>
            <?php
            $statuses = ['active', 'discontinued', 'out_of_stock'];
            $currentStatus = $weapon['status'] ?? 'active';
            foreach ($statuses as $status): ?>
                <option value="<?= e($status) ?>" <?= ($currentStatus === $status) ? 'selected' : '' ?>>
                    <?= e(ucfirst(str_replace('_', ' ', $status))) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary"><?= e($buttonText) ?></button>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const storeSearch = document.getElementById('store_search');
    const storeIdInput = document.getElementById('store_id');
    const dropdown = document.getElementById('store-dropdown');
    const selectedStore = document.getElementById('selected-store');
    const selectedStoreName = document.getElementById('selected-store-name');
    const selectedStoreDetails = document.getElementById('selected-store-details');
    const clearButton = document.getElementById('clear-store');
    
    let searchTimeout;
    let currentHighlight = -1;
    let stores = [];

    // Initialize - if we have a selected store, hide the search input
    if (storeIdInput.value) {
        storeSearch.style.display = 'none';
        selectedStore.style.display = 'block';
    }

    storeSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideDropdown();
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchStores(query);
        }, 300);
    });

    storeSearch.addEventListener('keydown', function(e) {
        if (dropdown.style.display === 'none') return;

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                currentHighlight = Math.min(currentHighlight + 1, stores.length - 1);
                updateHighlight();
                break;
            case 'ArrowUp':
                e.preventDefault();
                currentHighlight = Math.max(currentHighlight - 1, -1);
                updateHighlight();
                break;
            case 'Enter':
                e.preventDefault();
                if (currentHighlight >= 0 && stores[currentHighlight]) {
                    selectStore(stores[currentHighlight]);
                }
                break;
            case 'Escape':
                hideDropdown();
                break;
        }
    });

    storeSearch.addEventListener('blur', function() {
        // Delay hiding to allow for click events
        setTimeout(() => {
            hideDropdown();
        }, 200);
    });

    clearButton.addEventListener('click', function() {
        clearSelection();
    });

    function searchStores(query) {
        showLoading();
        
        fetch(`/weapons/store-autocomplete?query=${encodeURIComponent(query)}&limit=8`)
            .then(response => response.json())
            .then(data => {
                stores = data;
                displayResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                showError();
            });
    }

    function showLoading() {
        dropdown.innerHTML = '<div class="loading-spinner">Searching stores...</div>';
        dropdown.style.display = 'block';
        currentHighlight = -1;
    }

    function showError() {
        dropdown.innerHTML = '<div class="no-results">Error searching stores. Please try again.</div>';
        dropdown.style.display = 'block';
    }

    function displayResults(results) {
        if (results.length === 0) {
            dropdown.innerHTML = '<div class="no-results">No stores found matching your search.</div>';
        } else {
            dropdown.innerHTML = results.map((store, index) => `
                <div class="autocomplete-item" data-index="${index}" onclick="selectStore(${JSON.stringify(store).replace(/"/g, '&quot;')})">
                    <div class="store-name">${escapeHtml(store.name)}</div>
                    ${store.address ? `<div class="store-address">${escapeHtml(store.address)}</div>` : ''}
                </div>
            `).join('');
        }
        
        dropdown.style.display = 'block';
        currentHighlight = -1;
    }

    function updateHighlight() {
        const items = dropdown.querySelectorAll('.autocomplete-item');
        items.forEach((item, index) => {
            item.classList.toggle('highlighted', index === currentHighlight);
        });
    }

    function selectStore(store) {
        storeIdInput.value = store.id;
        selectedStoreName.textContent = store.name;
        
        let details = [];
        if (store.address) details.push(store.address);
        if (store.phone) details.push(store.phone);
        selectedStoreDetails.textContent = details.join(' • ');
        
        storeSearch.style.display = 'none';
        selectedStore.style.display = 'block';
        hideDropdown();
        
        // Clear any validation errors
        storeSearch.classList.remove('is-invalid');
    }

    function clearSelection() {
        storeIdInput.value = '';
        storeSearch.value = '';
        storeSearch.style.display = 'block';
        selectedStore.style.display = 'none';
        storeSearch.focus();
    }

    function hideDropdown() {
        dropdown.style.display = 'none';
        currentHighlight = -1;
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Click outside to close dropdown
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.store-autocomplete')) {
            hideDropdown();
        }
    });

    // Form validation
    document.querySelector('.weapon-form').addEventListener('submit', function(e) {
        if (!storeIdInput.value) {
            e.preventDefault();
            storeSearch.classList.add('is-invalid');
            storeSearch.focus();
            alert('Please select a store from the search results.');
        }
    });
});

// Global function for onclick events in dropdown
function selectStore(store) {
    const event = new CustomEvent('selectStore', { detail: store });
    document.getElementById('store_search').dispatchEvent(event);
    
    // Trigger the existing selectStore function
    const storeIdInput = document.getElementById('store_id');
    const selectedStore = document.getElementById('selected-store');
    const selectedStoreName = document.getElementById('selected-store-name');
    const selectedStoreDetails = document.getElementById('selected-store-details');
    const storeSearch = document.getElementById('store_search');
    const dropdown = document.getElementById('store-dropdown');
    
    storeIdInput.value = store.id;
    selectedStoreName.textContent = store.name;
    
    let details = [];
    if (store.address) details.push(store.address);
    if (store.phone) details.push(store.phone);
    selectedStoreDetails.textContent = details.join(' • ');
    
    storeSearch.style.display = 'none';
    selectedStore.style.display = 'block';
    dropdown.style.display = 'none';
}
</script>
<style>
.weapon-form {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-group {
    position: relative;
    display: flex;
}

.input-group-text {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-right: none;
    border-radius: 8px 0 0 8px;
    padding: 12px 15px;
    font-weight: 600;
    color: #6c757d;
}

.input-group .form-control {
    border-left: none;
    border-radius: 0 8px 8px 0;
}

/* Store Autocomplete Styles */
.store-autocomplete {
    position: relative;
}

.autocomplete-container {
    position: relative;
}

.autocomplete-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 2px solid #e9ecef;
    border-top: none;
    border-radius: 0 0 8px 8px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.autocomplete-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.2s ease;
}

.autocomplete-item:hover,
.autocomplete-item.highlighted {
    background-color: #f8f9fa;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.store-name {
    font-weight: 600;
    color: #495057;
    display: block;
}

.store-address {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
}

.selected-store {
    display: none;
    background: #e3f2fd;
    border: 2px solid #2196f3;
    border-radius: 8px;
    padding: 12px 15px;
    margin-top: 8px;
    position: relative;
}

.selected-store .store-info {
    padding-right: 30px;
}

.selected-store .store-name {
    font-weight: 600;
    color: #1976d2;
}

.selected-store .store-details {
    font-size: 12px;
    color: #666;
    display: block;
    margin-top: 4px;
}

.clear-selection {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 18px;
    color: #666;
    cursor: pointer;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.clear-selection:hover {
    background: #f44336;
    color: white;
}

.loading-spinner {
    padding: 12px 15px;
    text-align: center;
    color: #6c757d;
    font-size: 14px;
}

.no-results {
    padding: 12px 15px;
    text-align: center;
    color: #6c757d;
    font-size: 14px;
    font-style: italic;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f8f9fa;
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-icon {
    font-size: 16px;
}

@media (max-width: 768px) {
    .weapon-form {
        padding: 20px;
        margin: 10px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>