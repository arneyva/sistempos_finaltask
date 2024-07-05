@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Add Sales') }}</h1>
    <p>{{ __('Create sales transaction data easily and efficiently') }}</p>
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
                            <h4 class="card-title">{{ __('Create Sale') }}</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('sale.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        for="selectWarehouse">{{ __('From Warehouse/Outlet *') }}</label>
                                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="customer">{{ __('Customer *') }}</label>
                                    <select class="form-select" id="customer" name="client_id" required>
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($client as $cl)
                                            <option value="{{ $cl->id }}"
                                                data-status="{{ $cl->is_poin_activated }}">
                                                {{ $cl->name }}</option>
                                        @endforeach
                                    </select>
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
                                            {{ __('Scan/Search Product by Code or Name') }}
                                        </option>
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
                                                    <th>{{ __('Product Name') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Stock') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Discount') }}</th>
                                                    <th>{{ __('Tax') }}</th>
                                                    <th>{{ __('Subtotal') }}</th>
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
                                                <td>{{ __('Grand Total') }}</td>
                                                <th></th>
                                            </tr>
                                    </table>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="tax_rate">{{ __('Order Tax') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="tax_rate"
                                                    placeholder="{{ __('input tax') }}" name="tax_rate"
                                                    value="{{ old('sale.tax_rate') }}">
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
                                            placeholder="input tax net" name="TaxNet" value="{{ old('sale.TaxNet') }}">
                                        <input class="" type="hidden" id="grandTotal" name="GrandTotal">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="discount">{{ __('Discount') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="discount"
                                                    placeholder="{{ __('input discount') }}" name="discount"
                                                    value="{{ old('sale.discount') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
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
                                                <input type="number" class="form-control" id="shipping"
                                                    placeholder="{{ __('input shipping') }}" name="shipping"
                                                    value="{{ old('sale.shipping') }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
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
                                        {{--  --}}
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="brand">{{ __('Status *') }}</label>
                                            <select class="form-select select2" id="typeStatus" required name="statut"
                                                data-placeholder="Select a Brand">
                                                <option value="completed" selected>{{ __('Completed') }}</option>
                                                <option value="pending">{{ __('Pending') }}</option>
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
                                                <option value="cash">{{ __('Cash') }}</option>
                                                <option value="midtrans">{{ __('Via Midtrans') }}</option>
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
                                                    placeholder="{{ __('input received amount') }}" id="received_amount"
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
                                                    placeholder="{{ __('input paying amount') }}" id="paying_amount"
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
                                                    placeholder="{{ __('input change return') }}" id="change_return"
                                                    name="change_return" value="{{ Session::get('change_return') }}"
                                                    readonly>
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
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label" for="validationDefault05">{{ __('Note') }}</label>
                                        <input type="text" class="form-control" id="validationDefault05"
                                            name="notes" placeholder="{{ __('a few words...') }}">
                                    </div>
                                </div>
                                <div class="form-group mt-2">
                                    <button class="btn btn-primary" type="submit">{{ __('Submit form') }}</button>
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
            // Initialize Select2 for Warehouse Dropdown
            $('#selectWarehouse').select2({
                placeholder: "Choose a warehouse...",
                allowClear: true
            });

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

            function calculateChange() {
                var received = parseFloat(document.getElementById('received_amount').value) || 0;
                var paying = parseFloat(document.getElementById('paying_amount').value) || 0;
                var change = received - paying;
                document.getElementById('change_return').value = change >= 0 ? change : 0;
            }

            statusDropdown.addEventListener('change', updateVisibility);
            paymentMethod.addEventListener('change', updateVisibility);
            document.getElementById('received_amount').addEventListener('input', calculateChange);
            document.getElementById('paying_amount').addEventListener('input', calculateChange);

            updateVisibility(); // Initial call to set the correct visibility on page load
        });
    </script>
    <script>
        $(document).ready(function() {
            // Update grand total on page load
            updateGrandTotal();
            // Delete row event handler
            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateGrandTotal();
                // Reset dropdown produk setelah menghapus produk dari tabel
                $('#selectProduct').val('').trigger('change');
            });
            $('#selectWarehouse').on('change', function() {
                // Kosongkan tabel produk ketika warehouse diubah
                $('#product-table-body').empty();
                updateGrandTotal(); // Perbarui total grand setelah mengosongkan tabel
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
            // Select warehouse event handler
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
                                    .id + '" data-code="' + value.code +
                                    '" data-variant-id="' + (value
                                        .product_variant_id || '') + '">' + value
                                    .name + '</option>');
                            });
                            $('#selectProduct').prop('disabled', false);
                        }
                    });
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
                            return false; // Hentikan loop
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
                                '/' +
                                warehouseId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                var initialQuantity = 1;
                                var initialTotal = initialQuantity * data.Unit_price +
                                    initialQuantity * data.tax_price;
                                var row = '<tr>';
                                row += '<td>#</td>';
                                row += '<td>' + data.code + ' ~ ' + data.name + '</td>';
                                row += '<td>' + 'Rp ' + data.Unit_price + '</td>';
                                row += '<td>' + data.qty + ' ' + data.unitSale + '</td>';
                                row +=
                                    '<td><input type="number" class="form-control item-quantity" name="details[' +
                                    data.id + '_' + variantId +
                                    '][quantity]" value="' + initialQuantity +
                                    '"  data-min-quantity="1" data-max-quantity="' + data
                                    .qty + '"></td>';
                                row += '<td class="item-discount">Rp 0</td>';
                                row += '<td>' + 'Rp ' + data.tax_price + '</td>';
                                row += '<td class="item-total">Rp ' + initialTotal + '</td>';
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
                                // Hidden inputs inside a hidden <td>
                                row += '<td class="hidden-input">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][product_id]" value="' + data.id + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][product_variant_id]" value="' + (variantId ||
                                        '') + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][sale_unit_id]" value="' + data.sale_unit_id +
                                    '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][Unit_price]" value="' + data.Unit_price +
                                    '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][tax_percent]" value="' + data.tax_percent +
                                    '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][tax_method]" value="' + data.tax_method +
                                    '">';
                                row +=
                                    '<input type="hidden" class="item-subtotal" name="details[' +
                                    data.id + '_' + variantId + '][subtotal]" value="' +
                                    initialTotal + '">';
                                row +=
                                    '<input type="hidden" class="item-subdiscount" name="details[' +
                                    data.id + '_' + variantId + '][discount]" value="0">';
                                row +=
                                    '<input type="hidden" class="item-subdiscountmethod" name="details[' +
                                    data.id + '_' + variantId +
                                    '][discount_method]" value="0">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][quantity_discount]" value="' + data
                                    .quantity_discount + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][discount_percentage]" value="' + data
                                    .discount_percentage + '">';
                                row += '</td>';
                                row += '</tr>';
                                $('#product-table-body').append(row);
                                updateGrandTotal();
                                // Reset dropdown produk setelah menambahkan produk ke tabel
                                $('#selectProduct').val('').trigger('change');
                            }
                        });
                    }
                }
            });

            // Item quantity change event handler
            $('#product-table-body').on('input', '.item-quantity', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat($(this).val()) || 0;
                var maxQuantity = parseFloat($(this).data('max-quantity')) || 0;
                var minQuantity = parseFloat($(this).data('min-quantity')) || 1;

                if (quantity > maxQuantity) {
                    // alert('The quantity cannot exceed the available stock.');
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
                var unitPrice = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '')) || 0;
                var taxPrice = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '')) || 0;
                var quantityDiscount = parseFloat(row.find('input[name$="[quantity_discount]"]')
                        .val()) ||
                    0;
                var discountPercentage = parseFloat(row.find('input[name$="[discount_percentage]"]')
                        .val()) ||
                    0;
                var discount = 0;
                if (quantity >= quantityDiscount) {
                    discount = (unitPrice * quantity) * (discountPercentage / 100);
                    row.find('.item-discount').text('Rp ' + discount.toFixed(2));
                    row.find('.item-subdiscount').val(discount.toFixed(2));
                    row.find('.item-subdiscountmethod').val('discount');
                } else {
                    row.find('.item-discount').text('Rp 0');
                    row.find('.item-subdiscount').val(discount.toFixed(2));
                    row.find('.item-subdiscountmethod').val('nodiscount');
                }

                var totalPrice = (unitPrice + taxPrice) * quantity - discount;

                row.find('.item-total').text('Rp ' + totalPrice.toFixed(2));
                row.find('.item-subtotal').val(totalPrice.toFixed(2));
                updateGrandTotal();
            });

            // Tax rate, discount, shipping change event handler
            $('#tax_rate, #discount, #shipping').on('input', function() {
                updateGrandTotal();
            });
            function updateGrandTotal() {
                var grandTotal = 0;

                // Iterate through each row in the product table
                $('#product-table-body tr').each(function() {
                    var total = parseFloat($(this).find('.item-total').text().replace('Rp ', '')) ||
                        0;
                    if (!isNaN(total)) {
                        grandTotal += total;
                    }
                });

                // Get values of tax rate, discount, shipping
                var discount = parseFloat($('#discount').val()) || 0;
                var shipping = parseFloat($('#shipping').val()) || 0;
                var taxRate = parseFloat($('#tax_rate').val()) || 0;

                // Calculate tax amount
                var taxNet = (taxRate / 100) * grandTotal;
                $('#tax_net').val(taxNet.toFixed(2));

                // Calculate grand total
                grandTotal = grandTotal - discount + shipping + taxNet;

                // Update displayed values in the summary table
                $('#basic-table tr:nth-child(1) th').text('Rp ' + taxNet.toFixed(2)); // Order Tax
                $('#basic-table tr:nth-child(2) th').text('Rp ' + discount.toFixed(2)); // Discount
                $('#basic-table tr:nth-child(3) th').text('Rp ' + shipping.toFixed(2)); // Shipping
                $('#basic-table tr:nth-child(4) th').text('Rp ' + grandTotal.toFixed(2)); // Grand Total

                // Update hidden fields for backend submission
                $('#grandTotal').val(grandTotal.toFixed(2));
                $('#paying_amount').val(grandTotal.toFixed(2));
            }
        });
    </script>
    <script>
        document.getElementById('customer').addEventListener('change', function() {
            // Mengambil elemen option yang dipilih
            var selectedOption = this.options[this.selectedIndex];

            // Mengambil data-status dari option yang dipilih
            var status = selectedOption.getAttribute('data-status');

            // Mengecek nilai dari status dan melakukan aksi berdasarkan nilai tersebut
            if (status === '1') {


            } else if (status === '0') {

            }
        })
    </script>
@endpush
