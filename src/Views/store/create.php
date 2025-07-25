<?php
ob_start();
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<div>
    <form method="POST" action="store.php?action=store">
        <div class="form-group">
            <label for="name">Name*</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">

            <?php if (!empty($errors['name'])): ?>
                <div class="error"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="error"><?= htmlspecialchars($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Phone*</label>
            <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
            <?php if (!empty($errors['phone'])): ?>
                <div class="error"><?= htmlspecialchars($errors['phone']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address_line1">Address Line 1*</label>
            <input type="text" name="address_line1" id="address_line1" value="<?= htmlspecialchars($old['address_line1'] ?? '') ?>">
            <?php if (!empty($errors['address_line1'])): ?>
                <div class="error"><?= htmlspecialchars($errors['address_line1']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address_line2">Address Line 2</label>
            <input type="text" name="address_line2" id="address_line2" value="<?= htmlspecialchars($old['address_line2'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="city">City*</label>
            <input type="text" name="city" id="city" value="<?= htmlspecialchars($old['city'] ?? '') ?>">
            <?php if (!empty($errors['city'])): ?>
                <div class="error"><?= htmlspecialchars($errors['city']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="state_region">State*</label>
            <input type="text" name="state_region" id="state_region" value="<?= htmlspecialchars($old['state_region'] ?? '') ?>">
            <?php if (!empty($errors['state_region'])): ?>
                <div class="error"><?= htmlspecialchars($errors['state_region']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="country">Country*</label>
            <input type="text" name="country" id="country" value="<?= htmlspecialchars($old['country'] ?? '') ?>">
            <?php if (!empty($errors['country'])): ?>
                <div class="error"><?= htmlspecialchars($errors['country']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Create Store</button>
    </form>
</div>

<?php
$content = ob_get_clean();
$title = "Create Store";
include __DIR__ . '/../layout.php';
