@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{translate('Add New Product')}}</h5>
    </div>
    <div class="">
        <form class="form form-horizontal mar-top" action="{{route('products.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    @csrf
                    <input type="hidden" name="added_by" value="admin">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Product Name')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="name" placeholder="{{ translate('Product Name') }}" onchange="update_sku()" required>
                                </div>
                            </div>
                            <div class="form-group row" id="category">
                                <label class="col-md-3 col-from-label">{{translate('Category')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker categories" name="category_id" id="category_id" data-live-search="true" required>
                                        @include('backend.product.products.product_categories')
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="brand">
                                <label class="col-md-3 col-from-label">{{translate('Brand')}}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker brands" name="brand_id" id="brand_id" data-live-search="true">
                                        <option value="">{{ translate('Select Brand') }}</option>
                                        {{--                                    @include('backend.product.brands.product_brand')--}}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Unit')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Minimum Purchase Qty')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" lang="en" class="form-control" name="min_qty" value="1" min="1" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Tags')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control aiz-tag-input" name="tags[]" placeholder="{{ translate('Type and hit enter to add a tag') }}">
                                    <small class="text-muted">{{translate('This is used for search. Input those words by which cutomer can find this product.')}}</small>
                                </div>
                            </div>

                            @if (addon_is_activated('pos_system'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Barcode') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="barcode"
                                               placeholder="{{ translate('Barcode') }}">
                                    </div>
                                </div>
                            @endif

                            @if (addon_is_activated('refund_request'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Refundable') }}</label>
                                    <div class="col-md-8">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="refundable" checked>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{ translate('Gallery Images') }} <small>(600x600)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image"
                                         data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="photos" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('These images are visible in product details page gallery. Use 600x600 sizes images.') }}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{ translate('Thumbnail Image') }}
                                    <small>(300x300)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="thumbnail_img" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                    <small
                                        class="text-muted">{{ translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Videos') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Provider') }}</label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="video_provider"
                                            id="video_provider">
                                        <option value="youtube">{{ translate('Youtube') }}</option>
                                        <option value="dailymotion">{{ translate('Dailymotion') }}</option>
                                        <option value="vimeo">{{ translate('Vimeo') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Video Link') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="video_link"
                                           placeholder="{{ translate('Video Link') }}">
                                    <small
                                        class="text-muted">{{ translate("Use proper link without extra parameter. Don't use short share link/embeded iframe code.") }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Variation') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row gutters-5">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Colors') }}"
                                           disabled>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" data-live-search="true"
                                            data-selected-text-format="count" name="colors[]" id="colors" multiple
                                            disabled>
                                        @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                            <option value="{{ $color->code }}"
                                                    data-content="<span><span class='size-15px d-inline-block mr-2 rounded border' style='background:{{ $color->code }}'></span><span>{{ $color->name }}</span></span>">
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input value="1" type="checkbox" name="colors_active">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row gutters-5">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{ translate('Attributes') }}"
                                           disabled>
                                </div>
                                <div class="col-md-8">
                                    <select name="choice_attributes[]" id="choice_attributes"
                                            class="form-control aiz-selectpicker" data-selected-text-format="count"
                                            data-live-search="true" multiple
                                            data-placeholder="{{ translate('Choose Attributes') }}">
                                        @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                            <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <p>{{ translate('Choose the attributes of this product and then input values of each attribute') }}
                                </p>
                                <br>
                            </div>

                            <div class="customer_choice_options" id="customer_choice_options">

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product price + stock') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Unit price') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                           placeholder="{{ translate('Unit price') }}" name="unit_price"
                                           class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 control-label"
                                       for="start_date">{{ translate('Discount Date Range') }}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control aiz-date-range" name="date_range"
                                           placeholder="{{ translate('Select Date') }}" data-time-picker="true"
                                           data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Discount') }} <span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                           placeholder="{{ translate('Discount') }}" name="discount" class="form-control"
                                           required>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control aiz-selectpicker" name="discount_type">
                                        <option value="amount">{{ translate('Flat') }}</option>
                                        <option value="percent">{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>

                            @if (addon_is_activated('club_point'))
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">
                                        {{ translate('Set Point') }}
                                    </label>
                                    <div class="col-md-6">
                                        <input type="number" lang="en" min="0" value="0"
                                               step="1" placeholder="{{ translate('1') }}" name="earn_point"
                                               class="form-control">
                                    </div>
                                </div>
                            @endif

                            <div id="show-hide-div">
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Quantity') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" lang="en" min="0" value="0"
                                               step="1" placeholder="{{ translate('Quantity') }}"
                                               name="current_stock" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">
                                        {{ translate('SKU') }}
                                    </label>
                                    <div class="col-md-6">
                                        <input type="text" placeholder="{{ translate('SKU') }}" name="sku"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link') }}"
                                           name="external_link" class="form-control">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">
                                    {{ translate('External link button text') }}
                                </label>
                                <div class="col-md-9">
                                    <input type="text" placeholder="{{ translate('External link button text') }}"
                                           name="external_link_btn" class="form-control">
                                    <small
                                        class="text-muted">{{ translate('Leave it blank if you do not use external site link') }}</small>
                                </div>
                            </div>
                            <br>
                            <div class="sku_combination" id="sku_combination">

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Description') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-md-8">
                                    <textarea class="aiz-text-editor" name="description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0 h6">{{ translate('Product Shipping Cost') }}</h5>
                                </div>
                                <div class="card-body">

                                </div>
                            </div>-->

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('PDF Specification') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{ translate('PDF Specification') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="document">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="pdf" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('SEO Meta Tags') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Meta Title') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="meta_title"
                                           placeholder="{{ translate('Meta Title') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-md-8">
                                    <textarea name="meta_description" rows="8" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label"
                                       for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="meta_img" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Shipping Configuration') }}
                            </h5>
                        </div>

                        <div class="card-body">
                            @if (get_setting('shipping_type') == 'product_wise_shipping')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Free Shipping') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="free" checked>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Flat Rate') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="radio" name="shipping_type" value="flat_rate">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="flat_rate_shipping_div" style="display: none">
                                    <div class="form-group row">
                                        <label class="col-md-6 col-from-label">{{ translate('Shipping cost') }}</label>
                                        <div class="col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                   step="0.01" placeholder="{{ translate('Shipping cost') }}"
                                                   name="flat_shipping_cost" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label
                                        class="col-md-6 col-from-label">{{ translate('Is Product Quantity Mulitiply') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="is_quantity_multiplied" value="1">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Product wise shipping cost is disable. Shipping cost is configured from here') }}
                                    <a href="{{ route('shipping_configuration.index') }}"
                                       class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index', 'shipping_configuration.edit', 'shipping_configuration.update']) }}">
                                        <span class="aiz-side-nav-text">{{ translate('Shipping Configuration') }}</span>
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Low Stock Quantity Warning') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label for="name">
                                    {{ translate('Quantity') }}
                                </label>
                                <input type="number" name="low_stock_quantity" value="1" min="0"
                                       step="1" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">
                                {{ translate('Stock Visibility State') }}
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Show Stock Quantity') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="quantity" checked>
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label
                                    class="col-md-6 col-from-label">{{ translate('Show Stock With Text Only') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="text">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Hide Stock') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="radio" name="stock_visibility_state" value="hide">
                                        <span></span>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Cash On Delivery') }}</h5>
                        </div>
                        <div class="card-body">
                            @if (get_setting('cash_payment') == '1')
                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                                    <div class="col-md-6">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input type="checkbox" name="cash_on_delivery" value="1"
                                                   checked="">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                <p>
                                    {{ translate('Cash On Delivery option is disabled. Activate this feature from here') }}
                                    <a href="{{ route('activation.index') }}"
                                       class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index', 'shipping_configuration.edit', 'shipping_configuration.update']) }}">
                                        <span class="aiz-side-nav-text">{{ translate('Cash Payment Activation') }}</span>
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Featured') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="featured" value="1">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Todays Deal') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{ translate('Status') }}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="todays_deal" value="1">
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Flash Deal') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="name">
                                        {{ translate('Add To Flash') }}
                                    </label>
                                    <select class="form-control aiz-selectpicker" name="flash_deal_id" id="flash_deal">
                                        <option value="">Choose Flash Title</option>
                                        @foreach (\App\Models\FlashDeal::where('status', 1)->get() as $flash_deal)
                                            <option value="{{ $flash_deal->id }}">
                                                {{ $flash_deal->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name">
                                        {{ translate('Discount') }}
                                    </label>
                                    <input type="number" name="flash_discount" value="0" min="0"
                                           step="1" class="form-control">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="name">
                                        {{ translate('Discount Type') }}
                                    </label>
                                    <select class="form-control aiz-selectpicker" name="flash_discount_type"
                                            id="flash_discount_type">
                                        <option value="">Choose Discount Type</option>
                                        <option value="amount">{{ translate('Flat') }}</option>
                                        <option value="percent">{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Integration') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-dynamic-field">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz System') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-system">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Variants') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-product-variant">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Price & Stock') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-price-stock">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Service & Warranty') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-service-warranty">

                            </div>



                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Delivery') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-delivery">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Required Field') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-mandatory">

                            </div>

                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Daraz Optional field') }}</h5>
                            </div>
                            <div class="card-body" id="daraz-optional">

                            </div>


                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('Estimate Shipping Time') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-3">
                                    <label for="name">
                                        {{ translate('Shipping Days') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="est_shipping_days" min="1"
                                               step="1" placeholder="{{ translate('Shipping Days') }}">
                                        <div class="input-group-prepend">
                                        <span class="input-group-text"
                                              id="inputGroupPrepend">{{ translate('Days') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 h6">{{ translate('VAT & Tax') }}</h5>
                            </div>
                            <div class="card-body">
                                @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                    <label for="name">
                                        {{ $tax->name }}
                                        <input type="hidden" value="{{ $tax->id }}" name="tax_id[]">
                                    </label>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <input type="number" lang="en" min="0" value="0"
                                                   step="0.01" placeholder="{{ translate('Tax') }}" name="tax[]"
                                                   class="form-control" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <select class="form-control aiz-selectpicker" name="tax_type[]">
                                                <option value="amount">{{ translate('Flat') }}</option>
                                                <option value="percent">{{ translate('Percent') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="col-12">
                        <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                            <div class="btn-group mr-2" role="group" aria-label="First group">
                                <button type="submit" name="button" value="draft"
                                        class="btn btn-warning">{{ translate('Save As Draft') }}</button>
                            </div>
                            <div class="btn-group mr-2" role="group" aria-label="Third group">
                                <button type="submit" name="button" value="unpublish"
                                        class="btn btn-primary">{{ translate('Save & Unpublish') }}</button>
                            </div>
                            <div class="btn-group" role="group" aria-label="Second group">
                                <button type="submit" name="button" value="publish"
                                        class="btn btn-success">{{ translate('Save & Publish') }}</button>
                            </div>

                            <div class="btn-group" role="group" aria-label="Second group">
                                <button type="submit" name="save-daraz" value="publish" id="daraz-create-product-button"
                                        class="btn btn-success">{{ translate('Daraz Create Product') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('form').bind('submit', function(e) {
            // Disable the submit button while evaluating if the form should be submitted
            $("button[type='submit']").prop('disabled', true);

            var valid = true;

            if (!valid) {
                e.preventDefault();

                // Reactivate the button if the form was not submitted
                $("button[type='submit']").button.prop('disabled', false);
            }
        });

        $("[name=shipping_type]").on("change", function() {
            $(".flat_rate_shipping_div").hide();

            if ($(this).val() == 'flat_rate') {
                $(".flat_rate_shipping_div").show();
            }

        });

        function add_more_customer_choice_option(i, name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('products.add-more-choice-option') }}',
                data: {
                    attribute_id: i
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#customer_choice_options').append('\
                            <div class="form-group row">\
                                <div class="col-md-3">\
                                    <input type="hidden" name="choice_no[]" value="' + i + '">\
                                    <input type="text" class="form-control" name="choice[]" value="' + name +
                        '" placeholder="{{ translate('Choice Title') }}" readonly>\
                                </div>\
                                <div class="col-md-8">\
                                    <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_' +
                        i + '[]" multiple>\
                                        ' + obj + '\
                                    </select>\
                                </div>\
                            </div>');
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });


        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
            } else {
                $('#colors').prop('disabled', false);
                AIZ.plugins.bootstrapSelect('refresh');
            }
            update_sku();
        });

        $(document).on("change", ".attribute_choice", function() {
            update_sku();
        });

        $('#colors').on('change', function() {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function() {
            update_sku();
        });

        $('input[name="name"]').on('keyup', function() {
            update_sku();
        });

        function delete_row(em) {
            $(em).closest('.form-group row').remove();
            update_sku();
        }

        function delete_variant(em) {
            $(em).closest('.variant').remove();
        }

        function update_sku() {
            $.ajax({
                type: "POST",
                url: '{{ route('products.sku_combination') }}',
                data: $('#choice_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data);
                    AIZ.uploader.previewGenerate();
                    AIZ.plugins.fooTable();
                    if (data.length > 1) {
                        $('#show-hide-div').hide();
                    } else {
                        $('#show-hide-div').show();
                    }
                }
            });
        }

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                add_more_customer_choice_option($(this).val(), $(this).text());
            });

            update_sku();
        });


        // AJax Live Category Search

        // let timer;
        // const delay = 1000;

        // function handleAjaxRequest() {
        //   //Your code here
        // }
        // function debounceAjaxRequest(e) {
        //   clearTimeout(timer);
        //   timer = setTimeout(handleAjaxRequest(e), delay);
        // }

        // $(document).on("keyup", ".categories", function (e) {
        //   debounceAjaxRequest();
        // });


        $(document).on('input', '.categories', function(e) {
            var searchData = e.target.value;

            if (searchData.length > 2) {


                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: "{{ route('category.search') }}",
                    data: {

                        "search_category": searchData
                    },
                    async: true,
                    success: function(response) {

                        if (response.result) {

                            $('#category_id').html(response.data);
                            AIZ.plugins.bootstrapSelect('refresh');
                        }
                    },
                    error: function(response) {

                    }
                });
            }

        });

        $(document).ready(function() {
            appendBrand();
        });

        $(document).on('input', '.brands', function(e) {
            var searchData = e.target.value;

            if (searchData.length > 2) {
                appendBrand(searchData);
            } else {
                appendBrand();
            }
        });

        function appendBrand(searchData = null) {
            $.ajax({
                type: "GET",
                delay: 1000,
                url: "{{ route('admin.brands.get-brands') }}",
                data: {

                    "search_brand_name": searchData
                },
                async: true,
                success: function(response) {

                    if (response.result) {

                        $('#brand_id').html(response.data);

                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                },
                error: function(response) {

                }
            });
        }

        //     =====================================Product create functionality ===============================

        // const attributeResponseData = {
        //     normal: {
        //         mandatory: new Map(),
        //         nonMandatory: new Map(),
        //         saleProp: new Map()
        //     },
        //     sku: {
        //         mandatory: new Map(),
        //         nonMandatory: new Map(),
        //         saleProp: new Map()
        //     }
        // };

        let productCreatePayload = {

        };

        $('#category_id').change(function(e) {
            e.preventDefault();

            let selectedCategoryId = $(this).val();
            console.log("Category_id: ", selectedCategoryId);
            productCreatePayload.PrimaryCategory = selectedCategoryId;
            productCreatePayload.SPUId = [];
            productCreatePayload.AssociatedSku = [];
            appendProductDarazAttributes(selectedCategoryId);

        });

        function appendProductDarazAttributes($categoryId) {
            $.ajax({
                type: "GET",
                delay: 1000,
                url: "{{ route('daraz.category.attributes') }}",
                data: {

                    "category_id": $categoryId
                },
                async: true,
                success: function(response) {

                    if (response.result) {
                        console.log("==================Attribute============");
                        // console.log(response.data);
                        manipulateProductAttribute(JSON.parse(response.data));
                        darazAttributeOperation()

                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        // function manipulateProductAttribute(attributes) {
        //     attributes.data.forEach((item) => {
        //
        //         let htmlElement = buildAttributeField(item);
        //         item.html = htmlElement;
        //
        //         if (item.attribute_type === "normal") {
        //             const key = item.name;
        //
        //             if (item.is_sale_prop) {
        //
        //                 attributeResponseData.normal.saleProp.set(key, item);
        //             } else if (item.is_mandatory) {
        //
        //                 attributeResponseData.normal.mandatory.set(key, item);
        //             } else {
        //
        //                 attributeResponseData.normal.nonMandatory.set(key, item);
        //             }
        //
        //
        //         } else if (item.attribute_type === "sku") {
        //             const key = item.name;
        //
        //             if (item.is_sale_prop) {
        //                 // attributeResponseData.sku.saleProp[item.name] = item;
        //                 attributeResponseData.sku.saleProp.set(key, item);
        //             } else if (item.is_mandatory) {
        //
        //                 attributeResponseData.sku.mandatory.set(key, item);
        //
        //
        //             } else {
        //
        //                 attributeResponseData.sku.nonMandatory.set(key, item);
        //             }
        //         }
        //     });
        //
        //     let mandatoryList = [...attributeResponseData.normal.nonMandatory.keys()];
        //     // let nameAttribute = attributeResponseData.normal.mandatory.get('name')
        //     // console.log("nameAttribute: ", nameAttribute.html);
        //     let mandatoryIsSaleProp = [...attributeResponseData.normal.saleProp.keys()]
        //     let skuIsSaleProp = [... attributeResponseData.sku.saleProp.keys()]
        //
        //     console.log("mandatory.isSaleProp: ", mandatoryIsSaleProp, "sku.isSaleProb", skuIsSaleProp, );
        //     insertDarazSection(mandatoryList)
        // }

        function darazAttributeOperation() {

            const unnecessaryFields = ["product_warranty_en"];
            const systemField = ["name", "description_en", "short_description_en"];

            const productVariantsFields = ['color_family', 'size'];
            const priceAndStockFields = ["price", "quantity", "SellerSku", "seller_promotion", "special_price", "special_from_date", "special_to_date"];

            const serviceAndWarranty = ["warranty_type", "warranty", "product_warranty"];

            const deliveryField = [
                "package_weight",
                "package_length",
                "package_width",
                "package_height",
                "Hazmat",
            ];

            const mergedFields = unnecessaryFields.concat(
                systemField, productVariantsFields, priceAndStockFields, serviceAndWarranty, deliveryField
            );

            const mandatoryFields = filterDataset(attributeResponseData, mergedFields, 1);
            const optionalFields = filterDataset(attributeResponseData, mergedFields, 0);



            insertDarazSection(systemField, '#daraz-system');
            insertDarazSection(productVariantsFields, '#daraz-product-variant');
            insertDarazSection(priceAndStockFields, '#daraz-price-stock');
            insertDarazSection(serviceAndWarranty, '#daraz-service-warranty');
            insertDarazSection(deliveryField, '#daraz-delivery');

            insertDarazSection(mandatoryFields, '#daraz-mandatory');
            insertDarazSection(optionalFields, '#daraz-optional');

            //     =================================
            fetchDarazInputValue();
            fetchDarazSelectValue();
        }
        function filterDataset(dataset, needToExclude, isMandatory) {
            return Array.from(dataset.values())
                .filter(item => item.is_mandatory === isMandatory  && !needToExclude.includes(item.name))
                .map(item => item.name);
        }

        const attributeResponseData = new Map();


        function manipulateProductAttribute(attributes) {
            attributes.data.forEach((attribute) => {
                let payloadPath = getAttributePayloadPath(attribute);
                attribute.payload_path = payloadPath;

                let htmlElement = buildAttributeField(attribute);
                attribute.html = htmlElement;

                let key = attribute.name
                attributeResponseData.set(key, attribute);
            });
        }

        function getAttributePayloadPath(attribute) {
            const normalAttributePath = `Product,Attributes,${attribute.name}`;
            const skuAttributePath = `Product,Skus,Sku,${attribute.name}`;

            return attribute.attribute_type === "normal" ? normalAttributePath : skuAttributePath;
        }

        function buildAttributeField(attribute) {
            let name = attribute.name;

            const payloadPath = attribute.payload_path;
            let options = attribute.options
            let isRequired = attribute.is_mandatory
            let label = attribute.label

            if (options.length === 0) {
                let inputType = getAttributeInputType(attribute);

                return generateInputField(payloadPath, label, name, inputType, isRequired)
            } else if(options.length >  0) {
                let inputType = getAttributeSelectType(attribute);
                return generateSelectField(payloadPath, label, name, inputType, isRequired, options);
            }

            return null;
        }

        function getAttributeInputType(attribute) {
            if (attribute.input_type === 'numeric'){
                return 'multiple'
            }
            return '';
        }

        function getAttributeSelectType(attribute) {
            if (attribute.input_type === 'multiSelect' || attribute.input_type === 'multiEnumInput'){
                return attribute.input_type
            }
            return '';
        }

        function generateInputField(payloadPath, label, name, inputType, isRequired) {
            return `
                <div class="form-group row">
                    <label class="col-md-3 col-from-label">
                        ${label}
                        ${isRequired ? '<span class="text-danger">*</span>' : ''}
                    </label>
                    <div class="col-md-8">
                        <input type="${inputType}" class="form-control aiz-tag-input daraz-input-field" name="${name}" placeholder="" ${isRequired ? 'required' : ''} data-payload-path=${payloadPath}>
                    </div>
                </div>
            `;
        }

        function generateSelectField(payloadPath, label, name, inputType, isRequired, options) {
            let optionsHtml = '';

            options.forEach((option) => {
                optionsHtml += `<option value="${option.name}">${option.name}</option>`;
            });

            return `
                <div class="form-group row">
                    <label class="col-md-3 col-from-label"> ${label}</label>
                    <div class="col-md-8">
                        <select class="form-control aiz-selectpicker daraz-select-field" name="${name}"
                            id="" ${isRequired ? 'required' : ''} data-payload-path=${payloadPath} ${inputType}>
                            ${optionsHtml}
                        </select>
                    </div>
                </div>
            `;
        }

        function insertDarazSection(elementList, whereToInsert='#daraz-dynamic-field') {
            let content = "";

            elementList.forEach((element) => {
                let attribute = attributeResponseData.get(element);
                if (attribute !== undefined) {
                    content += attribute.html
                }
            });

            $(whereToInsert).html(content);

            AIZ.plugins.bootstrapSelect('refresh');

        }

        function fetchDarazInputValue(){
            let inputFields = document.querySelectorAll('.daraz-input-field');

            for(let i=0; i < inputFields.length; i++) {
                inputFields[i].addEventListener('keyup', event => {
                    let payloadPath = event.target.getAttribute('data-payload-path');
                    let value = event.target.value;

                    payloadPath = payloadPath.split(',');
                    let current = productCreatePayload;
                    for (let j = 0; j < payloadPath.length; j++) {
                        if (!current[payloadPath[j]]) {
                            current[payloadPath[j]] = {};
                        }
                        if (j === payloadPath.length - 1) {
                            current[payloadPath[j]] = value;
                        }
                        current = current[payloadPath[j]];
                    }
                    console.log(productCreatePayload);
                });
            }
        };


        function fetchDarazSelectValue(){
            let selectFields = document.querySelectorAll('.daraz-select-field');

            for(let i=0; i < selectFields.length; i++) {

                selectFields[i].addEventListener('change', event => {
                    let payloadPath = event.target.getAttribute('data-payload-path');
                    let value = event.target.value;
                    payloadPath = payloadPath.split(',');

                    let current = productCreatePayload;
                    for (let j = 0; j < payloadPath.length; j++) {
                        if (!current[payloadPath[j]]) {
                            current[payloadPath[j]] = {};
                        }
                        if (j === payloadPath.length - 1) {
                            current[payloadPath[j]] = value;
                        }
                        current = current[payloadPath[j]];
                    }

                    console.log(productCreatePayload);
                });
            }
        };

        let createProductButton = document.getElementById('daraz-create-product-button');

        createProductButton.addEventListener('click', () => {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('test.daraz.create') }}",
                data: {
                    "payload": productCreatePayload
                },
                async: true,
                success: function(response) {
                    if (response.result) {

                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        });

    </script>
@endsection
