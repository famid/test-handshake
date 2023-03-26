<!-- delete Modal -->
<div id="add-product-in-warehouse-modal" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Add Inventory Product in New Location')}}</h4>
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

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Select Warehouse')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="warehouse_id" placeholder="Select Warehouse" data-live-search="true" required>
                                        @foreach ($ownWarehouses as $key => $warehouse)
                                            <option value="{{ $warehouse->id  }}"> {{ $warehouse->name . ' (' . $warehouse->code . ')' }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Select Location')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="location_id" placeholder="Select Location" data-live-search="true" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Select Area')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="area_id" placeholder="Select Area" data-live-search="true" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Select Shelf')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="shelf_id" placeholder="Select Shelf" data-live-search="true" required>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Select Cell')}} <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                    <select class="form-control aiz-selectpicker" name="cell_id" placeholder="Select Cell" data-live-search="true" required>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 justify-content-center">
                            <button type="submit" class="btn btn-soft-secondary mt-2" data-dismiss="modal">
                                {{translate('Add to Warehouse')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->

