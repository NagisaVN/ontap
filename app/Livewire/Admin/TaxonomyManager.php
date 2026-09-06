<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TaxonomyManager extends Component
{
    public ?int $deleteId = null;
    public ?int $editId = null;
    public string $editName = '';

    /**
     * Build the taxonomy tree from the database.
     * TODO: replace with your real Taxonomy / Subject / SubSubject model.
     */
    public function getTree(): array
    {
        // Placeholder structure — swap with DB query when models exist.
        return [
            [
                'id'   => 1,
                'name' => 'Science',
                'children' => [
                    [
                        'id'   => 2,
                        'name' => 'Physics',
                        'children' => [
                            ['id' => 5, 'name' => 'Electromagnetism'],
                            ['id' => 6, 'name' => 'Thermodynamics'],
                        ],
                    ],
                    [
                        'id'   => 3,
                        'name' => 'Chemistry',
                        'children' => [
                            ['id' => 7, 'name' => 'Organic Chemistry'],
                        ],
                    ],
                ],
            ],
            [
                'id'   => 4,
                'name' => 'Mathematics',
                'children' => [
                    [
                        'id'   => 8,
                        'name' => 'Calculus',
                        'children' => [
                            ['id' => 9, 'name' => 'Differential Calculus'],
                            ['id' => 10, 'name' => 'Integral Calculus'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function createMajor(): void
    {
        // TODO: open a modal or inline form to create a top-level Major node.
        session()->flash('status', 'Create major — implement with your Taxonomy model.');
    }

    public function editNode(int $id): void
    {
        $this->editId = $id;
        // TODO: load the node from DB and open an edit modal.
        session()->flash('status', "Edit node #{$id} — implement with your Taxonomy model.");
    }

    public function deleteNode(int $id): void
    {
        // TODO: Taxonomy::findOrFail($id)->delete();
        session()->flash('status', "Deleted node #{$id} — implement with your Taxonomy model.");
    }

    public function addChild(int $parentId, string $name): void
    {
        if (blank($name)) return;
        // TODO: Taxonomy::create(['name' => $name, 'parent_id' => $parentId]);
        session()->flash('status', "Added child '{$name}' under #{$parentId}.");
    }

    public function render()
    {
        return view('livewire.admin.taxonomy-manager', [
            'tree' => $this->getTree(),
        ]);
    }
}
