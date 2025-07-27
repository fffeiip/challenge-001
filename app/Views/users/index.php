<h1>Users</h1>
<?php if ($this->currentUser->role === 'super_admin'): ?>
    <p>
        <a class="a-btn" href="/users/create">+ New User</a>
    </p>
<?php endif; ?>
<table id="users-table">
    <thead>
        <tr>
            <th class="sortable">ID</th>
            <th class="sortable">Username</th>
            <th class="sortable">Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u->id ?></td>
                <td><?= htmlspecialchars($u->username) ?></td>
                <td><?= $u->role ?></td>
                <td>
                    <?php if ($this->currentUser->role === 'super_admin'): ?>
                        <a href="/users/edit/<?= $u->id ?>" title="Edit">
                            <span class="icon-edit" aria-hidden="true">&#9998;</span>
                        </a> |
                        <a href="/users/show/<?= $u->id ?>" title="View">
                            <span class="icon-view" aria-hidden="true">&#128065;</span>
                        </a> |
                        <form method="post" action="/users/delete/<?= $u->id ?>" style="display:inline;">
                            <?= $this->csrfField() ?>
                            <button class="btn-delete" type="submit" title="Delete">
                                <span class="icon-delete" aria-hidden="true">&#128465;</span>
                            </button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>