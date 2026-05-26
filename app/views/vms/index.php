<section class="page-header">
    <div>
        <p class="eyebrow">Cadastro</p>
        <h1>Maquinas virtuais</h1>
    </div>
    <a class="button" href="<?= route_url('/vms/create') ?>">Nova VM</a>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Empresa de uso</th>
                    <th>Servidor fisico</th>
                    <th>IP</th>
                    <th>vCPU</th>
                    <th>RAM</th>
                    <th>Disco</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vms as $vm): ?>
                    <tr>
                        <td><a href="<?= route_url('/vms/show&id=' . $vm['id']) ?>"><?= e($vm['name']) ?></a></td>
                        <td><?= e($vm['company_name'] ?? 'Sem empresa') ?></td>
                        <td><?= e($vm['server_name']) ?></td>
                        <td><?= e($vm['ip_address']) ?></td>
                        <td><?= (int)$vm['vcpus'] ?></td>
                        <td><?= (int)$vm['ram_gb'] ?> GB</td>
                        <td><?= (int)$vm['disk_gb'] ?> GB</td>
                        <td><span class="badge"><?= e($vm['status']) ?></span></td>
                        <td class="row-actions">
                            <a href="<?= route_url('/vms/edit&id=' . $vm['id']) ?>">Editar</a>
                            <form method="post" action="<?= route_url('/vms/delete') ?>" onsubmit="return confirm('Remover esta VM?');">
                                <input type="hidden" name="id" value="<?= (int)$vm['id'] ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($vms === []): ?>
                    <tr><td colspan="9" class="empty">Nenhuma VM cadastrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
