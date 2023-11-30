@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">
        @livewire('tables.product-by-category-table', ['category' => $category])
    </div>
</div>
@endsection
