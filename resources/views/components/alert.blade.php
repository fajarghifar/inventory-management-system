@props([
    // TODO: WIP
])

@if (session('success'))
    <div class="alert alert-success alert-dismissible bg-white" role="alert">
        <h3 class="mb-1">Success</h3>
        <p>{{ session('success') }}</p>

        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible bg-white" role="alert">
        <h3 class="mb-1">Oops...</h3>
        <p>{{ session('error') }}</p>

        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible bg-white" role="alert">
        <h3 class="mb-1">Oops...</h3>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif
