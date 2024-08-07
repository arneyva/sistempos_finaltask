@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Edit Sales') }}</h1>
    <p>{{ __('Manage your product sales easily and efficiently') }}</p>
@endsection
<style>
    /* Custom CSS to ensure proper height */
    .select2-container .select2-selection--single {
        height: 38px !important;
        /* Adjust this value as needed */
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        /* Match this value with the height */
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        /* Match this value with the height */
    }

    .hidden-input {
        display: none;
    }
</style>
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
                            <h4 class="card-title">{{ __('Edit Sales') }}</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('sale.update', ['id' => $sale['id']]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="selectWarehouse">{{ __('From Warehouse/Outlet') }}
                                        *</label>
                                    <input type="text" class="form-control" id="selectWarehouseName"
                                        value="{{ $warehouse->firstWhere('id', $sale['warehouse_id'])->name }}" readonly>
                                    <input type="hidden" id="selectWarehouse" name="warehouse_id"
                                        value="{{ $sale['warehouse_id'] }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="customer">{{ __('Customer *') }}</label>
                                    <input type="text" class="form-control" id="selectWarehouseName"
                                        value="{{ $sale['client_name'] }}" readonly>
                                    <input type="hidden" id="selectWarehouse" name="client_id"
                                        value="{{ $sale['client_id'] }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="score">{{ __('Score') }}</label>
                                    <input type="text" id="score" class="form-control" value="{{ $sale['score'] }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Date *') }}</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectProduct">{{ __('Product *') }}</label>
                                    <select class="form-select" id="selectProduct" disabled>
                                        <option selected disabled value="">
                                            {{ __('Scan/Search Product by Code or Name') }}.</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Product') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Initial Stock') }}</th>
                                                    <th>{{ __('Stock') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Discount') }}</th>
                                                    <th>{{ __('Tax') }}</th>
                                                    <th>{{ __('Subtotal') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                @foreach ($details as $index => $detail)
                                                    <tr>
                                                        <td>#</td>
                                                        <td>{{ $detail['code'] }} ~ {{ $detail['name'] }}</td>
                                                        <td>{{ 'Rp ' . number_format($detail['Unit_price'], 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ $detail['initial_stock'] }} {{ $detail['unitSale'] }}</td>
                                                        <td>{{ $detail['stock'] }} {{ $detail['unitSale'] }}</td>
                                                        <td>
                                                            <input type="number" class="form-control item-quantity"
                                                                name="details[{{ $index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0"
                                                                data-unit-price="{{ $detail['Unit_price'] }}"
                                                                data-tax-percent="{{ $detail['tax_percent'] }}"
                                                                data-tax-method="{{ $detail['tax_method'] }}"
                                                                data-max-quantity="{{ $detail['stock'] }}">
                                                        </td>
                                                        <td class="item-discount">
                                                            {{ 'Rp ' . number_format($detail['DiscountNet'], 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ 'Rp ' . number_format($detail['taxe'], 0, ',', '.') }}</td>
                                                        <td class="item-total">
                                                            {{ 'Rp ' . number_format($detail['total'], 0, ',', '.') }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 48 48">
                                                                    <g fill="none" stroke="#FFFFFF"
                                                                        stroke-linejoin="round" stroke-width="4">
                                                                        <path d="M9 10v34h30V10z" />
                                                                        <path stroke-linecap="round"
                                                                            d="M20 20v13m8-13v13M4 10h40" />
                                                                        <path d="m16 10l3.289-6h9.488L32 10z" />
                                                                    </g>
                                                                </svg>
                                                            </button>
                                                        </td>
                                                        <td class="hidden-input">
                                                            <input type="hidden" class="item-subtotal"
                                                                name="details[{{ $index }}][subtotal]"
                                                                value="{{ $detail['total'] }}">
                                                            <input type="hidden" class="item-subdiscount"
                                                                name="details[{{ $index }}][discount]"
                                                                value="{{ $detail['discount'] }}">
                                                            <input type="hidden" class="item-subdiscountmethod"
                                                                name="details[{{ $index }}][discount_method]"
                                                                value="{{ $detail['discount_method'] }}">
                                                            <input type="hidden" name="details[{{ $index }}][id]"
                                                                value="{{ $detail['id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][no_unit]"
                                                                value="{{ $detail['no_unit'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][sale_unit_id]"
                                                                value="{{ $detail['sale_unit_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][product_variant_id]"
                                                                value="{{ $detail['product_variant_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][product_id]"
                                                                value="{{ $detail['product_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][Unit_price]"
                                                                value="{{ $detail['Unit_price'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][tax_percent]"
                                                                value="{{ $detail['tax_percent'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][tax_method]"
                                                                value="{{ $detail['tax_method'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][quantity_discount]"
                                                                value="{{ $detail['quantity_discount'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][discount_percentage]"
                                                                value="{{ $detail['discount_percentage'] }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <table id="basic-table" class="table table-hover table-bordered table-sm"
                                        role="grid">
                                        <tbody>
                                            <tr>
                                                <td>{{ __('Order Tax') }}</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Discount') }}</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Shipping') }}</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Membership') }}</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Grand Total') }}</td>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="tax_rate">{{ __('Order Tax') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="tax_rate"
                                                    placeholder="input tax" name="tax_rate"
                                                    value="{{ $sale['tax_rate'] }}">
                                                <span class="input-group-text" id="basic-addon1">%</span>
                                            </div>
                                            @error('sale.tax_rate')
                                                <div class="alert alert-right alert-warning alert-dismissible fade show mb-3"
                                                    role="alert" style="padding: 1px 1px 1px 1px; margin-top: 3px">
                                                    <span style="margin-left: 3px"> {{ $message }}</span>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="alert" aria-label="Close"
                                                        style="padding: 1px 1px 1px 1px; margin-top: 7px; margin-right: 3px;height: 10px"></button>
                                                </div>
                                            @enderror
                                        </div>
                                        <input type="hidden" class="form-control" id="tax_net"
                                            placeholder="input tax net" name="TaxNet" value="{{ $sale['TaxNet'] }}">
                                        <input class="" type="hidden" id="grandTotal" name="GrandTotal"
                                            value="{{ $sale['GrandTotal'] }}">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="discount">{{ __('Discount') }}</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="discount"
                                                    placeholder="input discount" name="discount"
                                                    value="{{ $sale['discount'] }}">
                                                <input type="hidden" id="discount_value" name="discount_value">
                                            </div>
                                            @error('sale.discount')
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
                                            <label class="form-label" for="shipping">{{ __('Shipping') }}</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="shipping"
                                                    placeholder="input shipping" name="shipping"
                                                    value="{{ $sale['shipping'] }}">
                                                <input type="hidden" id="shipping_value" name="shipping_value">
                                            </div>
                                            @error('sale.shipping')
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
                                            <label class="form-label" for="typeStatus">{{ __('Status *') }}</label>
                                            <select class="form-select select2" id="typeStatus" required name="statut"
                                                data-placeholder="Select a Status">
                                                <option value="completed"
                                                    {{ $sale['statut'] == 'completed' ? 'selected' : '' }}>
                                                    {{ __('Completed') }}
                                                </option>
                                                <option value="pending"
                                                    {{ $sale['statut'] == 'pending' ? 'selected' : '' }}>
                                                    {{ __('Pending') }}</option>
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
                                        <div class="col-md-4 mb-3" id="paymentMethod">
                                            <label class="form-label"
                                                for="payment_method">{{ __('Payment Method *') }}</label>
                                            <select class="form-select select2" name="payment_method" id="payment_method"
                                                data-placeholder="Select a payment_method">
                                                <option value="cash"
                                                    {{ $sale['payment_method'] == 'cash' ? 'selected' : '' }}>
                                                    {{ __('Cash') }}</option>
                                                <option value="midtrans"
                                                    {{ $sale['payment_method'] == 'midtrans' ? 'selected' : '' }}>
                                                    {{ __('Via Midtrans') }}
                                                </option>
                                            </select>
                                            @error('payment_method')
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
                                            <label class="form-label"
                                                for="received_amount">{{ __('Received Amount') }}</label>
                                            <div class="form-group input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                                <input type="text" class="form-control"
                                                    placeholder="input received amount" id="received_amount"
                                                    name="received_amount" value="{{ Session::get('received_amount') }}">
                                            </div>
                                            @error('received_amount')
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
                                            <label class="form-label"
                                                for="paying_amount">{{ __('Paying Amount') }}</label>
                                            <div class="form-group input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                                <input type="text" class="form-control"
                                                    placeholder="input paying amount" id="paying_amount"
                                                    name="paying_amount" value="{{ Session::get('paying_amount') }}">
                                            </div>
                                            @error('paying_amount')
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
                                            <label class="form-label"
                                                for="change_return">{{ __('Change Return') }}</label>
                                            <div class="form-group input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                                <input type="text" class="form-control"
                                                    placeholder="input change return" id="change_return"
                                                    name="change_return" value="{{ Session::get('change_return') }}"
                                                    readonly>
                                                <input type="hidden" id="change_return_hidden"
                                                    name="change_return_value">
                                            </div>
                                            @error('change_return')
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
                                    <label class="form-label"
                                        for="exampleFormControlTextarea1">{{ __('Note') }}</label>
                                    <input type="text" class="form-control" id="exampleFormControlTextarea1"
                                        rows="3" name="notes" value="{{ $sale['notes'] }}">
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Submit form') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Customer Dropdown
            $('#customer').select2({
                placeholder: "Choose a customer...",
                allowClear: true
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var statusDropdown = document.getElementById('typeStatus');
            var paymentMethod = document.getElementById('payment_method');
            var paymentMethodContainer = document.getElementById('paymentMethod');
            var receivedAmount = document.getElementById('receivedAmount');
            var payingAmount = document.getElementById('payingAmount');
            var changeReturn = document.getElementById('changeReturn');

            function updateVisibility() {
                var selectedStatus = statusDropdown.value;
                var selectedPaymentMethod = paymentMethod.value;

                if (selectedStatus === 'completed') {
                    paymentMethodContainer.style.display = 'block';

                    if (selectedPaymentMethod === 'cash') {
                        receivedAmount.style.display = 'block';
                        payingAmount.style.display = 'block';
                        changeReturn.style.display = 'block';
                    } else {
                        receivedAmount.style.display = 'none';
                        payingAmount.style.display = 'none';
                        changeReturn.style.display = 'none';
                    }
                } else {
                    paymentMethodContainer.style.display = 'none';
                    receivedAmount.style.display = 'none';
                    payingAmount.style.display = 'none';
                    changeReturn.style.display = 'none';
                }
            }
            // xyzab
            var cleavereceived_amount = new Cleave('#received_amount', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                prefix: 'Rp ',
                delimiter: '.'
            });

            var cleaverepaying_amount = new Cleave('#paying_amount', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                prefix: 'Rp ',
                delimiter: '.'
            });

            // Fungsi untuk format Rupiah
            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function getNumericValue(elementId) {
                var formattedValue = $('#' + elementId).val();
                var numericValue = formattedValue.replace(/[^\d]/g, ''); // Hapus karakter non-digit
                return parseFloat(numericValue) || 0;
            }

            // Fungsi untuk menghitung kembalian
            function calculateChange() {
                var received = getNumericValue('received_amount');
                var paying = getNumericValue('paying_amount');
                var change = received - paying;
                document.getElementById('change_return').value = formatRupiah(change >= 0 ? change : 0);
                // Set numeric value in hidden input
                document.getElementById('change_return_hidden').value = change >= 0 ? change : 0;
            }

            // Event listener untuk perubahan nilai pada input received amount dan paying amount
            document.getElementById('received_amount').addEventListener('input', calculateChange);
            document.getElementById('paying_amount').addEventListener('input', calculateChange);

            // Update visibility berdasarkan status dan metode pembayaran
            statusDropdown.addEventListener('change', updateVisibility);
            paymentMethod.addEventListener('change', updateVisibility);
            updateVisibility();
        });
    </script>
    <script>
        $(document).ready(function() {
            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            let newIndex = 0;
            // Initial update on page load
            updateGrandTotal();
            // Delete row event handler
            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateGrandTotal();
            });
            $('#selectProduct').select2({
                placeholder: 'Scan/Search Product by Code or Name',
                allowClear: true,
                matcher: function(params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined' || typeof $(data.element).data('code') ===
                        'undefined') {
                        return null;
                    }
                    var term = params.term.toLowerCase();
                    var text = data.text.toLowerCase();
                    var code = $(data.element).data('code').toString().toLowerCase();

                    if (text.indexOf(term) > -1 || code.indexOf(term) > -1) {
                        return data;
                    }
                    return null;
                }
            });

            // Tambahkan event listener untuk fokus pada input pencarian saat dropdown dibuka
            $('#selectProduct').on('select2:open', function() {
                setTimeout(function() {
                    document.querySelector('.select2-search__field').focus();
                }, 100); // Penundaan 100ms sebelum fokus pada input pencarian
            });
            $('#selectWarehouse').on('change', function() {
                var warehouseId = $(this).val();
                if (warehouseId) {
                    // Mengirimkan nilai warehouse_id ke server
                    $('input[name="warehouse_id"]').val(warehouseId);
                    loadProductsByWarehouse(warehouseId);
                } else {
                    $('#selectProduct').empty().prop('disabled', true);
                }
            });
            // Select product event handler
            $('#selectProduct').on('change', function() {
                var productId = $(this).val();
                var warehouseId = $('#selectWarehouse').val();
                var variantId = $(this).find(':selected').data('variant-id') || null;

                if (productId && warehouseId) {
                    var isDuplicate = false;
                    $('#product-table-body tr').each(function() {
                        var existingProductId = $(this).find('input[name$="[product_id]"]').val();
                        var existingVariantId = $(this).find('input[name$="[product_variant_id]"]')
                            .val() || null;
                        if (existingProductId == productId && existingVariantId == variantId) {
                            isDuplicate = true;
                            $('#selectProduct').val('').trigger('change');
                            return false; // Stop loop
                        }
                    });

                    if (isDuplicate) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Produk sudah ditambahkan.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    } else {
                        $.ajax({
                            url: '/adjustment/show_product_data/' + productId + '/' + variantId +
                                '/' + warehouseId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                var initialQuantity = 1;
                                var initialTotal = initialQuantity * data.Unit_price +
                                    initialQuantity * data.tax_price;
                                var formattedUnitPrice = formatRupiah(data.Unit_price);
                                var formattedTaxPrice = formatRupiah(data.tax_price);
                                var formattedInitialTotal = formatRupiah(initialTotal);
                                var row = '<tr>';
                                row += '<td>#</td>';
                                row += '<td>' + data.code + ' ~ ' + data.name + '</td>';
                                row += '<td>' + formattedUnitPrice + '</td>';
                                row += '<td>' + 'New Data' + '</td>';
                                row += '<td>' + data.qty + ' ' + data.unitSale + '</td>';
                                row +=
                                    '<td><input type="number" class="form-control item-quantity" name="details[new-' +
                                    newIndex + '][quantity]" value="' + initialQuantity +
                                    '" data-min-quantity="1" data-max-quantity="' + data.qty +
                                    '"></td>';
                                row += '<td class="item-discount">Rp 0</td>';
                                row += '<td>' + formattedTaxPrice + '</td>';
                                row += '<td class="item-total">' + formattedInitialTotal +
                                    '</td>';
                                row +=
                                    '<td><button type="button" class="btn btn-danger btn-sm delete-row">';
                                row +=
                                    '<svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 48 48">';
                                row +=
                                    '<g fill="none" stroke="#FFFFFF" stroke-linejoin="round" stroke-width="4">';
                                row += '<path d="M9 10v34h30V10z" />';
                                row +=
                                    '<path stroke-linecap="round" d="M20 20v13m8-13v13M4 10h40" />';
                                row += '<path d="m16 10l3.289-6h9.488L32 10z" />';
                                row += '</g>';
                                row += '</svg>';
                                row += '</button></td>';
                                row += '<td class="hidden-input">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][id]" value="new">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][no_unit]" value="1">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][product_id]" value="' + data.id + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][product_variant_id]" value="' + (variantId || '') + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][sale_unit_id]" value="' + data.sale_unit_id + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][Unit_price]" value="' + data.Unit_price + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][tax_percent]" value="' + data.tax_percent + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][tax_method]" value="' + data.tax_method + '">';
                                row +=
                                    '<input type="hidden" class="item-subtotal" name="details[new-' +
                                    newIndex + '][subtotal]" value="' + initialTotal +
                                    '">';
                                row +=
                                    '<input type="hidden" class="item-subdiscount" name="details[new-' +
                                    newIndex + '][discount]" value="0">';
                                row +=
                                    '<input type="hidden" class="item-subdiscountmethod" name="details[new-' +
                                    newIndex + '][discount_method]" value="nodiscount">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][quantity_discount]" value="' + data.quantity_discount +
                                    '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][discount_percentage]" value="' + data
                                    .discount_percentage + '">';
                                row += '</td>';
                                row += '</tr>';

                                $('#product-table-body').append(row);
                                newIndex++;
                                updateGrandTotal();
                                // Reset dropdown produk setelah menambahkan produk ke tabel
                                $('#selectProduct').val('').trigger('change');
                            }
                        });
                    }
                }
            });

            function loadProductsByWarehouse(warehouseId) {
                if (warehouseId) {
                    $.ajax({
                        url: '/adjustment/get_Available_Products_by_warehouse/' + warehouseId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            $('#selectProduct').empty().append(
                                '<option selected disabled value="">Choose...</option>');
                            $.each(data, function(key, value) {
                                $('#selectProduct').append('<option value="' + value.id +
                                    '" data-variant-id="' + value.product_variant_id +
                                    '">' + value.name + '</option>');
                            });
                            $('#selectProduct').prop('disabled', false);
                        }
                    });
                } else {
                    $('#selectProduct').empty().prop('disabled', true);
                }
            }

            var initialWarehouseId = $('#selectWarehouse').val();
            if (initialWarehouseId) {
                loadProductsByWarehouse(initialWarehouseId);
            }
            // Item quantity change event handler
            $('#product-table-body').on('input', '.item-quantity', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat($(this).val()) || 0;
                var maxQuantity = parseFloat($(this).data('max-quantity')) || 0;
                var minQuantity = parseFloat($(this).data('min-quantity')) || 1;

                if (quantity > maxQuantity) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'The quantity cannot exceed the available stock.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    $(this).val(maxQuantity);
                    quantity = maxQuantity;
                }
                if (quantity < minQuantity) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'The quantity cannot be less than 1.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    $(this).val(minQuantity);
                    quantity = minQuantity;
                }

                // Mengambil nilai unitPrice, taxPrice, quantityDiscount, dan discountPercentage dari elemen HTML
                var unitPrice = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '').replace(/\./g,
                    '')) || 0;
                var taxPrice = parseFloat(row.find('td:eq(7)').text().replace('Rp ', '').replace(/\./g,
                    '')) || 0;
                var quantityDiscount = parseFloat(row.find('input[name$="[quantity_discount]"]').val()) ||
                    0;
                var discountPercentage = parseFloat(row.find('input[name$="[discount_percentage]"]')
                    .val()) || 0;
                var discount = 0;

                // Menghitung diskon jika kuantitas memenuhi syarat
                if (quantity >= quantityDiscount) {
                    discount = (unitPrice * quantity) * (discountPercentage / 100);
                    row.find('.item-discount').text(formatRupiah(discount.toFixed(
                        0))); // Menggunakan toFixed(0) jika tidak menggunakan koma
                    row.find('.item-subdiscount').val(discount.toFixed(
                        0)); // Menggunakan toFixed(0) jika tidak menggunakan koma
                    row.find('.item-subdiscountmethod').val('discount');
                } else {
                    row.find('.item-discount').text('Rp 0');
                    row.find('.item-subdiscount').val('0');
                    row.find('.item-subdiscountmethod').val('nodiscount');
                }

                // Menghitung totalPrice
                var totalPrice = (unitPrice + taxPrice) * quantity - discount;
                row.find('.item-total').text(formatRupiah(totalPrice.toFixed(
                    0))); // Menggunakan toFixed(0) jika tidak menggunakan koma
                console.log("Total Price:", totalPrice.toFixed(
                    0));
                row.find('.item-subtotal').val(totalPrice.toFixed(
                    0)); // Menggunakan toFixed(0) jika tidak menggunakan koma

                // Memperbarui grand total
                updateGrandTotal();
            });
            var cleaveDiscount = new Cleave('#discount', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                prefix: 'Rp ',
                delimiter: '.'
            });
            var cleaveShipping = new Cleave('#shipping', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand',
                numeralDecimalMark: ',',
                prefix: 'Rp ',
                delimiter: '.'
            });

            // Function to get numeric value from a formatted input
            function getNumericValue(elementId) {
                var formattedValue = $('#' + elementId).val();
                var numericValue = formattedValue.replace(/[^\d,]/g, '').replace(',', '.');
                return parseFloat(numericValue) || 0;
            }
            $('#tax_rate, #discount, #shipping').on('input', function() {
                updateGrandTotal();
            });

            function updateGrandTotal() {
                var grandTotal = 0;

                // Iterate through each row in the product table
                $('#product-table-body tr').each(function() {
                    // Extract and parse the raw numeric value from the item-total text
                    var total = parseFloat($(this).find('.item-total').text().replace('Rp ', '').replace(
                        /\./g, '')) || 0;
                    if (!isNaN(total)) {
                        grandTotal += total;
                    }
                });
                // var discount = getNumericValue('discount');
                // console.log(discount);
                // var shipping = getNumericValue('shipping');
                // var taxRate = parseFloat($('#tax_rate').val()) || 0;
                var discount = getNumericValue('discount');
                var score = getNumericValue('score');
                var test = score * 100;
                console.log(test);
                console.log(discount);
                var shipping = getNumericValue('shipping');
                var taxRate = parseFloat($('#tax_rate').val()) || 0;
                // Update hidden fields with numeric values
                $('#discount_value').val(discount);
                $('#shipping_value').val(shipping);
                // Calculate tax amount
                var taxNet = (taxRate / 100) * grandTotal;
                $('#tax_net').val(taxNet.toFixed(2));

                // Calculate grand total
                // grandTotal = grandTotal - discount + shipping + taxNet;
                // Calculate grand total
                grandTotal = grandTotal - discount + shipping + taxNet;
                if (grandTotal >= test) {
                    grandTotal = grandTotal - test;
                }
                $('#basic-table tr:nth-child(1) th').text(formatRupiah(taxNet.toFixed(0))); // Order Tax
                $('#basic-table tr:nth-child(2) th').text(formatRupiah(discount.toFixed(0))); // Discount
                $('#basic-table tr:nth-child(3) th').text(formatRupiah(shipping.toFixed(0))); // Shipping
                $('#basic-table tr:nth-child(4) th').text(formatRupiah(test.toFixed(0)));
                $('#basic-table tr:nth-child(5) th').text(formatRupiah(grandTotal.toFixed(0))); // Grand Total
                $('#grandTotal').val(grandTotal.toFixed(2));
                $('#membership').val(test.toFixed(2));
                $('#paying_amount').val(formatRupiah(grandTotal.toFixed(0)));
            }
            // Event handler for score input change
            $('#score').on('input', function() {
                updateGrandTotal();
            });
            // Event handler for customer change
            $('#customer').on('change', function() {
                // Mengambil elemen option yang dipilih
                var selectedOption = this.options[this.selectedIndex];

                // Mengambil data-status dari option yang dipilih
                var status = selectedOption.getAttribute('data-status');

                // Mengecek nilai dari status dan melakukan aksi berdasarkan nilai tersebut
                if (status === '1') {
                    var score = selectedOption.getAttribute('data-score');
                    $('#score').val(score ? score : '');
                    updateGrandTotal(); // Tambahkan ini untuk memperbarui grand total setelah score diubah
                } else {
                    $('#score').val('0'); // Set score ke 0 jika statusnya 0
                    updateGrandTotal(); // Tambahkan ini untuk memperbarui grand total setelah score diubah
                }
            });
        });
    </script>
@endpush
