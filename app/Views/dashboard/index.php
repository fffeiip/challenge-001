<div class="dashboard-header">
    <h3>Welcome, <?= htmlspecialchars($this->currentUser->username) ?>!</h3>
    <p>Your role: <?= htmlspecialchars($this->currentUser->role) ?></p>
</div>
<div class="dashboard-grid">
  <div class="card">
    <h3>Stores</h3>
    <p><?= htmlspecialchars($storesCount) ?></p>
    <a href="/stores">Manage</a>
    <a class="btn" href="/stores/pdf" target="_blank">Print All</a>
  </div>
  <div class="card">
    <h3>Weapons</h3>
    <p><?= htmlspecialchars($weaponsCount) ?></p>
    <a href="/weapons">Manage</a>
    <a class="btn" href="/weapons/pdf" target="_blank">Print All</a>
  </div>
  <?php if ($userRole==='super_admin'): ?>
    <div class="card">
        <h3>Users</h3>
        <p><?= htmlspecialchars($usersCount) ?></p>
        <a href="/users">Manage</a>
    </div>
  <?php endif; ?>
</div>