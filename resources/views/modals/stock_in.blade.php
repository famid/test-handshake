<!-- delete Modal -->
<div id="stock-in-modal-{{ $inventory_product->id }}" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Add Product in Bin Location: ') }} <strong>{{ $inventory_product->bin_location }}</strong></h4>
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
                                <label class="col-md-3 col-from-label">{{translate('Price')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="number" step=".01" class="form-control" name="price" placeholder="Price" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Variant')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="variant" placeholder="Variant">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Batch no')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="batch_no" placeholder="Batch no" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Purchase date')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <input type="date" class="form-control" name="purchase_date" placeholder="Purchase date" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 justify-content-center">
                            <button type="submit" class="btn btn-soft-success mt-2" data-dismiss="modal">
                                {{translate('Add to Warehouse')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
