@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{translate('All Cells')}}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{route('cell.create')}}" class="btn btn-primary">
                    <span>{{translate('Add New Cells')}}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_cells" action="" method="GET">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Cells') }}</h5>

                <div class="col-md-2 ml-auto">
                    <select class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0" id="user_type" name="user_type" onchange="filter_cells()">
                        <option value="ALL">{{ translate('All') }}</option>
                        <option value="OWNER">{{ translate('Owner') }}</option>
                        <option value="VENDOR">{{ translate('Vendor') }}</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm" id="search" name="search"
                               @isset($sort_search) value="{{ $sort_search }}" @endisset
                               placeholder="{{ translate('Type cell name & Enter') }}">
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
                    <th>{{ translate('Area Name') }}</th>
                    <th>{{ translate('Shelf Name') }}</th>
                    <th class="text-right">{{ translate('Options') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($cells as $key => $cell)
                    <tr>
                        <td>{{ ($key+1) + ($cells->currentPage() - 1)*$cells->perPage() }}</td>
                        <td>{{ $cell->name }}</td>
                        <td>{{ $cell->code }}</td>
                        <td>{{ $cell->warehouse->name ?? "N/A"}}</td>
                        <td>{{ $cell->area->location->name ?? "N/A"}}</td>
                        <td>{{ $cell->area->name ?? "N/A" }}</td>
                        <td>{{ $cell->shelf->name ?? "N/A" }}</td>

                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                               href="{{ route('cell.edit', $cell->id) }}"
                               title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>

                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                               data-href="{{ route('cell.destroy', $cell->id) }}"
                               title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $cells->appends(request()->input())->links() }}
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

        function filter_cells(el){
            $('#sort_cells').submit();
        }
    </script>
@endsection
