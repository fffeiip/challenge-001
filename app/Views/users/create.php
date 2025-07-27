<h1>Create User</h1>
<form class="form-grid" method="post" action="/users/store">
    <div class="form-group">
        <?= $this->csrfField() ?>
        <label for="username">Username:</label>
        <input id="username" name="username">

        <label for="password">Password:</label>
        <input id="password" type="password" name="password">

        <label for="role">Role:</label>
        <select id="role" name="role">
                <option value="store_user">Store User</option>
                <option value="super_admin">Super Admin</option>
            </select>
        </label>

        <label for="store_id">Store:</label>
        <select id="store_id" name="store_id">
            <?php foreach ($stores as $store): ?>
                <option value="<?= $store->id ?>"><?= htmlspecialchars($store->name) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save</button>
    </div>
</form>