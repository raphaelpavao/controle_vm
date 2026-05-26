<?php

final class ServerController extends Controller
{
    public function index(): void
    {
        $this->view('servers/index', ['servers' => PhysicalServer::all()]);
    }

    public function create(array $errors = [], array $server = []): void
    {
        $this->view('servers/form', [
            'title' => 'Novo servidor fisico',
            'action' => '/servers/store',
            'server' => $server,
            'companies' => Company::all(),
            'errors' => $errors,
        ]);
    }

    public function store(): void
    {
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            $this->create($errors, $_POST);
            return;
        }

        try {
            PhysicalServer::create($_POST);
            $this->flash('success', 'Servidor fisico cadastrado com sucesso.');
            $this->redirect('/servers');
        } catch (RuntimeException) {
            $this->create(['management_ip' => 'Este IP de gerenciamento ja esta cadastrado.'], $_POST);
        }
    }

    public function show(): void
    {
        $server = $this->findOrRedirect();
        $this->view('servers/show', [
            'server' => $server,
            'vms' => PhysicalServer::vms((int)$server['id']),
        ]);
    }

    public function edit(array $errors = [], ?array $server = null): void
    {
        $server ??= $this->findOrRedirect();
        $this->view('servers/form', [
            'title' => 'Editar servidor fisico',
            'action' => '/servers/update',
            'server' => $server,
            'companies' => Company::all(),
            'errors' => $errors,
        ]);
    }

    public function update(): void
    {
        $id = $this->idFromRequest();
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            $server = array_merge($_POST, ['id' => $id]);
            $this->edit($errors, $server);
            return;
        }

        try {
            PhysicalServer::update($id, $_POST);
            $this->flash('success', 'Servidor fisico atualizado.');
            $this->redirect('/servers/show&id=' . $id);
        } catch (RuntimeException) {
            $server = array_merge($_POST, ['id' => $id]);
            $this->edit(['management_ip' => 'Este IP de gerenciamento ja esta cadastrado.'], $server);
        }
    }

    public function delete(): void
    {
        $id = $this->idFromRequest();
        if (PhysicalServer::delete($id)) {
            $this->flash('success', 'Servidor fisico removido.');
        } else {
            $this->flash('error', 'Nao e possivel remover um servidor que possui VMs vinculadas.');
        }

        $this->redirect('/servers');
    }

    private function validate(array $data): array
    {
        $errors = [];

        foreach (['company_id' => 'Empresa', 'name' => 'Nome', 'hostname' => 'Hostname', 'management_ip' => 'IP de gerenciamento'] as $field => $label) {
            if (trim((string)($data[$field] ?? '')) === '') {
                $errors[$field] = $label . ' e obrigatorio.';
            }
        }

        if (($data['management_ip'] ?? '') !== '' && filter_var($data['management_ip'], FILTER_VALIDATE_IP) === false) {
            $errors['management_ip'] = 'Informe um IP valido.';
        }

        if (!Company::find((int)($data['company_id'] ?? 0))) {
            $errors['company_id'] = 'Selecione uma empresa valida.';
        }

        return $errors;
    }

    private function findOrRedirect(): array
    {
        $server = PhysicalServer::find($this->idFromRequest());
        if ($server === null) {
            $this->flash('error', 'Servidor fisico nao encontrado.');
            $this->redirect('/servers');
        }

        return $server;
    }
}
