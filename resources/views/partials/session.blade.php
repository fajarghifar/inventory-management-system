    @if (session()->has('success'))
        <div class="col-12">
            <div class="alert alert-success " role="alert">
                <div class="alert-icon-content">
                    <div class="alert-icon-content">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
