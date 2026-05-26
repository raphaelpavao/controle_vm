<section class="page-header">
    <div>
        <p class="eyebrow">Cadastro</p>
        <h1>Servidores fisicos</h1>
    </div>
    <a class="button" href="<?= route_url('/servers/create') ?>">Novo servidor</a>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Empresa</th>
                    <th>Hostname</th>
                    <th>IP gerencia</th>
                    <th>Local</th>
                    <th>VMs</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servers as $server): ?>
                    <tr>
                        <td><a href="<?= route_url('/servers/show&id=' . $server['id']) ?>"><?= e($server['name']) ?></a></td>
                        <td><?= e($server['company_name'] ?? 'Sem empresa') ?></td>
                        <td><?= e($server['hostname']) ?></td>
                        <td><?= e($server['management_ip']) ?></td>
                        <td><?= e($server['location']) ?></td>
                        <td><?= (int)$server['vm_count'] ?></td>
                        <td class="row-actions">
                            <a href="<?= route_url('/servers/edit&id=' . $server['id']) ?>">Editar</a>
                            <form method="post" action="<?= route_url('/servers/delete') ?>" onsubmit="return confirm('Remover este servidor?');">
                                <input type="hidden" name="id" value="<?= (int)$server['id'] ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($servers === []): ?>
                    <tr><td colspan="7" class="empty">Nenhum servidor fisico cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
