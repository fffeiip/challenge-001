<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 40px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 12px;
            color: #555;
        }

        .title {
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            font-size: 14px;
            margin-bottom: 8px;
            color: #444;
        }

        .field {
            margin-bottom: 5px;
        }

        .field strong {
            display: inline-block;
            width: 120px;
            color: #555;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Weapon Store Management System</h2>
        <p>Weapon Export Document</p>
        <hr>
    </div>

    <div class="content">
        <div class="section">
            <div class="title"><?= htmlspecialchars($weapon['name']) ?> (<?= htmlspecialchars($weapon['type']) ?>)</div>

            <div class="field"><strong>Caliber:</strong> <?= htmlspecialchars($weapon['caliber']) ?></div>
            <div class="field"><strong>Serial Number:</strong> <?= htmlspecialchars($weapon['serial_number']) ?></div>
            <div class="field"><strong>Price:</strong> $<?= number_format($weapon['price'], 2) ?></div>
            <div class="field"><strong>In Stock:</strong> <?= $weapon['in_stock'] ?></div>
            <div class="field"><strong>Status:</strong> <?= ucfirst(htmlspecialchars($weapon['status'])) ?></div>
        </div>

        <div class="section">
            <h3>Store Details</h3>
            <?php if ($store): ?>
                <div class="field"><strong>Name:</strong> <?= htmlspecialchars($store['name']) ?></div>
                <div class="field"><strong>City:</strong> <?= htmlspecialchars($store['city']) ?></div>
                <div class="field"><strong>Phone:</strong> <?= htmlspecialchars($store['phone']) ?></div>
                <div class="field"><strong>Email:</strong> <?= htmlspecialchars($store['email']) ?></div>
            <?php else: ?>
                <div class="field">Store details not available.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <hr>
        <p>© <?= date('Y') ?> Weapon Store Management. All rights reserved.</p>
    </div>

</body>
</html>
