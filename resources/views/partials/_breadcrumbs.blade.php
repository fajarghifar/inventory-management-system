{{---
<nav class="mt-4 rounded" aria-label="breadcrumb">
    <ol class="breadcrumb px-3 py-2 rounded mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Categories</li>

        @foreach(request()->breadcrumbs()->segments() as $segment)
            <li class="breadcrumb-item">
                <a href="{{ $segment->url() }}">
                    {{ optional($segment->model())->title ?: $segment->name() }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
---}}

<ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
    @foreach(request()->breadcrumbs()->segments() as $segment)
        <li class="breadcrumb-item">
            <a href="{{ $segment->url() }}">
                {{ optional($segment->model())->title ?: $segment->name() }}
            </a>
        </li>
    @endforeach
</ol>
