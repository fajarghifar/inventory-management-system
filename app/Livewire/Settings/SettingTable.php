<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SettingTable extends PowerGridComponent
{
    public string $tableName = 'setting-table';
    public string $primaryKey = 'key';
    public string $sortField = 'key';
    public string $sortDirection = 'asc';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Setting::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('key')
            ->add('key_label', fn (Setting $model) => Str::title(str_replace('_', ' ', $model->key)))
            ->add('value')
            ->add('value_limited', fn (Setting $model) => Str::limit($model->value, 50));
    }

    public function columns(): array
    {
        return [
            Column::make('Setting Name', 'key_label', 'key')
                ->sortable()
                ->searchable(),

            Column::make('Value', 'value_limited', 'value')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function actions(Setting $row): array
    {
        return [
            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>')
                ->class('bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-md flex items-center justify-center')
                ->dispatch('edit-setting', ['key' => $row->key])
                ->tooltip('Edit Setting')
        ];
    }
}
