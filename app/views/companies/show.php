<section class="page-header">
    <div>
        <p class="eyebrow">Empresa</p>
        <h1><?= e($company['name']) ?></h1>
    </div>
    <a class="button secondary" href="<?= route_url('/companies/edit&id=' . $company['id']) ?>">Editar</a>
</section>

<section class="details">
    <article><span>Documento</span><strong><?= e($company['document']) ?></strong></article>
    <article><span>Contato</span><strong><?= e($company['contact_name']) ?></strong></article>
    <article><span>E-mail</span><strong><?= e($company['contact_email']) ?></strong></article>
    <article><span>Telefone</span><strong><?= e($company['phone']) ?></strong></article>
</section>

<?php if (trim($company['notes'] ?? '') !== ''): ?>
    <section class="panel">
        <h2>Observacoes</h2>
        <p><?= nl2br(e($company['notes'])) ?></p>
    </section>
<?php endif; ?>
