@extends('templates.main')
<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffefef;
        color: #F24D4D;
    }
</style>
@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">All Shipments
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shipment Ref</th>
                                <th>Sale Ref</th>
                                <th>Customer</th>
                                <th>Warehouse/Outlet</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $shipements->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="">
                        <div class="col mb-3">
                            <label class="form-label" for="validationDefault01">Name *</label>
                            <input type="text" class="form-control" id="validationDefault01" required
                                placeholder="input product cost">
                        </div>
                        <div class="col mb-3">
                            <label class="form-label" for="validationDefault01">Short Name*</label>
                            <input type="text" class="form-control" id="validationDefault01" required
                                placeholder="input product cost">
                        </div>
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h4 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Base Unit
                                    </button>
                                </h4>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <label for="validationCustomUsername" class="form-label">Product Unit</label>
                                        <select class="form-select" id="validationDefault04" required>
                                            <option selected disabled value="">Choose...</option>
                                            <option>Gram</option>
                                            <option>Liter</option>
                                            <option>Meter</option>
                                            <option>Gram</option>
                                        </select>
                                    </div>
                                    <div class="accordion-body">
                                        <label for="validationCustomUsername" class="form-label">Operator</label>
                                        <select class="form-select" id="validationDefault04" required>
                                            <option selected disabled value="">Choose...</option>
                                            <option>Multiply (*)</option>
                                            <option>Devide (/)</option>
                                        </select>
                                    </div>
                                    <div class="accordion-body">
                                        <label class="form-label" for="validationDefault01">Operation value *</label>
                                        <input type="text" class="form-control" id="validationDefault01" required
                                            placeholder="input product cost">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection
