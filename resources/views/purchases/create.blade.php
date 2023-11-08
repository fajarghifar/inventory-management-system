@extends('layouts.dashboard')

@push('page-styles')
{{--- ---}}
@endpush

@section('content')
<header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
    <div class="container-xl px-4">
        <div class="page-header-content pt-4">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto mt-4">
                    <h1 class="page-header-title">
                        <div class="page-header-icon">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        Add Purchase
                    </h1>
                </div>
            </div>

            @include('partials._breadcrumbs')
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-n10">
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Purchase Details
                    </div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-6">
                                <label for="purchase_date" class="small my-1">
                                    Date
                                    <span class="text-danger">*</span>
                                </label>
                                <input name="purchase_date" id="purchase_date" type="date"
                                       class="form-control form-control-solid example-date-input
                                       @error('purchase_date') is-invalid @enderror"
                                       value="{{ old('purchase_date') ?? now()->format('Y-m-d') }}"
                                       required
                                >

                                @error('purchase_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="small my-1" for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                <select class="form-select form-control-solid @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                    <option selected="" disabled="">Select a supplier:</option>

                                    @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @if(old('supplier_id') == $supplier->id) selected="selected" @endif>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Product List
                    </div>
                    <div class="card-body">
                        <div class="row gx-3 mb-3">
                            <div class="col-md-5">
                                <label class="small my-1" for="category_id">
                                    Category
                                    <span class="text-danger">*</span>
                                </label>

                                <select class="form-select form-control-solid @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                    <option selected="" disabled="">
                                        Select a category:
                                    </option>

                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @if(old('category_id') == $category->id) selected="selected" @endif>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="small my-1" for="product_id">
                                    Product
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-control-solid" id="product_id" name="product_id">
                                    <option disabled>Select a product:</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="small my-1"></label>
                                <button class="btn btn-primary form-control addEventMore" type="button">
                                    Add Product
                                </button>
                            </div>
                        </div>

                        <div class="gx-3 table-responsive">
                            <table class="table align-middle">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody id="addRow" class="addRow"></tbody>

                                <tbody>
                                    <tr class="table-primary">
                                        <td colspan="3"></td>
                                        <td>
                                            <label for="total_amount" class="visually-hidden"></label>
                                            <input type="text" name="total_amount" id="total_amount" class="form-control total_amount" value="0" readonly>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-outline-success" onclick="return confirm('Are you sure you want to purchase?')">
                                                Purchase Store
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@pushonce('page-scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{ asset('assets/js/handlebars.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js" ></script>

<script id="document-template" type="text/x-handlebars-template">
    <tr class="delete_add_more_item" id="delete_add_more_item">
        <td>
            <input type="hidden" name="product_id[]" value="@{{product_id}}" required>
            @{{product_name}}
        </td>

        <td>
            <input type="number" min="1" class="form-control quantity" name="quantity[]" value="" required>
        </td>

        <td>
            <input type="number" class="form-control unitcost" name="unitcost[]" value="" required>
        </td>

        <td>
            <input type="number" class="form-control total" name="total[]" value="0" readonly>
        </td>

        <td>
            <button class="btn btn-danger removeEventMore" type="button">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click",".addEventMore", function() {
            const product_id = $('#product_id').val();
            const product_name = $('#product_id').find('option:selected').text();

            if(product_id == ''){
                $.notify("Product Field is Required" ,  {globalPosition: 'top right', className:'error' });
                return false;
            }

            const source = $("#document-template").html();
            const tamplate = Handlebars.compile(source);
            const data = {
                product_id: product_id,
                product_name: product_name

            };
            const html = tamplate(data);
            $("#addRow").append(html);
        });

        $(document).on("click",".removeEventMore",function(event){
            $(this).closest(".delete_add_more_item").remove();
            totalAmountPrice();
        });

        $(document).on('keyup click','.unitcost,.quantity', function(){
            const unitcost = $(this).closest("tr").find("input.unitcost").val();
            const quantity = $(this).closest("tr").find("input.quantity").val();
            const total = unitcost * quantity;
            $(this).closest("tr").find("input.total").val(total);
            totalAmountPrice();
        });


        // Calculate sum of amout in invoice
        function totalAmountPrice(){
            let sum = 0;
            $(".total").each(function(){
                const value = $(this).val();
                if(!isNaN(value) && value.length !== 0){
                    sum += parseFloat(value);
                }
            });
            $('#total_amount').val(sum);
        }
    });
</script>

<!-- Get Products by category -->
<script type="text/javascript">
    $(function(){
        $(document).on('change','#category_id',function(){
            const category_id = $(this).val();

            $.ajax({
                url:"{{ route('api.product.index') }}",
                type: "GET",
                data:{category_id:category_id},
                success:function(data){
                    let html = '';
                    $.each(data,function(key,v){
                        html += '<option value=" '+v.id+' "> '+v.name+'</option>';
                    });
                    $('#product_id').html(html);
                }
            })
        });
    });
</script>
@endpushonce
