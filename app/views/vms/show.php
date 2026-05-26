<section class="page-header">
    <div>
        <p class="eyebrow">Maquina virtual</p>
        <h1><?= e($vm['name']) ?></h1>
    </div>
    <a class="button secondary" href="<?= route_url('/vms/edit&id=' . $vm['id']) ?>">Editar</a>
</section>

<section class="details">
    <article><span>Empresa de uso</span><strong><?= e($vm['company_name'] ?? 'Sem empresa') ?></strong></article>
    <article><span>Servidor fisico</span><strong><?= e($vm['server_name']) ?></strong></article>
    <article><span>Hostname</span><strong><?= e($vm['hostname']) ?></strong></article>
    <article><span>IP</span><strong><?= e($vm['ip_address']) ?></strong></article>
    <article><span>Recursos</span><strong><?= (int)$vm['vcpus'] ?> vCPU, <?= (int)$vm['ram_gb'] ?> GB RAM, <?= (int)$vm['disk_gb'] ?> GB disco</strong></article>
    <article><span>Status</span><strong><?= e($vm['status']) ?></strong></article>
</section>

<?php if (trim($vm['notes'] ?? '') !== ''): ?>
    <section class="panel">
        <h2>Observacoes</h2>
        <p><?= nl2br(e($vm['notes'])) ?></p>
    </section>
<?php endif; ?>
