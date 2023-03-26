@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Packages Users')}}</h1>
        </div>
        <div class="col text-right">
            <a href="{{ route('package-users.create') }}" class="btn btn-circle btn-info">
                <span>{{translate('Add User')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Packages Users') }}</h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>

                        <th data-breakpoints="lg">#</th>
                        <th>{{translate('User Name')}}</th>
                        <th>{{ translate('Package Name') }}</th>
                        <th>{{ translate('Package Type') }}</th>
                        <th data-breakpoints="sm">{{translate('Subscription Start')}}</th>
                        <th data-breakpoints="md">{{translate('Subscription End')}}</th>
                        <th>{{ translate('Status') }}</th>
                        {{-- <th data-breakpoints="sm" class="text-center">{{translate('Options')}}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($package_users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->User->name }}</td>
                            <td>{{ $user->Package->package_name }}</td>
                            <td>{{ $user->Package->package_type }}</td>
                            <td>{{ date('Y-m-d',strtotime($user->subscription_start)) }}</td>
                            <td>{!! $user->subscription_end != null ? date('Y-m-d',strtotime($user->subscription_end)) : '<i class="las la-infinity fs-20"></i>' !!}</td>
                            <td>
                                @if ($user->status == 1)
                                    <span class="btn btn-sm btn-success">Active</span>
                                @else
                                    <span class="btn btn-sm btn-danger">Deactive</span>
                                @endif
                            </td>
                            {{-- <td class="text-center">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('package-users.edit',$user->id )}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('package.destroy', $user->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td> --}}

                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $package_users->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
