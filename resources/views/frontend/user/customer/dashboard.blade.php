@extends('frontend.layouts.user_panel')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Dashboard') }} | @if (activeShop() != null)<span class="badge-pill badge-success fs-15">{{ activeShop()->shop_name }}</span>@endif</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Shop List
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @if (count($shops) > 0)
                            @foreach ($shops as $shop)
                                <a class="dropdown-item" href="{{ route('seller-shop.active',$shop->slug) }}">{{ $shop->shop_name }}</a>
                            @endforeach
                        @endif
                      <a class="dropdown-item" href="{{ route('seller-shop.index') }}?create_new_shop=1">Create New Shop</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <div class="col-md-4">
        <div class="bg-grad-1 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3">
                @php
                    $user_id = Auth::user()->id;
                    $cart = \App\Models\Cart::where('user_id', $user_id)->get();
                @endphp
                @if(count($cart) > 0)
                <div class="h3 fw-700">
                    {{ count($cart) }} {{ translate('Product(s)') }}
                </div>
                @else
                <div class="h3 fw-700">
                    0 {{ translate('Product') }}
                </div>
                @endif
                <div class="opacity-50">{{ translate('in your cart') }}</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,192L30,208C60,224,120,256,180,245.3C240,235,300,181,360,144C420,107,480,85,540,96C600,107,660,149,720,154.7C780,160,840,128,900,117.3C960,107,1020,117,1080,112C1140,107,1200,85,1260,74.7C1320,64,1380,64,1410,64L1440,64L1440,320L1410,320C1380,320,1320,320,1260,320C1200,320,1140,320,1080,320C1020,320,960,320,900,320C840,320,780,320,720,320C660,320,600,320,540,320C480,320,420,320,360,320C300,320,240,320,180,320C120,320,60,320,30,320L0,320Z"></path>
            </svg>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-grad-2 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3">
                @php
                    $orders = \App\Models\Order::where('user_id', Auth::user()->id)->get();
                    $total = 0;
                    foreach ($orders as $key => $order) {
                        $total += count($order->orderDetails);
                    }
                @endphp
                <div class="h3 fw-700">{{ count(Auth::user()->wishlists)}} {{ translate('Product(s)') }}</div>
                <div class="opacity-50">{{ translate('in your wishlist') }}</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,128L34.3,112C68.6,96,137,64,206,96C274.3,128,343,224,411,250.7C480,277,549,235,617,213.3C685.7,192,754,192,823,181.3C891.4,171,960,149,1029,117.3C1097.1,85,1166,43,1234,58.7C1302.9,75,1371,149,1406,186.7L1440,224L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path>
            </svg>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bg-grad-3 text-white rounded-lg mb-4 overflow-hidden">
            <div class="px-3 pt-3">
                @php
                    $orders = \App\Models\Order::where('user_id', Auth::user()->id)->get();
                    $total = 0;
                    foreach ($orders as $key => $order) {
                        $total += count($order->orderDetails);
                    }
                @endphp
                <div class="h3 fw-700">{{ $total }} {{ translate('Product(s)') }}</div>
                <div class="opacity-50">{{ translate('you ordered') }}</div>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                <path fill="rgba(255,255,255,0.3)" fill-opacity="1" d="M0,192L26.7,192C53.3,192,107,192,160,202.7C213.3,213,267,235,320,218.7C373.3,203,427,149,480,117.3C533.3,85,587,75,640,90.7C693.3,107,747,149,800,149.3C853.3,149,907,107,960,112C1013.3,117,1067,171,1120,202.7C1173.3,235,1227,245,1280,213.3C1333.3,181,1387,107,1413,69.3L1440,32L1440,320L1413.3,320C1386.7,320,1333,320,1280,320C1226.7,320,1173,320,1120,320C1066.7,320,1013,320,960,320C906.7,320,853,320,800,320C746.7,320,693,320,640,320C586.7,320,533,320,480,320C426.7,320,373,320,320,320C266.7,320,213,320,160,320C106.7,320,53,320,27,320L0,320Z"></path>
            </svg>
        </div>
    </div>
</div>
<div class="row gutters-10">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ translate('Daraz callback Url') }}</h6>
            </div>
            <div class="card-body">
                @if (Auth::user()->url_slug != null)
                    <p class="text-primary daraz-callback">{{ URL::to('/').'/api/v2/daraz-callback/'.Auth::user()->url_slug }}</p>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ translate('Default Shipping Address') }}</h6>
            </div>
            <div class="card-body">
                @if(Auth::user()->addresses != null)
                    @php
                        $address = Auth::user()->addresses->where('set_default', 1)->first();
                    @endphp
                    @if($address != null)
                        <ul class="list-unstyled mb-0">
                            <li class=" py-2"><span>{{ translate('Address') }} : {{ $address->address }}</span></li>
                            <li class=" py-2"><span>{{ translate('Country') }} : {{ $address->country->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('State') }} : {{ $address->state->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('City') }} : {{ $address->city->name }}</span></li>
                            <li class=" py-2"><span>{{ translate('Postal Code') }} : {{ $address->postal_code }}</span></li>
                            <li class=" py-2"><span>{{ translate('Phone') }} : {{ $address->phone }}</span></li>
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @if (get_setting('classified_product'))
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">{{ translate('Purchased Package') }}</h6>
            </div>
            <div class="card-body text-center">
                @php
                    $customer_package = \App\Models\PackageUser::where('user_id',Auth::user()->id)->where('user_type',loginType())->first();
                @endphp
                @if($customer_package != null)
                    <img src="{{ uploaded_asset($customer_package->Package->package_image) }}" class="img-fluid mb-4 h-110px">

                    <p class="mb-1 text-muted">{{ translate('Daraz Sync') }}: {{ $customer_package->Package->daraz_sync_limit }} {{ translate('Times')}}</p>

                    <p class="text-muted mb-1">{{ translate('Daraz Sync Remaining') }}: {{ $customer_package->remain_daraz_sync }} {{ translate('Times')}}</p>
                    <p class="text-muted mb-4">{{ translate('Valid Till') }}: {{ $customer_package->subscription_end != null ? date('d-M-Y',strtotime($customer_package->subscription_end)) : '--' }}</p>
                    <h5 class="fw-600 mb-3 text-primary">{{ translate('Current Package') }}: {{ $customer_package->Package->package_name }}</h5>
                @else
                    <h5 class="fw-600 mb-3 text-primary">{{translate('Package Not Found')}}</h5>
                @endif
                    <a href="{{ route('customer_packages_list_show') }}" class="btn btn-success d-inline-block">{{ translate('Upgrade Package') }}</a>
                    @if (isset($customer_package) && $customer_package->Package->package_price > 0)
                        <a href="javascript:void(0)" class="btn btn-warning d-inline-block" onclick="renewPackage()">Renew Pacakge</a>
                    @endif
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Online payment Modal -->
<div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Renew Your Package') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="online-payment-modal">

            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        $('.daraz-callback').click(function (e) {
            e.preventDefault();

            var url = $(this).text();
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            document.execCommand("copy");
            $temp.remove();

            AIZ.plugins.notify('success', '{{ translate('Callback Url copied succesfully.') }}');
        });


        function renewPackage(id)
        {
            var url = '{{ route("renew.package", ":id") }}';

                $.ajax({
                type: "GET",
                url: "{{ route('renew.package') }}",
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
    </script>
@endsection
