@extends('frontend.layouts.app')

@section('content')

    <section class="py-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-xl-8 mx-auto">

                    <div class="text-center py-4 mb-4">
                        <i class="la la-check-circle la-3x text-success mb-3"></i>
                        <h1 class="h3 mb-3 fw-600">{{ translate('Thank You for Becoming a Seller!')}}</h1>
                        <div class="text-center">
                            <a href="{{ route('auth.switch') }}" class="btn btn-primary">Seller Dashboard</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
