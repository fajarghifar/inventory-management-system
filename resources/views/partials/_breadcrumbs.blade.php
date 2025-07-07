<ol class="breadcrumb breadcrumb-arrows" aria-label="breadcrumbs">
    <!-- Always show Dashboard link -->
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    
    <!-- Dynamic breadcrumbs based on current route -->
    @php
        $segments = request()->segments();
        $url = '';
    @endphp

    @foreach($segments as $segment)
        @php
            $url .= "/$segment";
            $name = ucfirst(str_replace('-', ' ', $segment));
        @endphp

        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
            @if(!$loop->last)
                <a href="{{ $url }}">{{ $name }}</a>
            @else
                {{ $name }}
            @endif
        </li>
    @endforeach
</ol>