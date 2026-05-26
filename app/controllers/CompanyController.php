<?php

final class CompanyController extends Controller
{
    public function index(): void
    {
        $this->view('companies/index', ['companies' => Company::all()]);
    }

    public function create(array $errors = [], array $company = []): void
    {
        $this->view('companies/form', [
            'title' => 'Nova empresa',
            'action' => '/companies/store',
            'company' => $company,
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
            Company::create($_POST);
            $this->flash('success', 'Empresa cadastrada com sucesso.');
            $this->redirect('/companies');
        } catch (RuntimeException) {
            $this->create(['document' => 'Este documento ja esta cadastrado.'], $_POST);
        }
    }

    public function show(): void
    {
        $this->view('companies/show', ['company' => $this->findOrRedirect()]);
    }

    public function edit(array $errors = [], ?array $company = null): void
    {
        $company ??= $this->findOrRedirect();
        $this->view('companies/form', [
            'title' => 'Editar empresa',
            'action' => '/companies/update',
            'company' => $company,
            'errors' => $errors,
        ]);
    }

    public function update(): void
    {
        $id = $this->idFromRequest();
        $errors = $this->validate($_POST);
        if ($errors !== []) {
            $company = array_merge($_POST, ['id' => $id]);
            $this->edit($errors, $company);
            return;
        }

        try {
            Company::update($id, $_POST);
            $this->flash('success', 'Empresa atualizada.');
            $this->redirect('/companies/show&id=' . $id);
        } catch (RuntimeException) {
            $company = array_merge($_POST, ['id' => $id]);
            $this->edit(['document' => 'Este documento ja esta cadastrado.'], $company);
        }
    }

    public function delete(): void
    {
        $id = $this->idFromRequest();
        if (Company::delete($id)) {
            $this->flash('success', 'Empresa removida.');
        } else {
            $this->flash('error', 'Nao e possivel remover uma empresa vinculada a servidores ou VMs.');
        }

        $this->redirect('/companies');
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (trim($data['name'] ?? '') === '') {
            $errors['name'] = 'Nome e obrigatorio.';
        }

        if (($data['contact_email'] ?? '') !== '' && filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['contact_email'] = 'Informe um e-mail valido.';
        }

        return $errors;
    }

    private function findOrRedirect(): array
    {
        $company = Company::find($this->idFromRequest());
        if ($company === null) {
            $this->flash('error', 'Empresa nao encontrada.');
            $this->redirect('/companies');
        }

        return $company;
    }
}
