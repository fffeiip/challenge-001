<?php
ob_start();
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<div>
    <form method="POST" action="store.php?action=update&id=<?= $store['id'] ?>">
        <div class="form-group">
            <label for="name">Name*</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? $store['name'] ?? '') ?>">

            <?php if (!empty($errors['name'])): ?>
                <div class="error"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? $store['email'] ?? '') ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Phone*</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old['phone'] ?? $store['phone'] ?? '') ?>">
            <?php if (!empty($errors['phone'])): ?>
                <div class="error"><?= htmlspecialchars($errors['phone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address_line1">Address Line 1*</label>
            <input type="text" name="address_line1" id="address_line1" value="<?= htmlspecialchars($old['address_line1'] ?? $store['address_line1'] ?? '') ?>">
            <?php if (!empty($errors['address_line1'])): ?>
                <div class="error"><?= htmlspecialchars($errors['address_line1']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address_line2">Address Line 2</label>
            <input type="text" name="address_line2" id="address_line2" value="<?= htmlspecialchars($old['address_line2'] ?? $store['address_line2'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="city">City*</label>
            <input type="text" name="city" id="city" value="<?= htmlspecialchars($old['city'] ?? $store['city'] ?? '') ?>">
            <?php if (!empty($errors['city'])): ?>
                <div class="error"><?= htmlspecialchars($errors['city']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="state_region">State*</label>
            <input type="text" name="state_region" id="state_region" value="<?= htmlspecialchars($old['state_region'] ?? $store['state_region'] ?? '') ?>">
            <?php if (!empty($errors['state_region'])): ?>
                <div class="error"><?= htmlspecialchars($errors['state_region']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="country">Country*</label>
            <input type="text" name="country" id="country" value="<?= htmlspecialchars($old['country'] ?? $store['country'] ?? '') ?>">
            <?php if (!empty($errors['country'])): ?>
                <div class="error"><?= htmlspecialchars($errors['country']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Update Store</button>
    </form>
</div>

<!-- <form method="POST" action="store.php?action=update&id=<?= $store['id'] ?>">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($store['name']) ?>" required></label><br><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($store['email']) ?>" required></label><br><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($store['phone']) ?>"></label><br><br>
    <label>Address Line 1: <input type="text" name="address_line1" value="<?= htmlspecialchars($store['address_line1']) ?>"></label><br><br>
    <label>Address Line 2: <input type="text" name="address_line2" value="<?= htmlspecialchars($store['address_line2']) ?>"></label><br><br>
    <label>City: <input type="text" name="city" value="<?= htmlspecialchars($store['city']) ?>"></label><br><br>
    <label>State/Region: <input type="text" name="state_region" value="<?= htmlspecialchars($store['state_region']) ?>"></label><br><br>
    <label>Country: <input type="text" name="country" value="<?= htmlspecialchars($store['country']) ?>"></label><br><br>
    <button type="submit">Update Store</button>
</form> -->
<?php
$content = ob_get_clean();
$title = "Edit Store";
include __DIR__ . '/../layout.php';
