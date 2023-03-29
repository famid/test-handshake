@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All Areas')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('area.create') }}" class="btn btn-primary">
                    <span>{{translate('Add New Areas')}}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_areas" action="" method="GET">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Areas') }}</h5>

                <div class="col-md-2 ml-auto">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_type" name="user_type" onchange="filter_areas()">
                        <option value="ALL">{{ translate('All') }}</option>
                        <option value="OWNER">{{ translate('Owner') }}</option>
                        <option value="VENDOR">{{ translate('Vendor') }}</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               @isset($sort_search) value="{{ $sort_search }}" @endisset
                               placeholder="{{ translate('Type area name & Enter') }}">
                    </div>
                </div>
            </div>
        </form>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ translate('Name') }}</th>
                    <th>{{ translate('Code') }}</th>
                    <th>{{ translate('Warehouse Name') }}</th>
                    <th>{{ translate('Location Name') }}</th>
                    <th>{{ translate('Add Shelf') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($areas as $key => $area)
                    <tr>
                        <td>{{ ($key+1) + ($areas->currentPage() - 1)*$areas->perPage() }}</td>
                        <td>{{ $area->name }}</td>
                        <td>{{ $area->code }}</td>
                        <td>{{ $area->warehouse->name ?? 'N/A' }}</td>
                        <td>{{ $area->location->name ?? 'N/A' }}</td>

                        <td>
                            @if($area->location->warehouse->owner_id == auth()->user()->id)
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                   href="{{route('shelf.create', ['id' => $area->id])}}"
                                   title="{{ translate('Add') }}">
                                    <i class="las la-plus-circle"></i>
                                </a>
                            @endif
                        </td>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               href="{{route('area.edit', $area->id)}}"
                               title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>

                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                               data-href="{{ route('area.destroy', $area->id) }}"
                               title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $areas->appends(request()->input())->links() }}
            </div>

        </div>
    </div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            @if(\Session::has('success'))
            AIZ.plugins.notify('success', "{{\Session::get('success')}}" );

            @elseif(\Session::has('error'))
            AIZ.plugins.notify('danger', "{{\Session::get('error')}}" );
            @endif
        })

        function filter_areas(el){
            $('#sort_areas').submit();
        }
    </script>
@endsection
