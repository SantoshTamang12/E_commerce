<div class="modal fade" id="field-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modelBody">
                    <div class="form-group row">
                        <div class="col-6">
                            <label for="label" class="col-form-label">Label <span class="text-danger">*</span></label>
                            <input type="text" required class="label form-control" id="label" name="name" placeholder="Enter label Name">
                            <span id="new-label" class="text-danger" style="display: none; margin-left: 20px">This field is required.</span>
                        </div>
                        <div class="col-6">
                            <label for="type" class="col-form-label">Type <span class="text-danger">*</span> </label>
                            <select name="type" class="form-control type" required>
                                <option value="">Choose Type</option>
                                <option value="SELECT">SELECT</option>
                                <option value="TEXT">TEXT</option>
                                <option value="TEXT_NUMBER">TEXT_NUMBER</option>
                            </select>
                            <span id="new-type" class="text-danger" style="display: none; margin-left: 20px">This field is required.</span>
                        </div>
                    </div>
                <div class="form-group row">
                    <div class="col-6">
                        <label for="is_price" class="col-form-label">Is Price </label>
                        <input type="checkbox"  placeholder="Enter if is price" id="is_price"  name="is_price" class=" is_price ml-3">
                    </div>
                    <div class="col-6">
                        <label for="required" class="col-form-label">Required </label>
                        <input type="checkbox"  data-single="true" placeholder="Required " id="required"  name="required" class="required ml-3 datepicker">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-12">
                        <div class="col-12 d-flex align-items-center justify-content-between">
                            <label for="options" class="col-form-label">Options </label>
                            <button id="add_options_row" class="btn btn-sm btn-secondary text-white">Add New Row</button>
                        </div>
                        <div class="col-12 row mt-2 align-items-center" id="optionsItems">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="create-submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

