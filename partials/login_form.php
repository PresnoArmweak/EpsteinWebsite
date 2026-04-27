<?php // partials/login_form.php ?>
<div class="historica-auth">
    <div class="historica-auth__card">
        <span class="historica-auth__eyebrow">Restricted area</span>
        <h1 class="historica-auth__title">Sign in</h1>
        <p class="historica-auth__lede">
            Public entries can be read without an account. Sign in to access
            members-only entries; sign in as an administrator for full access
            to the editorial dashboard.
        </p>

        <?php if (!empty($login_error)): ?>
            <div class="historica-auth__error"><?= e($login_error) ?></div>
        <?php endif; ?>

        <form method="post" class="historica-auth__form">
            <input type="hidden" name="action" value="login">

            <label class="historica-auth__field">
                <span>Username</span>
                <input type="text" name="username" autocomplete="username" required autofocus>
            </label>

            <label class="historica-auth__field">
                <span>Password</span>
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <button type="submit" class="historica-auth__submit">Sign in</button>
        </form>

        <div class="historica-auth__hint">
            <strong>Demo accounts</strong> (set up in the SQL dump):<br>
            <code>admin / admin123</code> &middot;
            <code>member / member123</code>
        </div>

        <p class="historica-auth__note">
            The Epstein Archive does not allow public registration. New
            accounts are created by an administrator.
        </p>
    </div>
</div>
