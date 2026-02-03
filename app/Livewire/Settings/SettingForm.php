<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class SettingForm extends Component
{
    public ?string $key = null;
    public ?string $value = null;
    public string $label = '';

    public function rules()
    {
        return [
            'value' => ['nullable', 'string'],
        ];
    }

    #[On('edit-setting')]
    public function edit($key)
    {
        $this->resetValidation();
        $setting = Setting::findOrFail($key);

        $this->key = $setting->key;
        $this->value = $setting->value;
        $this->label = Str::title(str_replace('_', ' ', $setting->key));

        $this->dispatch('open-modal', name: 'setting-form-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            Setting::set($this->key, $this->value);

            $this->dispatch('close-modal', name: 'setting-form-modal');
            $this->dispatch('pg:eventRefresh-setting-table');
            $this->dispatch('toast', message: 'Setting updated successfully.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: 'Failed to update setting: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.settings.setting-form');
    }
}
