<section class="page-header">
    <div>
        <p class="eyebrow">Administracao</p>
        <h1>Inventario de infraestrutura virtual</h1>
    </div>
    <div class="actions">
        <a class="button secondary" href="<?= route_url('/companies/create') ?>">Nova empresa</a>
        <a class="button secondary" href="<?= route_url('/servers/create') ?>">Novo servidor</a>
        <a class="button" href="<?= route_url('/vms/create') ?>">Nova VM</a>
    </div>
</section>

<section class="metrics">
    <article>
        <span>Empresas</span>
        <strong><?= count($companies) ?></strong>
    </article>
    <article>
        <span>Servidores fisicos</span>
        <strong><?= count($servers) ?></strong>
    </article>
    <article>
        <span>VMs cadastradas</span>
        <strong><?= (int)$vmStats['total'] ?></strong>
    </article>
    <article>
        <span>RAM alocada</span>
        <strong><?= (int)$vmStats['ram_gb'] ?> GB</strong>
    </article>
</section>

<section class="panel">
    <div class="panel-heading">
        <h2>Capacidade por servidor</h2>
        <a href="<?= route_url('/servers') ?>">Ver todos</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Servidor</th>
                    <th>Empresa</th>
                    <th>VMs</th>
                    <th>vCPU</th>
                    <th>RAM</th>
                    <th>Disco</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servers as $server): ?>
                    <tr>
                        <td><a href="<?= route_url('/servers/show&id=' . $server['id']) ?>"><?= e($server['name']) ?></a></td>
                        <td><?= e($server['company_name'] ?? 'Sem empresa') ?></td>
                        <td><?= (int)$server['vm_count'] ?></td>
                        <td><?= (int)$server['used_vcpus'] ?> / <?= (int)$server['total_vcpus'] ?></td>
                        <td><?= (int)$server['used_ram_gb'] ?> / <?= (int)$server['total_ram_gb'] ?> GB</td>
                        <td><?= (int)$server['used_disk_gb'] ?> / <?= (int)$server['total_disk_gb'] ?> GB</td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($servers === []): ?>
                    <tr><td colspan="6" class="empty">Nenhum servidor cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
