<?php

final class UserController extends Controller
{
    public function index(): void
    {
        $this->view('users/index', ['users' => User::all()]);
    }

    public function create(array $errors = [], array $user = []): void
    {
        $this->view('users/form', [
            'title' => 'Novo usuario',
            'action' => '/users/store',
            'user' => $user,
            'errors' => $errors,
            'passwordRequired' => true,
        ]);
    }

    public function store(): void
    {
        $errors = $this->validate($_POST, true);
        if ($errors !== []) {
            $this->create($errors, $_POST);
            return;
        }

        try {
            User::create($_POST);
            $this->flash('success', 'Usuario cadastrado com sucesso.');
            $this->redirect('/users');
        } catch (RuntimeException) {
            $this->create(['email' => 'Este e-mail ja esta cadastrado.'], $_POST);
        }
    }

    public function edit(array $errors = [], ?array $user = null): void
    {
        $user ??= $this->findOrRedirect();
        $this->view('users/form', [
            'title' => 'Editar usuario',
            'action' => '/users/update',
            'user' => $user,
            'errors' => $errors,
            'passwordRequired' => false,
        ]);
    }

    public function update(): void
    {
        $id = $this->idFromRequest();
        $errors = $this->validate($_POST, false);
        if ($errors !== []) {
            $user = array_merge($_POST, ['id' => $id]);
            $this->edit($errors, $user);
            return;
        }

        try {
            User::update($id, $_POST);
            $this->flash('success', 'Usuario atualizado.');
            $this->redirect('/users');
        } catch (RuntimeException) {
            $user = array_merge($_POST, ['id' => $id]);
            $this->edit(['email' => 'Este e-mail ja esta cadastrado.'], $user);
        }
    }

    public function delete(): void
    {
        if (User::delete($this->idFromRequest())) {
            $this->flash('success', 'Usuario removido.');
        } else {
            $this->flash('error', 'Nao foi possivel remover este usuario.');
        }

        $this->redirect('/users');
    }

    private function validate(array $data, bool $passwordRequired): array
    {
        $errors = [];

        if (trim($data['name'] ?? '') === '') {
            $errors['name'] = 'Nome e obrigatorio.';
        }

        if (trim($data['email'] ?? '') === '') {
            $errors['email'] = 'E-mail e obrigatorio.';
        } elseif (filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Informe um e-mail valido.';
        }

        if ($passwordRequired && trim($data['password'] ?? '') === '') {
            $errors['password'] = 'Senha e obrigatoria.';
        }

        if (trim($data['password'] ?? '') !== '' && strlen((string)$data['password']) < 6) {
            $errors['password'] = 'Use pelo menos 6 caracteres.';
        }

        if (!in_array($data['role'] ?? 'user', ['admin', 'user'], true)) {
            $errors['role'] = 'Perfil invalido.';
        }

        return $errors;
    }

    private function findOrRedirect(): array
    {
        $user = User::find($this->idFromRequest());
        if ($user === null) {
            $this->flash('error', 'Usuario nao encontrado.');
            $this->redirect('/users');
        }

        return $user;
    }
}
