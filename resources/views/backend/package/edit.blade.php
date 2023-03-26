@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Package Information')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('package.update',$package->id) }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Package Name')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('Name') }}" value="{{ $package->package_name }}" id="name" name="name" class="form-control" required>
                            @if ($errors->has('name'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Package Type')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="package_type" id="" class="form-control aiz-selectpicker" required>
                                <option value="">Select Type</option>
                                <option value="seller" {{ $package->package_type == 'seller' ? 'selected' : '' }}>Seller</option>
                                <option value="vendor" {{ $package->package_type == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Package Price')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" min="0" class="form-control" value="{{ $package->package_price }}" name="package_price" placeholder="{{translate('Package Price')}}" required>
                            @if ($errors->has('package_price'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('package_price') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Product upload limit')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="product_limit" value="{{ $package->product_limit }}" placeholder="{{translate('Product upload limit')}}" required>
                            @if ($errors->has('product_limit'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('product_limit') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Warehouse limit')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="warehouse_limit" value="{{ $package->warehouse_limit }}" placeholder="{{translate('Warehouse limit')}}" required>
                            @if ($errors->has('warehouse_limit'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('warehouse_limit') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Daraz sync limit')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="daraz_sync_limit" value="{{ $package->daraz_sync_limit }}" placeholder="{{translate('Daraz sync limit')}}" required>
                            @if ($errors->has('daraz_sync_limit'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('daraz_sync_limit') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Package Duration')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" name="package_duration" value="{{ $package->package_duration }}" placeholder="{{translate('Package Duration')}}" required>
                            @if ($errors->has('package_duration'))
                                <span class="text-danger" role="alert">
                                    <strong>{{ $errors->first('package_duration') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="additional-info">
                        @if ($package->additional_packages != null)
                            @php
                                $additional = json_decode($package->additional_packages);
                            @endphp

                            @foreach ($additional as $item)
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{translate('Additional Info')}}</label>
                                    <div class="col-md-9">
                                        <div class="row">
                                            <div class="col-11">
                                                <input type="text" name="additional_info[]" value="{{ $item }}" class="form-control" placeholder="Add Additional Info">
                                            </div>
                                            <div class="col-1">
                                                <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger remove-additional">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group row">
                                <div class="col-12">
                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-success add-additional float-right">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{translate('Additional Info')}}</label>
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-11">
                                            <input type="text" name="additional_info[]" class="form-control" placeholder="Add Additional Info">
                                        </div>
                                        <div class="col-1">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-success add-additional">
                                                <i class="las la-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Package Image')}} <small>({{ translate('200x200') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="package_image" value="{{ $package->package_image }}" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Status')}}</label>
                        <div class="col-md-9">
                            <select name="status" required class="form-control aiz-selectpicker mb-2 mb-md-0">
                                <option value="1" {{ $package->status == 1 ? 'selected' : '' }}>{{translate('Active')}}</option>
                                <option value="0" {{ $package->status == 0 ? 'selected' : '' }}>{{translate('Deactive')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <a href="{{ route('package.index') }}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $(document).on('click','.add-additional', function(e){

            var content = `<div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Additional Info')}}</label>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-11">
                                    <input type="text" name="additional_info[]" class="form-control" placeholder="Add Additional Info">
                                </div>
                                <div class="col-1">
                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger remove-additional">
                                        <i class="las la-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`

            $('.additional-info').prepend(content);

        });

        $(document).on('click','.remove-additional', function(e){

            $(this).closest('.form-group').remove();
        });
    </script>
@endsection
