<main class="login-shell">
    <section class="login-panel">
        <div class="brand login-brand">
            <span class="brand-mark">VM</span>
            <strong><?= e($appName) ?></strong>
        </div>
        <h1>Acesso ao sistema teste de deploy</h1>

        <?php if (!empty($errors['login'])): ?>
            <div class="flash error"><?= e($errors['login']) ?></div>
        <?php endif; ?>

        <form class="form" method="post" action="<?= route_url('/authenticate') ?>">
            <label>E-mail
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autofocus>
            </label>
            <label>Senha
                <input type="password" name="password" required>
            </label>
            <button class="button" type="submit">Entrar</button>
        </form>
    </section>
</main>
