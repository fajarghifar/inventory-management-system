@props([
    'th',
    'tbody'
])


<div class="table-responsive">
    <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
        <thead class="thead-light">
            <tr>
                {{ $th }}
            </tr>
        </thead>
        <tbody>
            {{ $tbody }}
        </tbody>
    </table>
</div>
