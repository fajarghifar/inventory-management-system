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
    @elseif (session()->has('error'))
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <div class="alert-icon-content">
                    <div class="alert-icon-content">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        </div>
    @endif
