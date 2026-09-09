<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.app')]
class AuditLogs extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $dateFrom     = '';
    public string $actionFilter = '';
    public int    $perPage      = 6;

    protected $queryString = [
        'search'       => ['except' => ''],
        'dateFrom'     => ['except' => ''],
        'actionFilter' => ['except' => ''],
    ];

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingDateFrom(): void     { $this->resetPage(); }
    public function updatingActionFilter(): void { $this->resetPage(); }

    // ── Query logs — called in render() so pagination works correctly ────────
    private function queryLogs()
    {
        return Activity::with(['causer', 'subject'])
            ->when($this->search, fn($q) =>
                $q->where(fn($inner) =>
                    $inner->where('description', 'like', "%{$this->search}%")
                           ->orWhereHasMorph('causer', [\App\Models\User::class], fn($u) =>
                               $u->where('name',  'like', "%{$this->search}%")
                                 ->orWhere('email', 'like', "%{$this->search}%")
                           )
                )
            )
            ->when($this->actionFilter && $this->actionFilter !== 'all', fn($q) =>
                $q->where('event', $this->actionFilter)
            )
            ->when($this->dateFrom, fn($q) =>
                $q->whereDate('created_at', '>=', $this->dateFrom)
            )
            ->latest()
            ->paginate($this->perPage)
            ->through(fn($log) => $this->formatLog($log));
    }

    public function formatLog(Activity $log): array
    {
        $causerName   = $log->causer?->name  ?? 'System';
        $causerEmail  = $log->causer?->email ?? '';
        $subjectType  = class_basename($log->subject_type ?? 'Unknown');
        $subjectLabel = $log->subject?->name
                      ?? $log->subject?->email
                      ?? "#{$log->subject_id}";

        $changes = [];
        $props   = $log->properties;

        if ($props->has('old') && $props->has('attributes')) {
            foreach ($props->get('attributes', []) as $field => $newVal) {
                $oldVal = $props->get('old')[$field] ?? null;
                if ($oldVal !== $newVal) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'from'  => $this->mask($field, $oldVal),
                        'to'    => $this->mask($field, $newVal),
                    ];
                }
            }
        }
        if ($props->has('new') && $props->has('old') && !$props->has('attributes')) {
            foreach ($props->get('new', []) as $field => $newVal) {
                $oldVal = $props->get('old')[$field] ?? null;
                $changes[] = [
                    'field' => ucfirst(str_replace('_', ' ', $field)),
                    'from'  => $this->mask($field, $oldVal),
                    'to'    => $this->mask($field, $newVal),
                ];
            }
        }

        return [
            'id'              => $log->id,
            'causer_name'     => $causerName,
            'causer_email'    => $causerEmail,
            'event'           => $log->event ?? 'action',
            'description'     => $log->description,
            'subject_type'    => $subjectType,
            'subject_label'   => $subjectLabel,
            'changes'         => $changes,
            'created_at'      => $log->created_at->format('d/m H:i:s'),
            'diff_for_humans' => $log->created_at->diffForHumans(),
        ];
    }

    private function mask(string $field, mixed $value): string
    {
        return in_array($field, ['password', 'remember_token']) ? '........' : (string)($value ?? '-');
    }

    public function exportLogs(): void
    {
        session()->flash('info', 'Export feature coming soon.');
    }

    // ── render() fetches data and passes it to the view ────────────────────
    public function render()
    {
        return view('livewire.admin.audit-logs', [
            'logs' => $this->queryLogs(),
        ]);
    }
}