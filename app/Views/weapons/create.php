<h1>Create Weapon</h1>
<form class="form-grid" method="post" action="/weapons/store">
    <?= $this->csrfField() ?>
    <div class="form-group">
        <label for="name">Name:</label>
        <input id="name" name="name">

        <label for="type">Type:</label>
        <input id="type" name="type">

        <label for="caliber">Caliber:</label>
        <input id="caliber" name="caliber">

        <label for="serial_number">Serial Number:</label>
        <input id="serial_number" name="serial_number">

        <label for="price">Price:</label>
        <input id="price" name="price" type="number" step="0.01">

        <label for="in_stock">In Stock:</label>
        <input id="in_stock" name="in_stock" type="number" value="0">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="active">Active</option>
            <option value="out_of_stock">Out of Stock</option>
            <option value="discontinued">Discontinued</option>
        </select>

        <label for="store_id">Store:</label>
        <select id="store_id" name="store_id">
            <?php foreach ($stores as $store): ?>
                <option value="<?= $store->id ?>"><?= htmlspecialchars($store->name) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save</button>
    </div>
</form>