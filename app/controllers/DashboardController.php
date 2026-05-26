<?php

final class DashboardController extends Controller
{
    public function index(): void
    {
        $servers = PhysicalServer::all();
        $vmStats = VirtualMachine::stats();

        $this->view('dashboard/index', [
            'companies' => Company::all(),
            'servers' => $servers,
            'vmStats' => $vmStats,
        ]);
    }

    public function notFound(): void
    {
        $this->view('errors/404');
    }

    public function forbidden(): void
    {
        $this->view('errors/403');
    }
}
