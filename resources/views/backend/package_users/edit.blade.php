@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add user to package')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="#" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('User')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <select name="user_id" id="user" class="form-control aiz-selectpicker" data-live-search="true">
                                <option value="">Select User</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ $package_user->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Package')}} <span class="text-danger">*</span>
                            <small>(Select user first)</small>
                        </label>
                        <div class="col-md-9">
                            <select name="package_id" id="package" class="form-control aiz-selectpicker" data-live-search="true" required>
                                <option value="">Select Package</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}" {{ $package_user->package_id == $package->id ? 'selected' : '' }}>{{ $package->package_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Subscription Start Date')}} <span class="text-danger">*</span></label>
                        <div class="col-md-9">
                            <input type="date" value="{{ date('d-M-Y',strtotime($package_user->subscription_start)) }}" class="form-control">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{translate('Status')}}</label>
                        <div class="col-md-9">
                            <select name="status" required class="form-control aiz-selectpicker mb-2 mb-md-0">
                                <option value="1" {{ $package_user->status == 1 ? 'selected' : '' }}>{{translate('Active')}}</option>
                                <option value="0" {{ $package_user->status == 0 ? 'selected' : '' }}>{{translate('Deactive')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <a href="{{ route('package-users.index') }}" class="btn btn-danger">Cancel</a>
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
        $('#user').change(function (e) {
            e.preventDefault();

            var user_id = $(this).val();
            var url = '{{ route("package_by_user", ":id") }}';
            url = url.replace(':id', user_id);

            $.ajax({
                type: "GET",
                url: url,
                success: function (response) {

                    if (response.result) {
                        $('#package').html(response.view);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }else{

                        alert(response.message);
                    }
                }
            });
        });
    </script>
@endsection
