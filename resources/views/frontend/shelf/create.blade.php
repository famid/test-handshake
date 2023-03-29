@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Shelf')}}</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('seller.shelf.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-12">
                @csrf
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Shelf Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Shelf Name')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" placeholder="Shelf Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Shelf Code')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="code" placeholder="Shelf Code" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Select Warehouse')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="warehouse_id" placeholder="Select Warehouse" data-live-search="true" {{ isset($selectedArea) ? "disabled" : "" }} required>
                                    @if( isset($selectedArea) )
                                        <option value="{{ $selectedArea->location->warehouse->id  }}" selected > {{ $selectedArea->location->warehouse->name . ' (' . $selectedArea->location->warehouse->code . ')' }} </option>
                                    @else
                                        <option value=""> Nothing Selected </option>
                                        @foreach ($warehouses as $key => $warehouse)
                                            <option value="{{ $warehouse->id  }}"> {{ $warehouse->name . ' (' . $warehouse->code . ')' }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Select Location')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="location_id" placeholder="Select Location" data-live-search="true" {{ isset($selectedArea) ? "disabled" : "" }} required>
                                    @isset($selectedArea)
                                        <option value="{{ $selectedArea->location->id  }}" selected > {{ $selectedArea->location->name . ' (' . $selectedArea->location->code . ')' }} </option>
                                    @endisset
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Select Area')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="area_id" placeholder="Select Area" data-live-search="true" {{ isset($selectedArea) ? "disabled" : "" }} required>
                                    @isset($selectedArea)
                                        <option value="{{ $selectedArea->id  }}" selected > {{ $selectedArea->name . ' (' . $selectedArea->code . ')' }} </option>
                                    @endisset
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="btn-toolbar mb-3 justify-content-center" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                        <button type="submit" name="button" value="publish" class="btn btn-success">{{ translate('Create') }}</button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Third group">
                        <button type="button" id="btn_add_cell" value="unpublish" class="btn btn-primary">{{ translate('Add Cell') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')

<script type="text/javascript">

    $(document).ready(function() {
        @if(\Session::has('error'))
            AIZ.plugins.notify('danger', "{{\Session::get('error')}}" );
        @endif
    })

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
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr);
            }
        });
    });

    $('#btn_add_cell').on('click', function() {
        window.location.href = "{{ route('seller.cell.create') }}";
    });

</script>

@endsection
