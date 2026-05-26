<?php

final class VmController extends Controller
{
    public function index(): void
    {
        $this->view('vms/index', ['vms' => VirtualMachine::all()]);
    }

    public function create(array $errors = [], array $vm = []): void
    {
        $this->view('vms/form', [
            'title' => 'Nova VM',
            'action' => '/vms/store',
            'vm' => $vm,
            'servers' => PhysicalServer::all(),
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
            VirtualMachine::create($_POST);
            $this->flash('success', 'VM cadastrada com sucesso.');
            $this->redirect('/vms');
        } catch (RuntimeException) {
            $this->create(['ip_address' => 'Este IP ja esta cadastrado para outra VM.'], $_POST);
        }
    }

    public function show(): void
    {
        $this->view('vms/show', ['vm' => $this->findOrRedirect()]);
    }

    public function edit(array $errors = [], ?array $vm = null): void
    {
        $vm ??= $this->findOrRedirect();
        $this->view('vms/form', [
            'title' => 'Editar VM',
            'action' => '/vms/update',
            'vm' => $vm,
            'servers' => PhysicalServer::all(),
            'companies' => Company::all(),
            'errors' => $errors,
        ]);
    }

    public function update(): void
    {
        $id = $this->idFromRequest();
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            $vm = array_merge($_POST, ['id' => $id]);
            $this->edit($errors, $vm);
            return;
        }

        try {
            VirtualMachine::update($id, $_POST);
            $this->flash('success', 'VM atualizada.');
            $this->redirect('/vms/show&id=' . $id);
        } catch (RuntimeException) {
            $vm = array_merge($_POST, ['id' => $id]);
            $this->edit(['ip_address' => 'Este IP ja esta cadastrado para outra VM.'], $vm);
        }
    }

    public function delete(): void
    {
        VirtualMachine::delete($this->idFromRequest());
        $this->flash('success', 'VM removida.');
        $this->redirect('/vms');
    }

    private function validate(array $data): array
    {
        $errors = [];

        foreach (['company_id' => 'Empresa de uso', 'name' => 'Nome', 'hostname' => 'Hostname', 'ip_address' => 'IP', 'physical_server_id' => 'Servidor fisico'] as $field => $label) {
            if (trim((string)($data[$field] ?? '')) === '') {
                $errors[$field] = $label . ' e obrigatorio.';
            }
        }

        if (($data['ip_address'] ?? '') !== '' && filter_var($data['ip_address'], FILTER_VALIDATE_IP) === false) {
            $errors['ip_address'] = 'Informe um IP valido.';
        }

        if (!PhysicalServer::find((int)($data['physical_server_id'] ?? 0))) {
            $errors['physical_server_id'] = 'Selecione um servidor fisico valido.';
        }

        if (!Company::find((int)($data['company_id'] ?? 0))) {
            $errors['company_id'] = 'Selecione uma empresa valida.';
        }

        return $errors;
    }

    private function findOrRedirect(): array
    {
        $vm = VirtualMachine::find($this->idFromRequest());
        if ($vm === null) {
            $this->flash('error', 'VM nao encontrada.');
            $this->redirect('/vms');
        }

        return $vm;
    }
}
