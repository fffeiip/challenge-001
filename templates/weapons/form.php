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

    <div class="form-group">
        <label for="store_id">Store</label>
        <select id="store_id" name="store_id" class="form-control" required>
            <option value="">-- Select a Store --</option>
            <?php foreach ($stores as $store): ?>
                <option value="<?= e($store['id']) ?>" <?= (isset($weapon['store_id']) && $weapon['store_id'] == $store['id']) ? 'selected' : '' ?>>
                    <?= e($store['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
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


