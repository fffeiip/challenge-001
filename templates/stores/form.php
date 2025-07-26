<?php

?>

<div class="page-header">
    <h1><?= e($title) ?></h1>
    <a href="/stores" class="btn btn-secondary">Back to List</a>
</div>

<form action="<?= e($action) ?>" method="POST">
    <div class="form-group">
        <label for="name">Store Name</label>
        <input type="text" id="name" name="name" class="form-control" value="<?= e($store['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="address_line1">Address Line 1</label>
        <input type="text" id="address_line1" name="address_line1" class="form-control" value="<?= e($store['address_line1'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="address_line2">Address Line 2</label>
        <input type="text" id="address_line2" name="address_line2" class="form-control" value="<?= e($store['address_line2'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="city">City</label>
        <input type="text" id="city" name="city" class="form-control" value="<?= e($store['city'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="state_region">State/Region</label>
        <input type="text" id="state_region" name="state_region" class="form-control" value="<?= e($store['state_region'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="country">Country</label>
        <input type="text" id="country" name="country" class="form-control" value="<?= e($store['country'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" class="form-control" value="<?= e($store['phone'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($store['email'] ?? '') ?>">
    </div>

    <button type="submit" class="btn btn-primary"><?= e($buttonText) ?></button>
</form>

