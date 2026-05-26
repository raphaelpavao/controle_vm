<section class="page-header">
    <div>
        <p class="eyebrow">Maquina virtual</p>
        <h1><?= e($title) ?></h1>
    </div>
</section>

<?php if ($servers === [] || $companies === []): ?>
    <div class="flash error">Cadastre ao menos uma empresa e um servidor fisico antes de criar VMs.</div>
<?php endif; ?>

<form class="form panel" method="post" action="<?= route_url($action) ?>">
    <?php if (!empty($vm['id'])): ?>
        <input type="hidden" name="id" value="<?= (int)$vm['id'] ?>">
    <?php endif; ?>

    <div class="grid two">
        <label>Empresa de uso
            <select name="company_id" required>
                <option value="">Selecione</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= (int)$company['id'] ?>" <?= (int)($vm['company_id'] ?? 0) === (int)$company['id'] ? 'selected' : '' ?>>
                        <?= e($company['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small><?= e($errors['company_id'] ?? '') ?></small>
        </label>
        <label>Servidor fisico
            <select name="physical_server_id" required>
                <option value="">Selecione</option>
                <?php foreach ($servers as $server): ?>
                    <option value="<?= (int)$server['id'] ?>" <?= (int)($vm['physical_server_id'] ?? 0) === (int)$server['id'] ? 'selected' : '' ?>>
                        <?= e($server['name']) ?> (<?= e($server['company_name'] ?? 'Sem empresa') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small><?= e($errors['physical_server_id'] ?? '') ?></small>
        </label>
        <label>Status
            <select name="status">
                <?php foreach (['ativa' => 'Ativa', 'desligada' => 'Desligada', 'manutencao' => 'Manutencao', 'desativada' => 'Desativada'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($vm['status'] ?? 'ativa') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Nome
            <input name="name" value="<?= e($vm['name'] ?? '') ?>" required>
            <small><?= e($errors['name'] ?? '') ?></small>
        </label>
        <label>Hostname
            <input name="hostname" value="<?= e($vm['hostname'] ?? '') ?>" required>
            <small><?= e($errors['hostname'] ?? '') ?></small>
        </label>
        <label>IP da VM
            <input name="ip_address" value="<?= e($vm['ip_address'] ?? '') ?>" required>
            <small><?= e($errors['ip_address'] ?? '') ?></small>
        </label>
        <label>vCPUs
            <input type="number" min="1" name="vcpus" value="<?= e($vm['vcpus'] ?? '1') ?>">
        </label>
        <label>RAM (GB)
            <input type="number" min="1" name="ram_gb" value="<?= e($vm['ram_gb'] ?? '1') ?>">
        </label>
        <label>Disco (GB)
            <input type="number" min="1" name="disk_gb" value="<?= e($vm['disk_gb'] ?? '10') ?>">
        </label>
    </div>

    <label>Observacoes
        <textarea name="notes" rows="4"><?= e($vm['notes'] ?? '') ?></textarea>
    </label>

    <div class="form-actions">
        <a class="button secondary" href="<?= route_url('/vms') ?>">Cancelar</a>
        <button class="button" type="submit" <?= ($servers === [] || $companies === []) ? 'disabled' : '' ?>>Salvar</button>
    </div>
</form>
