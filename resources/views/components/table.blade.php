@props([
    'thead',
])

<div class="table-responsive">
    <table class="table card-table table-vcenter text-nowrap datatable">
        <thead>
            <tr>
                {{ $thead }}
            </tr>
        </thead>

        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>
