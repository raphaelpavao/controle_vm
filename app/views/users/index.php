<section class="page-header">
    <div>
        <p class="eyebrow">Acesso</p>
        <h1>Usuarios</h1>
    </div>
    <a class="button" href="<?= route_url('/users/create') ?>">Novo usuario</a>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Acoes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role'] === 'admin' ? 'Administrador' : 'Usuario') ?></td>
                        <td><span class="badge"><?= !empty($user['active']) ? 'ativo' : 'inativo' ?></span></td>
                        <td class="row-actions">
                            <a href="<?= route_url('/users/edit&id=' . $user['id']) ?>">Editar</a>
                            <form method="post" action="<?= route_url('/users/delete') ?>" onsubmit="return confirm('Remover este usuario?');">
                                <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
                                <button type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
