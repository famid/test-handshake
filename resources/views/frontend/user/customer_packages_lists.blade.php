@extends('frontend.layouts.app')

@section('content')
<section class="py-8 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto text-center">
                <h1 class="mb-0 fw-700">{{ translate('Our Premium Packages') }}</h1>
            </div>
        </div>
    </div>
</section>



<section class="py-4 py-lg-5">
    <div class="container">
        <div class="row row-cols-xxl-4 row-cols-lg-3 row-cols-md-2 row-cols-1 gutters-10 justify-content-center">
            @foreach ($customer_packages as $key => $customer_package)

                <div class="col">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            <div class="text-center mb-4 mt-3">
                                <img class="mw-100 mx-auto mb-4" src="{{ uploaded_asset($customer_package->package_image) }}" height="100">
                                <h5 class="mb-3 h5 fw-600">{{$customer_package->package_name }}</h5>
                            </div>
                            <ul class="list-group list-group-raw fs-15 mb-5">
                                <li class="list-group-item py-2">
                                    <i class="las la-check text-success mr-2"></i>
                                    {{ $customer_package->product_limit }} {{translate('Product Upload')}}
                                </li>
                                <li class="list-group-item py-2">
                                    <i class="las la-check text-success mr-2"></i>
                                    {{ $customer_package->warehouse_limit }} {{translate('Warehouse Limit')}}
                                </li>
                                <li class="list-group-item py-2">
                                    <i class="las la-check text-success mr-2"></i>
                                    {{ $customer_package->daraz_sync_limit }} {{translate('Daraz Store Sync Limit')}}
                                </li>
                                @if ($customer_package->additional_packages != null)
                                    @php
                                        $additional_info = json_decode($customer_package->additional_packages);
                                    @endphp

                                    @foreach ($additional_info as $item)
                                        <li class="list-group-item py-2">
                                            <i class="las la-check text-success mr-2"></i>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                @endif
                            </ul>

                            <div class="mb-5 d-flex align-items-center justify-content-center">
                                @if ($customer_package->package_price == 0)
                                    <span class="fs-24 fw-600 lh-1 mb-0">{{ translate('Free') }}</span>
                                @else
                                    <p class="fs-24 fw-600 lh-1 mb-0">{{ single_price($customer_package->package_price) }} / <span class="fs-16 text-primary fw-600">{{ $customer_package->package_duration }} Days</span></p>
                                @endif
                            </div>
                            <div class="text-center">

                                @if (checkPackage() != null && checkPackage()->package_id == $customer_package->id)
                                    <div class="text-center"><p class="text-danger font-weight-bold fs-15">Your Current Package</p></div>
                                @endif

                                @if ($customer_package->package_price == 0)
                                    <button class="btn btn-primary" onclick="show_price_modal({{ $customer_package->id}})"
                                        {{ (checkPackage() != null && checkPackage()->package_id == $customer_package->id) ? 'disabled' : '' }}>
                                        {{ translate('Free Package')}}
                                    </button>
                                @else
                                    @if (addon_is_activated('offline_payment') )
                                        <button class="btn btn-primary" onclick="select_payment_type({{ $customer_package->id}})">{{ translate('Purchase Package')}}</button>
                                    @else
                                        <button class="btn btn-primary" onclick="show_price_modal({{ $customer_package->id}})"
                                            {{ (checkPackage() != null && checkPackage()->package_id == $customer_package->id) ? 'disabled' : '' }}>
                                            {{ translate('Purchase Package')}}
                                        </button>

                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@section('modal')

    <!-- Select Payment Type Modal -->
    <div class="modal fade" id="select_payment_type_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Select Payment Type') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="package_id" name="package_id" value="">
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('Payment Type')}}</label>
                        </div>
                        <div class="col-md-10">
                            <div class="mb-3">
                                <select class="form-control aiz-selectpicker" onchange="payment_type(this.value)"
                                        data-minimum-results-for-search="Infinity">
                                    <option value="">{{ translate('Select One')}}</option>
                                    <option value="online">{{ translate('Online payment')}}</option>
                                    <option value="offline">{{ translate('Offline payment')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-primary transition-3d-hover mr-1" id="select_type_cancel" data-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Online payment Modal -->
    <div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Purchase Your Package') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="online-payment-modal">

                </div>
            </div>
        </div>
    </div>


    <!-- offline payment Modal -->
    <div class="modal fade" id="offline_customer_package_purchase_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Offline Package Purchase ') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="offline_customer_package_purchase_modal_body"></div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function select_payment_type(id) {
            $('input[name=package_id]').val(id);
            $('#select_payment_type_modal').modal('show');
        }

        function payment_type(type) {
            var package_id = $('#package_id').val();
            if (type == 'online') {
                $("#select_type_cancel").click();
                show_price_modal(package_id);
            } else if (type == 'offline') {
                $("#select_type_cancel").click();
                $.post('{{ route('offline_customer_package_purchase_modal') }}', {
                    _token: '{{ csrf_token() }}',
                    package_id: package_id
                }, function (data) {
                    $('#offline_customer_package_purchase_modal_body').html(data);
                    $('#offline_customer_package_purchase_modal').modal('show');
                });
            }
        }

        function show_price_modal(id) {

            var url = '{{ route("purchase_info.package", ":id") }}';
                url = url.replace(':id', id);

            $.ajax({
                type: "GET",
                url: url,
                success: function (response) {

                    if (response.result) {
                        $('.online-payment-modal').html(response.data);
                        $('#price_modal').modal('show');
                        AIZ.plugins.bootstrapSelect('refresh');
                    }else{

                        AIZ.plugins.notify('danger', response.message);
                    }

                }
            });
        }


        function get_free_package(id) {
            $('input[name=customer_package_id]').val(id);
            $('#package_payment_form').submit();
        }
    </script>
@endsection
