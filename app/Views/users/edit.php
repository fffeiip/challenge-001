<h1>Edit User</h1>
<form class="form-grid" method="post" action="/users/update/<?= $user->id ?>">
    <?= $this->csrfField() ?>
    <div class="form-group">
        <label for="username">Username:</label>
        <input id="username" name="username" value="<?= htmlspecialchars($user->username) ?>">

        <label for="password">New Password:</label>
        <input id="password" type="password" name="password">

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option <?= $user->role==='store_user'?'selected':'' ?> value="store_user">Store User</option>
            <option <?= $user->role==='super_admin'?'selected':'' ?> value="super_admin">Super Admin</option>
        </select>

        <label for="store_id">Store:</label>
        <select id="store_id" name="store_id">
            <?php foreach ($stores as $store): ?>
                <option value="<?= $store->id ?>" <?= $user->store_id === $store->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($store->name) ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit">Update</button>
    </div>
</form>