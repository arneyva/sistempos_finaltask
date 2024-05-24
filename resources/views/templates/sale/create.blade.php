@extends('templates.main')
@section('content')
    {{-- part 1 --}}
    <div class="col-md-12 col-lg-12">
    </div>
    {{-- part 2  sisi kiri --}}
    <div class="col-md-12">
        <div class="row">
            {{-- part --}}
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Create Sale</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('sale.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="selectWarehouse">From Warehouse/Outlet *</label>
                                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="customer">Customer *</label>
                                    <select class="form-select" id="customer" name="client_id" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($client as $cl)
                                            <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">Date *</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectProduct">Product *</label>
                                    <select class="form-select" id="selectProduct" disabled>
                                        <option selected disabled value="">Choose warehouse first...</option>
                                    </select>
                                </div>
                                <!-- Tambahkan bagian untuk menampilkan tabel produk -->
                                <!-- Dalam contoh ini, tabel produk akan ditampilkan di bawah dropdown produk -->
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Net Unit Price</th>
                                                    <th>Stock</th>
                                                    <th>Quantity</th>
                                                    <th>Discount</th>
                                                    <th>Tax</th>
                                                    <th>Subtotal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                <!-- Isi dari tbody akan diisi secara dinamis menggunakan JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3"></div>
                                <div class="col-md-6 mb-3">
                                    <table id="basic-table" class="table table-hover table-bordered table-sm"
                                        role="grid">
                                        <tbody>
                                            <tr>
                                                <td>Order Tax</td>
                                                <th>1 %</th>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <th>Rp 10000</th>
                                            </tr>
                                            <tr>
                                                <td>Shipping</td>
                                                <th>Rp 10000</th>
                                            </tr>
                                            <tr>
                                                <td>Grand Total</td>
                                                <th>Rp 10000</th>
                                            </tr>
                                    </table>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="codebaseproduct">Order Tax *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="codebaseproduct"
                                                    placeholder="input tax" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">%</span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="codebaseproduct">Discount *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="codebaseproduct"
                                                    placeholder="input discount" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="codebaseproduct">Shipping *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="codebaseproduct"
                                                    placeholder="input shipping" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        {{-- <div class="col-md-4 mb-3">
                                            <label class="form-label" for="brand">Status</label>
                                            <select class="form-select select2" id="typeStatus" required name="status"
                                                data-placeholder="Select a Brand ">
                                                <option value="completed">Completed</option>
                                                <option value="pending">Pending</option>
                                                <option value="ordered">Ordered</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="brand">Payment Status</label>
                                            <select class="form-select select2" required id="typePaymentStatus"
                                                name="brand_id" data-placeholder="Select a Brand ">
                                                <option value="pending">Pending</option>
                                                <option value="paid">Paid</option>
                                                <option value="partial">Partial</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="paymentChoice">
                                            <label class="form-label" for="brand">Payment Choice</label>
                                            <select class="form-select select2" required name="brand_id"
                                                data-placeholder="Select a Brand ">
                                                <option value="">Cash</option>
                                                <option value="">Credit Card</option>
                                                <option value="">Other</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="receivedAmount">
                                            <label class="form-label" for="codebaseproduct">Received Amount *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input tax" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="payingAmount">
                                            <label class="form-label" for="codebaseproduct">Paying Amount *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input discount" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="changeReturn">
                                            <label class="form-label" for="codebaseproduct">Change Return *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input shipping" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div> --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="brand">Status</label>
                                            <select class="form-select select2" id="typeStatus" required name="status"
                                                data-placeholder="Select a Brand">
                                                <option value="completed" selected>Completed</option>
                                                <option value="pending">Pending</option>
                                                <option value="ordered">Ordered</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="paymentStatusContainer">
                                            <label class="form-label" for="brand">Payment Status</label>
                                            <select class="form-select select2" required id="typePaymentStatus"
                                                name="brand_id" data-placeholder="Select a Brand">
                                                <option value="pending" selected>Pending</option>
                                                <option value="paid">Paid</option>
                                                <option value="partial">Partial</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="paymentChoice">
                                            <label class="form-label" for="brand">Payment Choice</label>
                                            <select class="form-select select2" required name="brand_id"
                                                data-placeholder="Select a Brand">
                                                <option value="">Cash</option>
                                                <option value="">Credit Card</option>
                                                <option value="">Other</option>
                                            </select>
                                            @error('brand_id')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="receivedAmount">
                                            <label class="form-label" for="codebaseproduct">Received Amount *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input tax" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="payingAmount">
                                            <label class="form-label" for="codebaseproduct">Paying Amount *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input discount" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-3" id="changeReturn">
                                            <label class="form-label" for="codebaseproduct">Change Return *</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" required
                                                    placeholder="input shipping" name="code"
                                                    value="{{ Session::get('code') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                            </div>
                                            @error('code')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationDefault05">Description</label>
                                    <input type="text" class="form-control" id="validationDefault05" name="notes"
                                        required placeholder="a few words...">
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <button class="btn btn-primary" type="submit">Submit form</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // status
            var typeStatus = document.getElementById('typeStatus');
            // payment status
            var typePaymentStatus = document.getElementById('typePaymentStatus');
            var paymentChoice = document.getElementById('paymentChoice');
            var receivedAmount = document.getElementById('receivedAmount');
            var payingAmount = document.getElementById('payingAmount');
            var changeReturn = document.getElementById('changeReturn');

            typeStatus.addEventListener('change', function() {
                var selectedTypeStatus = this.value;
                if (selectedTypeStatus === 'completed') {
                    typePaymentStatus.style.display = 'block'; //terlihat
                } else {
                    typePaymentStatus.style.display = 'none';
                }
            })

            typePaymentStatus.addEventListener('change', function() {
                var selectedTypePaymentStatus = this.value;
                if (selectedTypePaymentStatus === 'paid') {
                    paymentChoice.style.display = 'none';
                    receivedAmount.style.display = 'none';
                    payingAmount.style.display = 'none';
                    changeReturn.style.display = 'none';
                } else if (selectedTypePaymentStatus === 'partial') {
                    paymentChoice.style.display = 'block';
                    receivedAmount.style.display = 'block';
                    payingAmount.style.display = 'block';
                    changeReturn.style.display = 'block';
                } else if (selectedTypePaymentStatus === 'pending') {
                    paymentChoice.style.display = 'none';
                    receivedAmount.style.display = 'none';
                    payingAmount.style.display = 'none';
                    changeReturn.style.display = 'none';
                }

            })
        })
    </script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // status
            var typeStatus = document.getElementById('typeStatus');
            // payment status
            var typePaymentStatus = document.getElementById('typePaymentStatus');
            var paymentChoice = document.getElementById('paymentChoice');
            var receivedAmount = document.getElementById('receivedAmount');
            var payingAmount = document.getElementById('payingAmount');
            var changeReturn = document.getElementById('changeReturn');

            function updateVisibility() {
                var selectedTypeStatus = typeStatus.value;
                var selectedTypePaymentStatus = typePaymentStatus.value;

                if (selectedTypeStatus !== 'completed') {
                    typePaymentStatus.style.display = 'none';
                    paymentChoice.style.display = 'none';
                    receivedAmount.style.display = 'none';
                    payingAmount.style.display = 'none';
                    changeReturn.style.display = 'none';
                } else {
                    typePaymentStatus.style.display = 'block';
                    if (selectedTypePaymentStatus === 'paid') {
                        paymentChoice.style.display = 'block';
                        receivedAmount.style.display = 'block';
                        payingAmount.style.display = 'block';
                        changeReturn.style.display = 'block';
                    } else if (selectedTypePaymentStatus === 'partial') {
                        paymentChoice.style.display = 'block';
                        receivedAmount.style.display = 'block';
                        payingAmount.style.display = 'block';
                        changeReturn.style.display = 'block';
                    } else if (selectedTypePaymentStatus === 'pending') {
                        paymentChoice.style.display = 'none';
                        receivedAmount.style.display = 'none';
                        payingAmount.style.display = 'none';
                        changeReturn.style.display = 'none';
                    }
                }
            }

            typeStatus.addEventListener('change', updateVisibility);
            typePaymentStatus.addEventListener('change', updateVisibility);

            // Initial call to set visibility on page load
            updateVisibility();
        });
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // status
            var typeStatus = document.getElementById('typeStatus');
            // payment status container
            var paymentStatusContainer = document.getElementById('paymentStatusContainer');
            var typePaymentStatus = document.getElementById('typePaymentStatus');
            var paymentChoice = document.getElementById('paymentChoice');
            var receivedAmount = document.getElementById('receivedAmount');
            var payingAmount = document.getElementById('payingAmount');
            var changeReturn = document.getElementById('changeReturn');

            function updateVisibility() {
                var selectedTypeStatus = typeStatus.value;
                var selectedTypePaymentStatus = typePaymentStatus.value;

                if (selectedTypeStatus !== 'completed') {
                    paymentStatusContainer.style.display = 'none';
                    paymentChoice.style.display = 'none';
                    receivedAmount.style.display = 'none';
                    payingAmount.style.display = 'none';
                    changeReturn.style.display = 'none';
                } else {
                    paymentStatusContainer.style.display = 'block';
                    if (selectedTypePaymentStatus === 'paid') {
                        paymentChoice.style.display = 'block';
                        receivedAmount.style.display = 'block';
                        payingAmount.style.display = 'block';
                        changeReturn.style.display = 'block';
                    } else if (selectedTypePaymentStatus === 'partial') {
                        paymentChoice.style.display = 'block';
                        receivedAmount.style.display = 'block';
                        payingAmount.style.display = 'block';
                        changeReturn.style.display = 'block';
                    } else if (selectedTypePaymentStatus === 'pending') {
                        paymentChoice.style.display = 'none';
                        receivedAmount.style.display = 'none';
                        payingAmount.style.display = 'none';
                        changeReturn.style.display = 'none';
                    }
                }
            }

            typeStatus.addEventListener('change', updateVisibility);
            typePaymentStatus.addEventListener('change', updateVisibility);

            // Initial call to set visibility on page load
            updateVisibility();
        });
    </script>

    <script>
        // Fungsi untuk menambahkan event listener untuk tombol delete di dalam tbody
        $(document).ready(function() {
            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr')
                    .remove(); // Menghapus baris tabel yang berisi tombol delete yang diklik
            });
            // Event listener untuk perubahan pada pilihan gudang
            $('#selectWarehouse').on('change', function() {
                var warehouseId = $(this).val();
                if (warehouseId) {
                    $.ajax({
                        url: '/adjustment/get_Products_by_warehouse/' + warehouseId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#selectProduct').empty().append(
                                '<option selected disabled value="">Choose...</option>');
                            $.each(data, function(key, value) {
                                $('#selectProduct').append('<option value="' + value
                                    .id +
                                    '" data-variant-id="' + value
                                    .product_variant_id + '">' +
                                    value.name + '</option>');
                            });
                            $('#selectProduct').prop('disabled', false);
                        }
                    });
                } else {
                    $('#selectProduct').empty().prop('disabled', true);
                }
            });

            // Event listener untuk perubahan pada pilihan produk
            $('#selectProduct').on('change', function() {
                var productId = $(this).val();
                var warehouseId = $('#selectWarehouse').val();
                var variantId = $(this).find(':selected').data('variant-id');

                // Periksa jika variantId adalah null, maka atur nilai variantId menjadi null
                if (!variantId) {
                    variantId = null;
                }

                if (productId && warehouseId) {
                    $.ajax({
                        url: '/adjustment/show_product_data/' + productId + '/' + variantId + '/' +
                            warehouseId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            // Buat objek untuk baris tabel
                            var row = '<tr>';
                            row += '<td>#</td>';
                            row += '<td>' + data.code + '</td>';
                            row += '<td>' + data.name + '</td>';
                            row += '<td>' + data.qty + '</td>';
                            row +=
                                '<td><input type="number" class="form-control" name="details[' +
                                data
                                .id + '][quantity]" value="0" min="0"></td>';
                            row += '<td><select class="form-select" name="details[' + data.id +
                                '][type]"><option value="add">Add</option><option value="sub">Subtract</option></select></td>';
                            row += '<td><input type="hidden" name="details[' + data.id +
                                '][product_id]" value="' + data.id + '"></td>';
                            row += '<td><input type="hidden" name="details[' + data.id +
                                '][product_variant_id]" value="' + (variantId || '') +
                                '"></td>';
                            row +=
                                '<td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>'; // Tombol delete ditambahkan di sini
                            row += '</tr>';

                            // Masukkan baris ke dalam tbody
                            $('#product-table-body').append(row);
                        }
                    });
                }
            });
        });
    </script>
@endpush
