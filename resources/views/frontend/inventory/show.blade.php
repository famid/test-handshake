@extends('frontend.layouts.user_panel')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Inventory') }}</h1>
        </div>
      </div>
    </div>

    <div class="row gutters-10 justify-content-center">
        <div class="col-md-4 mx-auto mb-3" >
            <a  data-toggle="modal" data-target="#add-product-in-warehouse-modal">
              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                      <i class="las la-plus la-3x text-white"></i>
                  </span>
                  <div class="fs-18 text-primary">{{ translate('Add Inventory Product in Warehouse') }}</div>
              </div>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Inventory for Product: ') . $product->name }}</h5>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <form class="" action="" method="GET">
                        <input type="text" class="form-control" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Search Bin Location') }}">
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="md">{{ translate('Batch No.')}}</th>
                        <th data-breakpoints="md">{{ translate('Current Qty')}}</th>
                        <th>{{ translate('Unit Price')}}</th>
                        <th>{{ translate('Variant')}}</th>
                        <th data-breakpoints="md">{{ translate('Purchase Date')}}</th>
                        <th data-breakpoints="md">{{ translate('SKU')}}</th>
                        <th data-breakpoints="md">{{ translate('Bin location')}}</th>
                        <th data-breakpoints="md">{{ translate('Warehouse Owner')}}</th>
                        <th data-breakpoints="md" class="text-right">{{ translate('Options')}}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($inventory as $key => $inventory_product)
                        <tr>
                            <td>{{ $inventory_product->batch_no }}</td>
                            <td>
                                @php
                                    $qty = 0;
                                    foreach ($inventory_product->product->stocks as $key => $stock) {
                                        $qty += $stock->qty;
                                    }
                                    echo $qty;
                                @endphp
                            </td>
                            <td>{{ $inventory_product->price }}</td>
                            <td>{{ $inventory_product->variant }}</td>
                            <td>{{ $inventory_product->purchase_date }}</td>
                            <td>{{ $inventory_product->sku }}</td>
                            <td>{{ $inventory_product->bin_location }}</td>
                            <td>{{ $inventory_product->warehouse_owner->name }}</td>

                            @if(auth()->user()->id == $inventory_product->warehouse_owner_id)
                                <td class="text-right">
                                  <a data-toggle="modal"
                                     data-target="#stock-in-modal-{{ $inventory_product->id }}"
                                     class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                     title="{{ translate('Stock In') }}"
                                  >
                                      <i class="las la-sign-in-alt"></i>
                                  </a>
                                  <a data-toggle="modal"
                                     data-target="#stock-out-modal-{{ $inventory_product->id }}"
                                     class="btn btn-soft-danger btn-icon btn-circle btn-sm"
                                     title="{{ translate('Stock Out') }}"
                                  >
                                      <i class="las la-sign-out-alt"></i>
                                  </a>
                                </td>
                            @else
                                <td class="text-right">
                                    No Access
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $inventory->links() }}
          	</div>
        </div>
    </div>

@endsection

@section('modal')
    @include('modals.add_product_in_warehouse')
    @include('modals.stock_in')
    @include('modals.stock_out')
@endsection

@section('script')
    <script type="text/javascript">
        $('form').bind('submit', function (e) {
            // Disable the submit button while evaluating if the form should be submitted
            $("button[type='submit']").prop('disabled', true);

            var valid = true;

            if (!valid) {
                e.preventDefault();

                // Reactivate the button if the form was not submitted
                $("button[type='submit']").button.prop('disabled', false);
            }
        });

        $("select[name='warehouse_id']").on('change', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('seller.area.get-locations-by-warehouse') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    warehouse_id: $("select[name=warehouse_id]").val()
                },
                dataType: 'JSON',
                success: function (response) {
                    var locationsArr = response.data;
                    var options = '';
                    locationsArr.forEach(function (location, index) {
                        options += '<option value=' + location.id + '>' + location.name + ' (' + location.code + ')</option>';
                    });

                    $("select[name='location_id']").empty();
                    $("select[name='location_id']").append(options);
                    $("select[name='location_id']").selectpicker('refresh');
                    $("select[name='location_id']").trigger('change');
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr);
                }
            });
        });

        $("select[name='location_id']").on('change', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('seller.shelf.get-areas-by-location') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    location_id: $("select[name=location_id]").val()
                },
                dataType: 'JSON',
                success: function (response) {
                    var areasArr = response.data;
                    var options = '';
                    areasArr.forEach(function (area, index) {
                        options += '<option value=' + area.id + '>' + area.name + ' (' + area.code + ')</option>';
                    });

                    $("select[name='area_id']").empty();
                    $("select[name='area_id']").append(options);
                    $("select[name='area_id']").selectpicker('refresh');
                    $("select[name='area_id']").trigger('change');
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr);
                }
            });
        });

        $("select[name='area_id']").on('change', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('seller.cell.get-shelves-by-area') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    area_id: $("select[name=area_id]").val()
                },
                dataType: 'JSON',
                success: function (response) {
                    var shelvesArr = response.data;
                    var options = '';
                    shelvesArr.forEach(function (shelf, index) {
                        options += '<option value=' + shelf.id + '>' + shelf.name + ' (' + shelf.code + ')</option>';
                    });

                    $("select[name='shelf_id']").empty();
                    $("select[name='shelf_id']").append(options);
                    $("select[name='shelf_id']").selectpicker('refresh');
                    $("select[name='shelf_id']").trigger('change');
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr);
                }
            });
        });

        $("select[name='shelf_id']").on('change', function() {
            $.ajax({
                type: "GET",
                url: "{{ route('seller.cell.get-cells-by-shelf') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    area_id: $("select[name=shelf_id]").val()
                },
                dataType: 'JSON',
                success: function (response) {
                    var cellsArr = response.data;
                    var options = '';
                    cellsArr.forEach(function (cell, index) {
                        options += '<option value=' + cell.id + '>' + cell.name + ' (' + cell.code + ')</option>';
                    });

                    $("select[name='cell_id']").empty();
                    $("select[name='cell_id']").append(options);
                    $("select[name='cell_id']").selectpicker('refresh');
                },
                error: function(xhr, textStatus, thrownError) {
                    alert(xhr);
                }
            });
        });
    </script>
@endsection
