@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Packages')}}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('package.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add New Package')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Packages') }}</h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>

                        <th data-breakpoints="lg">#</th>
                        <th>{{translate('Package Name')}}</th>
                        <th>{{ translate('Package Type') }}</th>
                        <th>{{ translate('Package Price') }}</th>
                        <th data-breakpoints="sm">{{translate('Package Duration')}}</th>
                        <th data-breakpoints="md">{{translate('Product Limit')}}</th>
                        <th data-breakpoints="lg">{{translate('Warehouse Limit')}}</th>
                        <th data-breakpoints="lg">{{translate('Daraz Sync Limit')}}</th>
                        <th>{{ translate('Status') }}</th>
                        <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $package->package_name }}</td>
                            <td>{{ $package->package_type }}</td>
                            <td>{{ $package->package_price }}</td>
                            <td>{{ $package->package_duration }}</td>
                            <td>{{ $package->product_limit }}</td>
                            <td>{{ $package->warehouse_limit }}</td>
                            <td>{{ $package->daraz_sync_limit }}</td>
                            <td>
                                @if ($package->status == 1)
                                    <span class="btn btn-sm btn-success">Active</span>
                                @else
                                    <span class="btn btn-sm btn-danger">Deactive</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('package.edit',$package->id )}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('package.destroy', $package->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $packages->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
