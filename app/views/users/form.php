<section class="page-header">
    <div>
        <p class="eyebrow">Usuario</p>
        <h1><?= e($title) ?></h1>
    </div>
</section>

<form class="form panel" method="post" action="<?= route_url($action) ?>">
    <?php if (!empty($user['id'])): ?>
        <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
    <?php endif; ?>

    <div class="grid two">
        <label>Nome
            <input name="name" value="<?= e($user['name'] ?? '') ?>" required>
            <small><?= e($errors['name'] ?? '') ?></small>
        </label>
        <label>E-mail
            <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required>
            <small><?= e($errors['email'] ?? '') ?></small>
        </label>
        <label>Senha <?= $passwordRequired ? '' : '(deixe em branco para manter)' ?>
            <input type="password" name="password" <?= $passwordRequired ? 'required' : '' ?>>
            <small><?= e($errors['password'] ?? '') ?></small>
        </label>
        <label>Perfil
            <select name="role">
                <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>Usuario</option>
                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
            </select>
            <small><?= e($errors['role'] ?? '') ?></small>
        </label>
        <input type="hidden" name="active" value="0">
        <label class="checkbox-line">
            <input type="checkbox" name="active" value="1" <?= array_key_exists('active', $user) ? (!empty($user['active']) ? 'checked' : '') : 'checked' ?>>
            Ativo
        </label>
    </div>

    <div class="form-actions">
        <a class="button secondary" href="<?= route_url('/users') ?>">Cancelar</a>
        <button class="button" type="submit">Salvar</button>
    </div>
</form>
