<section class="page-header">
    <div>
        <p class="eyebrow">Servidor fisico</p>
        <h1><?= e($server['name']) ?></h1>
    </div>
    <div class="actions">
        <a class="button secondary" href="<?= route_url('/servers/edit&id=' . $server['id']) ?>">Editar</a>
        <a class="button" href="<?= route_url('/vms/create') ?>">Nova VM</a>
    </div>
</section>

<section class="details">
    <article><span>Empresa proprietaria</span><strong><?= e($server['company_name'] ?? 'Sem empresa') ?></strong></article>
    <article><span>Hostname</span><strong><?= e($server['hostname']) ?></strong></article>
    <article><span>IP gerencia</span><strong><?= e($server['management_ip']) ?></strong></article>
    <article><span>Local</span><strong><?= e($server['location']) ?></strong></article>
    <article><span>CPU</span><strong><?= e($server['cpu_model']) ?></strong></article>
    <article><span>Capacidade</span><strong><?= (int)$server['total_vcpus'] ?> vCPU, <?= (int)$server['total_ram_gb'] ?> GB RAM, <?= (int)$server['total_disk_gb'] ?> GB disco</strong></article>
</section>

<section class="panel">
    <div class="panel-heading">
        <h2>VMs vinculadas</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Empresa de uso</th>
                    <th>IP</th>
                    <th>vCPU</th>
                    <th>RAM</th>
                    <th>Disco</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vms as $vm): ?>
                    <tr>
                        <td><a href="<?= route_url('/vms/show&id=' . $vm['id']) ?>"><?= e($vm['name']) ?></a></td>
                        <td><?= e(Company::label((int)($vm['company_id'] ?? 0))) ?></td>
                        <td><?= e($vm['ip_address']) ?></td>
                        <td><?= (int)$vm['vcpus'] ?></td>
                        <td><?= (int)$vm['ram_gb'] ?> GB</td>
                        <td><?= (int)$vm['disk_gb'] ?> GB</td>
                        <td><span class="badge"><?= e($vm['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($vms === []): ?>
                    <tr><td colspan="7" class="empty">Nenhuma VM vinculada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
