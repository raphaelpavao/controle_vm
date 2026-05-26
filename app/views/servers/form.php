<section class="page-header">
    <div>
        <p class="eyebrow">Servidor fisico</p>
        <h1><?= e($title) ?></h1>
    </div>
</section>

<?php if ($companies === []): ?>
    <div class="flash error">Cadastre uma empresa antes de criar servidores.</div>
<?php endif; ?>

<form class="form panel" method="post" action="<?= route_url($action) ?>">
    <?php if (!empty($server['id'])): ?>
        <input type="hidden" name="id" value="<?= (int)$server['id'] ?>">
    <?php endif; ?>

    <div class="grid two">
        <label>Empresa proprietaria
            <select name="company_id" required>
                <option value="">Selecione</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= (int)$company['id'] ?>" <?= (int)($server['company_id'] ?? 0) === (int)$company['id'] ? 'selected' : '' ?>>
                        <?= e($company['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small><?= e($errors['company_id'] ?? '') ?></small>
        </label>
        <label>Nome
            <input name="name" value="<?= e($server['name'] ?? '') ?>" required>
            <small><?= e($errors['name'] ?? '') ?></small>
        </label>
        <label>Hostname
            <input name="hostname" value="<?= e($server['hostname'] ?? '') ?>" required>
            <small><?= e($errors['hostname'] ?? '') ?></small>
        </label>
        <label>IP de gerenciamento
            <input name="management_ip" value="<?= e($server['management_ip'] ?? '') ?>" required>
            <small><?= e($errors['management_ip'] ?? '') ?></small>
        </label>
        <label>Localizacao
            <input name="location" value="<?= e($server['location'] ?? '') ?>">
        </label>
        <label>Modelo de CPU
            <input name="cpu_model" value="<?= e($server['cpu_model'] ?? '') ?>">
        </label>
        <label>Total de vCPUs
            <input type="number" min="0" name="total_vcpus" value="<?= e($server['total_vcpus'] ?? '0') ?>">
        </label>
        <label>RAM total (GB)
            <input type="number" min="0" name="total_ram_gb" value="<?= e($server['total_ram_gb'] ?? '0') ?>">
        </label>
        <label>Disco total (GB)
            <input type="number" min="0" name="total_disk_gb" value="<?= e($server['total_disk_gb'] ?? '0') ?>">
        </label>
    </div>

    <label>Observacoes
        <textarea name="notes" rows="4"><?= e($server['notes'] ?? '') ?></textarea>
    </label>

    <div class="form-actions">
        <a class="button secondary" href="<?= route_url('/servers') ?>">Cancelar</a>
        <button class="button" type="submit" <?= $companies === [] ? 'disabled' : '' ?>>Salvar</button>
    </div>
</form>
