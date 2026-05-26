<section class="page-header">
    <div>
        <p class="eyebrow">Cadastro</p>
        <h1>Empresas</h1>
    </div>
    <a class="button" href="<?= route_url('/companies/create') ?>">Nova empresa</a>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Documento</th>
                    <th>Contato</th>
                    <th>E-mail</th>
                    <th>Servidores</th>
                    <th>VMs</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($companies as $company): ?>
                    <tr>
                        <td><a href="<?= route_url('/companies/show&id=' . $company['id']) ?>"><?= e($company['name']) ?></a></td>
                        <td><?= e($company['document']) ?></td>
                        <td><?= e($company['contact_name']) ?></td>
                        <td><?= e($company['contact_email']) ?></td>
                        <td><?= (int)$company['server_count'] ?></td>
                        <td><?= (int)$company['vm_count'] ?></td>
                        <td class="row-actions">
                            <a href="<?= route_url('/companies/edit&id=' . $company['id']) ?>">Editar</a>
                            <form method="post" action="<?= route_url('/companies/delete') ?>" onsubmit="return confirm('Remover esta empresa?');">
                                <input type="hidden" name="id" value="<?= (int)$company['id'] ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if ($companies === []): ?>
                    <tr><td colspan="7" class="empty">Nenhuma empresa cadastrada.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
