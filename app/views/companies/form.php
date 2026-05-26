<section class="page-header">
    <div>
        <p class="eyebrow">Empresa</p>
        <h1><?= e($title) ?></h1>
    </div>
</section>

<form class="form panel" method="post" action="<?= route_url($action) ?>">
    <?php if (!empty($company['id'])): ?>
        <input type="hidden" name="id" value="<?= (int)$company['id'] ?>">
    <?php endif; ?>

    <div class="grid two">
        <label>Nome
            <input name="name" value="<?= e($company['name'] ?? '') ?>" required>
            <small><?= e($errors['name'] ?? '') ?></small>
        </label>
        <label>Documento
            <input name="document" value="<?= e($company['document'] ?? '') ?>">
            <small><?= e($errors['document'] ?? '') ?></small>
        </label>
        <label>Contato
            <input name="contact_name" value="<?= e($company['contact_name'] ?? '') ?>">
        </label>
        <label>E-mail
            <input type="email" name="contact_email" value="<?= e($company['contact_email'] ?? '') ?>">
            <small><?= e($errors['contact_email'] ?? '') ?></small>
        </label>
        <label>Telefone
            <input name="phone" value="<?= e($company['phone'] ?? '') ?>">
        </label>
    </div>

    <label>Observacoes
        <textarea name="notes" rows="4"><?= e($company['notes'] ?? '') ?></textarea>
    </label>

    <div class="form-actions">
        <a class="button secondary" href="<?= route_url('/companies') ?>">Cancelar</a>
        <button class="button" type="submit">Salvar</button>
    </div>
</form>
