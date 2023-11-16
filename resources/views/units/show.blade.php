@extends('layouts.tabler')


@pushonce('page-styles')
    {{--- ---}}
@endpushonce


@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ $unit->name }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $unit])
    </div>
</div>

<div class="page-body">
    @foreach($unit->products as $products)
        <li>{{ $products->name }}</li>
    @endforeach
</div>
@endsection


@pushonce('page-scripts')
    {{--    --}}
@endpushonce
