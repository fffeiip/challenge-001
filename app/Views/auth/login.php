<h1>Login</h1>
<form class="form-grid" method="post" action="/login">
    <?= $this->csrfField() ?>
    <div class="form-group">
        <label for="username">Username:</label>
        <input id="username" name="username">

        <label for="password">Password:</label>
        <input id="password" type="password" name="password">

         <button type="submit">Login</button>
    </div>
</form>