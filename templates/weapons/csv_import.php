<?php
// Create this as: views/weapons/csv_import.php
?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <div class="page-actions">
        <a href="/weapons/list" class="btn btn-secondary">← Back to Weapons</a>
        <a href="/weapons/csv-template" class="btn btn-info">📥 Download Template</a>
    </div>
</div>

<div class="import-instructions">
    <div class="alert alert-info">
        <h3>CSV Import Instructions</h3>
        <ul>
            <li><strong>Required columns:</strong> name, type, caliber, serial_number, price, store_id, in_stock, status</li>
            <li><strong>File format:</strong> CSV with UTF-8 encoding</li>
            <li><strong>Maximum file size:</strong> 10MB</li>
            <li><strong>Status values:</strong> active, out_of_stock, discontinued</li>
            <li><strong>In Stock values:</strong> 1 (yes) or 0 (no)</li>
            <li><strong>Store ID:</strong> Must match existing store IDs, or use default store below</li>
        </ul>
        <p><strong>Tip:</strong> Download the template file to see the correct format and example data.</p>
    </div>
</div>

<div class="import-form-container">
    <form method="POST" action="/weapons/import-csv" enctype="multipart/form-data" class="import-form">
        <div class="form-section">
            <h3>File Selection</h3>
            
            <div class="form-group">
                <label for="csv_file" class="form-label">CSV File *</label>
                <input type="file" 
                       id="csv_file" 
                       name="csv_file" 
                       accept=".csv,.txt" 
                       required 
                       class="form-control">
                <small class="form-help">Select a CSV file to import. Maximum size: 10MB</small>
            </div>
        </div>

        <div class="form-section">
            <h3>Import Options</h3>
            
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" 
                           id="skip_first_row" 
                           name="skip_first_row" 
                           value="1" 
                           checked 
                           class="form-checkbox">
                    Skip first row (header row)
                </label>
                <small class="form-help">Check if your CSV file has column headers in the first row</small>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" 
                           id="update_existing" 
                           name="update_existing" 
                           value="1" 
                           class="form-checkbox">
                    Update existing weapons (match by serial number)
                </label>
                <small class="form-help">If unchecked, existing weapons will be skipped</small>
            </div>

            <div class="form-group">
                <label for="default_store_id" class="form-label">Default Store (Optional)</label>
                <select id="default_store_id" name="default_store_id" class="form-control">
                    <option value="">Select default store...</option>
                    <?php foreach ($stores as $store): ?>
                        <option value="<?= e($store['id']) ?>">
                            <?= e($store['name']) ?> - <?= e($store['country']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-help">Used for rows where store_id is empty or invalid</small>
            </div>
        </div>

        <div class="form-section">
            <h3>CSV Format Example</h3>
            <div class="csv-preview">
                <table class="table-preview">
                    <thead>
                        <tr>
                            <th>name</th>
                            <th>type</th>
                            <th>caliber</th>
                            <th>serial_number</th>
                            <th>price</th>
                            <th>store_id</th>
                            <th>in_stock</th>
                            <th>status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Glock 19</td>
                            <td>Pistol</td>
                            <td>9mm</td>
                            <td>GL19123456</td>
                            <td>599.99</td>
                            <td>1</td>
                            <td>1</td>
                            <td>active</td>
                        </tr>
                        <tr>
                            <td>AR-15 Rifle</td>
                            <td>Rifle</td>
                            <td>.223</td>
                            <td>AR15789012</td>
                            <td>899.99</td>
                            <td>2</td>
                            <td>0</td>
                            <td>active</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                📤 Import Weapons
            </button>
            <a href="/weapons/list" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.import-form');
    const fileInput = document.getElementById('csv_file');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // File validation
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Check file size (10MB max)
            if (file.size > 10 * 1024 * 1024) {
                alert('File is too large. Maximum size is 10MB.');
                this.value = '';
                return;
            }
            
            // Check file extension
            const extension = file.name.split('.').pop().toLowerCase();
            if (!['csv', 'txt'].includes(extension)) {
                alert('Please select a CSV file.');
                this.value = '';
                return;
            }
        }
    });
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            alert('Please select a CSV file to import.');
            e.preventDefault();
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Importing...';
        
        // Confirm import
        if (!confirm('Are you sure you want to import this CSV file? This action cannot be undone.')) {
            e.preventDefault();
            submitBtn.disabled = false;
            submitBtn.innerHTML = '📤 Import Weapons';
            return;
        }
    });
});
</script>