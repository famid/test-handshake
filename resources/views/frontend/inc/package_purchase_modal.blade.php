<form class="" id="package_payment_form" action="{{ route('customer_packages.purchase') }}" method="post">
    @csrf
    <input type="hidden" name="customer_package_id" value="{{ $package->id }}">
    <div class="modal-body gry-bg px-3 pt-3">
        <div class="row">
            <div class="col-md-3">
                <label>{{ translate('Payment Method')}}</label>
            </div>
            <div class="col-md-9">
                <div class="mb-3">
                    <select class="form-control aiz-selectpicker" data-live-search="true" name="payment_option">
                        @if (get_setting('paypal_payment') == 1)
                            <option value="paypal">{{ translate('Paypal')}}</option>
                        @endif
                        @if (get_setting('stripe_payment') == 1)
                            <option value="stripe">{{ translate('Stripe')}}</option>
                        @endif
                        @if(get_setting('sslcommerz_payment') == 1)
                            <option value="sslcommerz">{{ translate('sslcommerz')}}</option>
                        @endif
                        @if(get_setting('instamojo_payment') == 1)
                            <option value="instamojo">{{ translate('Instamojo')}}</option>
                        @endif
                        @if(get_setting('razorpay') == 1)
                            <option value="razorpay">{{ translate('RazorPay')}}</option>
                        @endif
                        @if(get_setting('paystack') == 1)
                            <option value="paystack">{{ translate('PayStack')}}</option>
                        @endif
                        @if(get_setting('voguepay') == 1)
                            <option value="voguepay">{{ translate('Voguepay')}}</option>
                        @endif
                        @if(get_setting('payhere') == 1)
                            <option value="payhere">{{ translate('Payhere')}}</option>
                        @endif
                        @if(get_setting('ngenius') == 1)
                            <option value="ngenius">{{ translate('Ngenius')}}</option>
                        @endif
                        @if(get_setting('iyzico') == 1)
                            <option value="iyzico">{{ translate('Iyzico')}}</option>
                        @endif
                        @if(get_setting('nagad') == 1)
                            <option value="nagad">{{ translate('Nagad')}}</option>
                        @endif
                        @if(get_setting('bkash') == 1)
                            <option value="bkash">{{ translate('Bkash')}}</option>
                        @endif
                        @if(addon_is_activated('african_pg'))
                            @if(get_setting('mpesa') == 1)
                                <option value="mpesa">{{ translate('Mpesa')}}</option>
                            @endif
                            @if(get_setting('flutterwave') == 1)
                                <option value="flutterwave">{{ translate('Flutterwave')}}</option>
                            @endif
                            @if (get_setting('payfast') == 1)
                              <option value="payfast">{{ translate('PayFast')}}</option>
                            @endif
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="page-price-info">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>Package Details</td>
                        <td class="text-center">Amount</td>
                    </tr>
                </thead>

                @php
                    $discount = 0;
                    $subtotal = 0;
                @endphp
                <tbody>
                    <tr>
                        <td>{{ $package->package_name }}</td>
                        <td class="text-right">${{ number_format($package->package_price,2) }}</td>
                    </tr>
                    @if ($current_package != null && $current_package->package->package_price > 0)
                        @php
                            $remain_days = countDays(date("Y-m-d H:i:s"),$current_package->subscription_end);
                            $discount    = floor(($current_package->package->package_price / $current_package->package->package_duration) * $remain_days);
                        @endphp
                        <tr>
                            <td>{{ $current_package->package->package_name }} - (Current Package)
                                {!! $discount > 0 ? '<b>(Discount Applied)</b>' : '' !!}
                            </td>
                            <td class="text-right">- {{ number_format($discount,2) }}</td>
                        </tr>
                    @endif

                    @php
                        $subtotal = $package->package_price - $discount;

                        if ($subtotal <= 0) {
                            $subtotal = 0;
                        }
                    @endphp

                    <tr>
                        <td class="text-right">Subtotal -</td>
                        <td class="text-right">${{ number_format($subtotal,2) }}</td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="total_amount" value="{{ $subtotal }}">
        </div>

        <div class="form-group text-right">
            <button type="button" class="btn btn-sm btn-secondary transition-3d-hover mr-1" data-dismiss="modal">{{translate('cancel')}}</button>
            <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{translate('Confirm')}}</button>
        </div>
    </div>
</form>
