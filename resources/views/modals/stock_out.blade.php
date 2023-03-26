<!-- delete Modal -->
<div id="stock-out-modal-{{ $inventory_product->id }}" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Remove Product from Warehouse Location: ') }}<strong>{{ $inventory_product->bin_location }}</strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <form class="form form-horizontal mar-top" action="" method="POST" enctype="multipart/form-data" id="choice_form">
                    <div class="row gutters-5">
                        <div class="col-lg-12">
                            @csrf
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Quantity')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" step="1" class="form-control" name="quantity" placeholder="Quantity" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Date')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="date" class="form-control" name="date" placeholder="Date" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 justify-content-center">
                            <button type="submit" class="btn btn-soft-danger mt-2" data-dismiss="modal">
                                {{translate('Stock Out of Warehouse')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
