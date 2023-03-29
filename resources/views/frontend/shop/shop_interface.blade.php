@extends('backend.layouts.layout')

@push('custom_admin_css')
    <style>
        .shop-card {
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            flex-direction: column;
        }
    </style>
@endpush
@section('content')

<div class="h-100 bg-cover bg-center py-5 d-flex align-items-center" style="background-image: url({{ uploaded_asset(get_setting('admin_login_background')) }})">

    <div class="container">

        @if (count($shops) > 0 && !isset(request()->create_new_shop))
            <div class="text-center mb-4">
                <h4>Select Your Shop</h4>
            </div>
            <div class="row justify-content-center">
                @foreach ($shops as $shop)
                    <div class="col-lg-3 col-xl-2 col-md-4 col-sm-6 col-12">
                        <div class="card text-center">
                            @if ($shop->verified_at != null)
                                <a href="{{ route('seller-shop.active',$shop->slug) }}" class="card-body shop-card">
                                    {{ $shop->shop_name }}
                                    <div class="btn btn-soft-primary btn-circle mt-2"><i class="las la-angle-right"></i></div>
                                </a>
                            @else
                                <div class="card-body shop-card verify-shop" data-email="{{ $shop->shop_email }}">
                                    {{ $shop->shop_name }}
                                    <div class="btn btn-soft-primary btn-circle mt-2"><i class="las la-angle-right"></i></div>
                                </div>
                                <div class="badge-pill badge-danger">Unverfied</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif (isset(request()->create_new_shop) || count($shops) <= 0)
            <div class="row">
                <div class="col-lg-6 col-xl-4 mx-auto">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="mb-5 text-center">
                                @if(get_setting('system_logo_black') != null)
                                    <img src="{{ uploaded_asset(get_setting('system_logo_black')) }}" class="mw-100 mb-4" height="40">
                                @else
                                    <img src="{{ static_asset('assets/img/logo.png') }}" class="mw-100 mb-4" height="40">
                                @endif
                                <h1 class="h3 text-primary mb-2">{{ translate('Welcome to') }} {{ env('APP_NAME') }}</h1>
                                <h5>{{ translate('Register your Shop') }}</h5>
                            </div>
                            <form class="pad-hor shop-form" method="POST" role="form" action="#">
                                @csrf

                                <div class="form-group">
                                    <label for="name">Enter your shop name:</label>
                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('email') }}" required placeholder="{{ translate('Enter shop name') }}">
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="email">Enter your Daraz Email:</label>
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ translate('Email') }}">
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <div class="text-left d-flex align-items-center">
                                            <label for="">Daraz Connect:</label>
                                            <label class="aiz-switch aiz-switch-success mb-0 ml-2">
                                                <input name="daraz_connect" value="1" type="checkbox">
                                                <span class="slider round"></span>
                                            </label>
                                            {{-- <label class="aiz-checkbox">
                                                <input type="checkbox" name="connect_daraz" id="connect_daraz" {{ old('connect_daraz') ? 'checked' : '' }}>
                                                <span>{{ translate('Connect with Daraz') }}</span>
                                                <span class="aiz-square-check"></span>
                                            </label> --}}
                                        </div>
                                    </div>

                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    {{ translate('Submit') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- OTP MODAL--}}
<div class="modal fade" id="confirm-otp" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Verify Email')}}</h5>
            </div>
            <form action="" method="post" id="verification-form">
                @csrf
                <input type="hidden" name="verified_email" value="" id="verified_email">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="verify">Enter your OTP Code</label>
                        <input type="text" name="verify_otp" id="" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">{{translate('Proceed!')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>

        $.ajaxSetup({
            headers : {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Register New Shop

        $('.shop-form').submit(function (e) {
            e.preventDefault();

            var form = $(this).serialize();
            var submit_btn = $(this).find('button[type="submit"]');

            $(submit_btn).text('Processing...');
            $(submit_btn).attr('disabled',true);

            $.ajax({
                type: "POST",
                url: "{{ route('seller-shop.store') }}",
                data: form,
                async: true,
                success: function (response) {

                    if (response.result) {
                        AIZ.plugins.notify('success', response.message);
                        $('#verified_email').val(response.email);
                        $('#confirm-otp').modal('show');

                    }else{

                        AIZ.plugins.notify('danger', response.message);
                        $(submit_btn).removeAttr('disabled',true);
                        $(submit_btn).text('Submit');
                    }
                }
            });
        });

        // Verify Shop

        $('#verification-form').submit(function (e) {
            e.preventDefault();
            var form = $(this).serialize();
            var submit_btn = $(this).find('button[type="submit"]');
            var url = "{{ route('seller-shop.active',':slug') }}"

            $(submit_btn).text('Processing...');
            $(submit_btn).attr('disabled',true);

            $.ajax({
                type: "POST",
                url: "{{ route('seller-shop.verify') }}",
                data: form,
                async: true,
                success: function (response) {

                    if (response.result) {

                        url = url.replace(':slug', response.slug);
                        AIZ.plugins.notify('success', response.message);
                        $('#confirm-otp').modal('hide');
                        location.href = url;
                    }else{

                        AIZ.plugins.notify('danger', response.message);
                        $(submit_btn).removeAttr('disabled',true);
                        $(submit_btn).text('Proceed!');
                    }
                },
                error: function(response){

                    $(submit_btn).removeAttr('disabled',true);
                }
            });
        });


        // Re-verify Shop

        $('.verify-shop').click(function (e) {
            e.preventDefault();

            var email = $(this).data('email');

            $.ajax({
                type: "post",
                url: "{{ route('seller-shop.re-verify') }}",
                data: {
                    'email': email
                },
                async: true,
                success: function (response) {

                    if (response.result) {

                        AIZ.plugins.notify('success', response.message);
                        $('#verified_email').val(response.email);
                        $('#confirm-otp').modal('show');
                    }else{

                        AIZ.plugins.notify('danger', response.message);
                    }
                }
            });
        });
    </script>
@endsection
