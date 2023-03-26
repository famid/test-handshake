@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Transactions')}}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_products" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('All Transactions') }}</h5>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>

                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('User Name') }}</th>
                        <th>{{translate('Transaction ID')}}</th>
                        <th>{{ translate('Transaction Type') }}</th>
                        <th data-breakpoints="sm">{{translate('Note')}}</th>
                        <th data-breakpoints="lg">{{translate('Transaction At')}}</th>
                        <th data-breakpoints="md">{{translate('Transaction Amount')}}</th>
                        {{-- <th data-breakpoints="sm" class="text-center">{{translate('Options')}}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transaction->User->name }}</td>
                            <td>{{ $transaction->transaction_id }}</td>
                            <td>{{ $transaction->type }}</td>
                            <td>{{ $transaction->note }}</td>
                            <td>{{ date('d-m-Y - H:i:s',strtotime($transaction->created_at)) }}</td>
                            <td class="text-center">{{ currency_symbol().number_format($transaction->transaction_amount,2) }}</td>
                            {{-- <td class="text-center">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" data-toggle="modal" data-target="#transaction_modal" href="#" title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>

                            </td> --}}

                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- <div class="aiz-pagination">
                {{ $packages->appends(request()->input())->links() }}
            </div> --}}
        </div>
    </form>
</div>

{{-- <div class="modal fade" id="transaction_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Transaction Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="transaction-details">

            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('script')
    {{-- <script>
        function showTransaction(id) {

            $.ajax({
                type: "GET",
                url: "{{ route('') }}",
                data: "data",
                dataType: "dataType",
                success: function (response) {

                }
            });
        }
    </script> --}}
@endsection
