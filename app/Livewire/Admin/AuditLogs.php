<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class AuditLogs extends Component
{
    public string $dateFrom = '';
    public string $actionFilter = '';
    public int $page = 1;
    public int $pageSize = 15;
    public int $totalPages = 1;
    public int $totalFiltered = 0;

    public array $actionMeta = [
        'CREATE' => ['label' => 'CREATE', 'variant' => 'success'],
        'UPDATE' => ['label' => 'UPDATE', 'variant' => 'info'],
        'DELETE' => ['label' => 'DELETE', 'variant' => 'error'],
        'LOGIN'  => ['label' => 'LOGIN',  'variant' => 'neutral'],
    ];

    public function updatedDateFrom(): void    { $this->page = 1; }
    public function updatedActionFilter(): void { $this->page = 1; }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function render()
    {
        // TODO: replace with real AuditLog model query when the model exists.
        // Example:
        // $query = AuditLog::with('actor')
        //     ->when($this->dateFrom,     fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
        //     ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
        //     ->latest();
        //
        // $this->totalFiltered = $query->count();
        // $this->totalPages    = max(1, (int) ceil($this->totalFiltered / $this->pageSize));
        // $paginatedLogs       = $query->forPage($this->page, $this->pageSize)->get()->map(...)->toArray();

        $paginatedLogs = [];
        $this->totalFiltered = 0;
        $this->totalPages = 1;

        return view('livewire.admin.audit-logs', [
            'paginatedLogs' => $paginatedLogs,
            'totalFiltered' => $this->totalFiltered,
            'totalPages'    => $this->totalPages,
            'page'          => $this->page,
            'pageSize'      => $this->pageSize,
            'actionMeta'    => $this->actionMeta,
        ]);
    }
}
