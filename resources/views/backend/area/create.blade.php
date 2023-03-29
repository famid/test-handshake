@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Area')}}</h5>
</div>
<div class="">
    <form class="form form-horizontal mar-top" action="{{route('area.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-12">
                @csrf
                <input type="hidden" name="added_by" value="admin">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Area Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Area Name')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="name" placeholder="Area Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Area Code')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="code" placeholder="Area Code" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Select Warehouse')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="warehouse_id" placeholder="Select Warehouse" data-live-search="true" {{ isset($selectedLocation) ? "disabled" : "" }} required>
                                    @if( isset($selectedLocation) )
                                        <option value="{{ $selectedLocation->warehouse->id  }}" selected > {{ $selectedLocation->warehouse->name . ' (' . $selectedLocation->warehouse->code . ')' }} </option>
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
                                <select class="form-control aiz-selectpicker" name="location_id" placeholder="Select Location" data-live-search="true" {{ isset($selectedLocation) ? "disabled" : "" }} required>
                                    @isset($selectedLocation)
                                        <option value="{{ $selectedLocation->id  }}" selected > {{ $selectedLocation->name . ' (' . $selectedLocation->code . ')' }} </option>
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
                        <button type="button" id="btn_add_shelf" value="unpublish" class="btn btn-primary">{{ translate('Add Shelf') }}</button>
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
            url: "{{ route('area.get-locations-by-warehouse') }}",
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
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr);
            }
        });
    });

    $('#btn_add_shelf').on('click', function() {
        window.location.href = "{{ route('shelf.create') }}";
    });

</script>

@endsection
