<?php

namespace Tests\Unit\Bootstrap5;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Table;
use Tests\Models\Company;
use Tests\Models\User;
use Tests\TestCase;

class ColumnSortableTest extends TestCase
{
    /** @test */
    public function it_cant_sort_any_column_when_no_column_is_sortable(): void
    {
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Id');
                $table->column('Name');
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', null)
            ->assertSet('sortAsc', false)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ])
            ->assertDontSeeHtml([
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort descending"',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
            ]);
    }

    /** @test */
    public function it_can_sort_first_sortable_column_when_no_column_is_sorted_by_default(): void
    {
        Config::set('laravel-table.icon.sort_desc', 'icon-sort-desc');
        Config::set('laravel-table.icon.sort', 'icon-sort');
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Id')->sortable();
                $table->column('Name')->sortable();
            }
        };
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', 'id')
            ->assertSet('sortAsc', true)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort descending"',
                'icon-sort-desc',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ]);
    }

    /** @test */
    public function it_can_sort_asc_column_by_default(): void
    {
        Config::set('laravel-table.icon.sort_desc', 'icon-sort-desc');
        Config::set('laravel-table.icon.sort', 'icon-sort');
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Id')->sortable();
                $table->column('Name')->sortable()->sortByDefault();
            }
        };
        $users = $users->sortBy('name');
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortAsc', true)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort descending"',
                'icon-sort-desc',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ]);
    }

    /** @test */
    public function it_can_sort_desc_column_by_default(): void
    {
        Config::set('laravel-table.icon.sort_asc', 'icon-sort-asc');
        Config::set('laravel-table.icon.sort', 'icon-sort');
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Id')->sortable();
                $table->column('Name')->sortable()->sortByDefault(false);
            }
        };
        $users = $users->sortByDesc('name');
        Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortAsc', false)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
                'icon-sort-asc',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ]);
    }

    /** @test */
    public function it_can_sort_specific_column(): void
    {
        Config::set('laravel-table.icon.sort_asc', 'icon-sort-asc');
        Config::set('laravel-table.icon.sort_desc', 'icon-sort-desc');
        Config::set('laravel-table.icon.sort', 'icon-sort');
        $users = User::factory()->count(2)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Id')->sortable();
                $table->column('Name')->sortable();
            }
        };
        $users = $users->sortBy('name');
        $component = Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', 'id')
            ->assertSet('sortAsc', true)
            ->call('sortBy', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortAsc', true)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort descending"',
                'icon-sort-desc',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ]);
        $users = $users->sortByDesc('name');
        $component->call('sortBy', 'name')
            ->assertSet('sortBy', 'name')
            ->assertSet('sortAsc', false)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'id\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Id',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
                'icon-sort-asc',
                'Name',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->name . '</td>',
                $users->last()->name . '</td>',
                '</tbody>',
            ]);
    }

    /** @test */
    public function it_can_sort_specific_column_from_closure(): void
    {
        Config::set('laravel-table.icon.sort_asc', 'icon-sort-asc');
        Config::set('laravel-table.icon.sort_desc', 'icon-sort-desc');
        Config::set('laravel-table.icon.sort', 'icon-sort');
        $users = User::factory()->count(2)->create();
        Company::factory()->count(6)->create();
        $config = new class extends AbstractTableConfiguration {
            protected function table(Table $table): void
            {
                $table->model(User::class);
            }

            protected function columns(Table $table): void
            {
                $table->column('Name')->sortable();
                $table->column('Companies count')
                    ->format(fn(User $user) => $user->companies->count())
                    ->sortable(fn(Builder $query, bool $sortAsc) => $query->withCount('companies')->orderBy('companies_count'))
                    ->sortByDefault();
            }
        };
        $users = $users->loadCount('companies')->sortBy('companies_count');
        $component = Livewire::test(\Okipa\LaravelTable\Livewire\Table::class, ['config' => $config::class])
            ->call('init')
            ->assertSet('sortBy', 'companies_count')
            ->assertSet('sortAsc', true)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Name',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'companies_count\')"',
                'title="Sort descending"',
                'icon-sort-desc',
                'Companies count',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->companies->count() . '</td>',
                $users->last()->companies->count() . '</td>',
                '</tbody>',
            ]);
        $users = $users->sortByDesc('companies_count');
        $component->call('sortBy', 'companies_count')
            ->assertSet('sortBy', 'companies_count')
            ->assertSet('sortAsc', false)
            ->assertSeeHtmlInOrder([
                '<thead>',
                '<tr',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'name\')"',
                'title="Sort ascending"',
                'icon-sort',
                'Name',
                '</th>',
                '<th class="align-middle" scope="col">',
                '<a wire:click.prevent="sortBy(\'companies_count\')"',
                'title="Sort ascending"',
                'icon-sort-asc',
                'Companies count',
                '</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                $users->first()->companies->count() . '</td>',
                $users->last()->companies->count() . '</td>',
                '</tbody>',
            ]);
    }
}
