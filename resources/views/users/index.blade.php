@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Users') }}
                    </h3>
                </div>
                <div class="card-actions">
                    <x-actions.create route="{{ route('users.create') }}"/>
                </div>
            </div>
            <div class="card-body">
                <livewire:power-grid.user-table/>
            </div>
        </div>
    </div>
</div>
@endsection
