<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Stores</title>
</head>
<body>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($stores)): ?>
                <tr><td colspan="3">No stores found.</td></tr>
            <?php else: ?>
                <?php foreach ($stores as $store): ?>
                    <tr>
                        <td><?= htmlspecialchars($store['name']) ?></td>
                        <td><?= htmlspecialchars($store['email']) ?></td>
                        <td><?= htmlspecialchars($store['phone']) ?></td>
                        <td><?= htmlspecialchars($store['city']) ?></td>
                        <td><?= htmlspecialchars($store['state']) ?></td>
                        <td><?= htmlspecialchars($store['country']) ?></td>
                        <td><?= date('F j, Y', strtotime($store['created_at'])) ?></td>
                        <td>
                            View | Edit | Delete
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>

