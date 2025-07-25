<?php
ob_start();
?>
<form method="POST" action="store.php?action=update&id=<?= $store['id'] ?>">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($store['name']) ?>" required></label><br><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($store['email']) ?>" required></label><br><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($store['phone']) ?>"></label><br><br>
    <label>Address Line 1: <input type="text" name="address_line1" value="<?= htmlspecialchars($store['address_line1']) ?>"></label><br><br>
    <label>Address Line 2: <input type="text" name="address_line2" value="<?= htmlspecialchars($store['address_line2']) ?>"></label><br><br>
    <label>City: <input type="text" name="city" value="<?= htmlspecialchars($store['city']) ?>"></label><br><br>
    <label>State/Region: <input type="text" name="state_region" value="<?= htmlspecialchars($store['state_region']) ?>"></label><br><br>
    <label>Country: <input type="text" name="country" value="<?= htmlspecialchars($store['country']) ?>"></label><br><br>
    <button type="submit">Update Store</button>
</form>
<?php
$content = ob_get_clean();
$title = "Edit Store";
include __DIR__ . '/../layout.php';
