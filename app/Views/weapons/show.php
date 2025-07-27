<h1>Weapon: <?= htmlspecialchars($weapon->name) ?></h1>
<p>
  <a class="a-btn" href="/weapons/pdf/<?= $weapon->id ?>" target="_blank">Print PDF</a>
</p>
<p>
  <strong>Store:</strong>
  <a href="/stores/show/<?= $store->id ?>">
    <?= htmlspecialchars($store->name) ?>
  </a>
</p>
<p><strong>Type:</strong> <?= htmlspecialchars($weapon->type) ?></p>
<p><strong>Caliber:</strong> <?= htmlspecialchars($weapon->caliber) ?></p>
<p><strong> Serial Number:</strong> <?= htmlspecialchars($weapon->serial_number) ?></p>
<p><strong>Price:</strong> $<?= number_format($weapon->price, 2) ?></p>
<p><strong>In Stock:</strong> <?= $weapon->in_stock ?></p>
<p><strong>Status:</strong> <?= $weapon->status ?></p>
