@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        @livewire('tables.user-table')
    </div>
</div>
@endsection
