<?php
ob_start();
?>
<form method="POST" action="store.php?action=store">
    <label>Name: <input type="text" name="name" required></label><br><br>
    <label>Email: <input type="email" name="email" required></label><br><br>
    <label>Phone: <input type="text" name="phone" required></label><br><br>
    <label>Address Line 1: <input type="text" name="address_line1" required></label><br><br>
    <label>Address Line 2: <input type="text" name="address_line2"></label><br><br>
    <label>City: <input type="text" name="city" required></label><br><br>
    <label>State: <input type="text" name="state_region" required></label><br><br>
    <label>Country: <input type="text" name="country" required></label><br><br>
    <button type="submit">Create Store</button>
</form>
<?php
$content = ob_get_clean();
$title = "Create Store";
include __DIR__ . '/../layout.php';
