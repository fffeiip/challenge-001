<?php
ob_start();
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<div>
    <form method="POST" action="weapon.php?action=store">
        <div class="form-group">
            <label for="name">Name*</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
            <?php if (!empty($errors['name'])): ?>
                <div class="error"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="type">Type*</label>
            <input type="text" name="type" id="type" value="<?= htmlspecialchars($old['type'] ?? '') ?>">
            <?php if (!empty($errors['type'])): ?>
                <div class="error"><?= htmlspecialchars($errors['type']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="caliber">Caliber*</label>
            <input type="text" name="caliber" id="caliber" value="<?= htmlspecialchars($old['caliber'] ?? '') ?>">
            <?php if (!empty($errors['caliber'])): ?>
                <div class="error"><?= htmlspecialchars($errors['caliber']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="serial_number">Serial Number*</label>
            <input type="text" name="serial_number" id="serial_number" value="<?= htmlspecialchars($old['serial_number'] ?? '') ?>">
            <?php if (!empty($errors['serial_number'])): ?>
                <div class="error"><?= htmlspecialchars($errors['serial_number']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="price">Price*</label>
            <input type="number" name="price" id="price" step="0.01" value="<?= htmlspecialchars($old['price'] ?? '') ?>">
            <?php if (!empty($errors['price'])): ?>
                <div class="error"><?= htmlspecialchars($errors['price']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="in_stock">In Stock*</label>
            <input type="number" name="in_stock" id="in_stock" value="<?= htmlspecialchars($old['in_stock'] ?? '') ?>">
            <?php if (!empty($errors['in_stock'])): ?>
                <div class="error"><?= htmlspecialchars($errors['in_stock']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="store_id">Store*</label>
            <select name="store_id" id="store_id">
                <option value="">-- Select Store --</option>
                <?php if (empty($stores)): ?>
                    <option value="">No stores available</option>
                <?php else: ?>
                    <?php foreach ($stores as $store): ?>
                        <option value="<?= $store['id'] ?>" <?= (isset($old['store_id']) && $old['store_id'] == $store['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($store['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (!empty($errors['store_id'])): ?>
                <div class="error"><?= htmlspecialchars($errors['store_id']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Create Weapon</button>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = "Create Weapon";
include __DIR__ . '/../layout.php';
