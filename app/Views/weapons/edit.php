<h1>Edit Weapon</h1>
<form class="form-grid" method="post" action="/weapons/update/<?= $weapon->id ?>">
    <div class="form-group">
        <?= $this->csrfField() ?>
        <label for="name">Name:</label>
        <input id="name" name="name" value="<?= htmlspecialchars($weapon->name) ?>">

        <label for="type">Type:</label>
        <input id="type" name="type" value="<?= htmlspecialchars($weapon->type) ?>">

        <label for="caliber">Caliber:</label>
        <input id="caliber" name="caliber" value="<?= htmlspecialchars($weapon->caliber) ?>">

        <label for="serial_number">Serial Number:</label>
        <input id="serial_number" name="serial_number" value="<?= htmlspecialchars($weapon->serial_number) ?>">

        <label for="price">Price:</label>
        <input id="price" name="price" type="number" step="0.01" value="<?= htmlspecialchars($weapon->price) ?>">

        <label for="in_stock">In Stock:</label>
        <input id="in_stock" name="in_stock" type="number" value="<?= $weapon->in_stock ?>">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="active" <?= $weapon->status === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="out_of_stock" <?= $weapon->status === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
            <option value="discontinued" <?= $weapon->status === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
        </select>
        
        <label for="store_id">Store:</label>
        <select id="store_id" name="store_id">
            <?php foreach ($stores as $store): ?>
                <option value="<?= $store->id ?>" <?= $weapon->store_id === $store->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($store->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Update</button>
    </div>
</form>