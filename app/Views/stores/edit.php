<h1>Edit Store</h1>
<form class="form-grid" method="post" action="/stores/update/<?= $store->id ?>">
    <div class="form-group">
        <?= $this->csrfField() ?>
        <label for="name">Name:</label>
        <input id="name" name="name" value="<?= htmlspecialchars($store->name) ?>">

        <label for="slug">Slug:</label>
        <input id="slug" name="slug" value="<?= htmlspecialchars($store->slug) ?>">

        <label for="address_line1">Address Line 1:</label>
        <input id="address_line1" name="address_line1" value="<?= htmlspecialchars($store->address_line1) ?>">

        <label for="address_line2">Address Line 2:</label>
        <input id="address_line2" name="address_line2" value="<?= htmlspecialchars($store->address_line2) ?>">

        <label for="city">City:</label>
        <input id="city" name="city" value="<?= htmlspecialchars($store->city) ?>">

        <label for="state_region">State/Region:</label>
        <input id="state_region" name="state_region" value="<?= htmlspecialchars($store->state_region) ?>">

        <label for="country">Country:</label>
        <input id="country" name="country" value="<?= htmlspecialchars($store->country) ?>">

        <label for="phone">Phone:</label>
        <input id="phone" name="phone" value="<?= htmlspecialchars($store->phone) ?>">

        <label for="email">Email:</label>
        <input id="email" name="email" value="<?= htmlspecialchars($store->email) ?>">
        
        <button type="submit">Update</button>
    </div>
</form>
