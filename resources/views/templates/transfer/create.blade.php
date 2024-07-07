@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Create Transfer') }}</h1>
    <p>{{ __('Manage your product transfers easily and efficiently') }}</p>
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
    <div class="col-md-12 col-lg-12">
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">{{ __('Create Transfer') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transfer.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        for="selectWarehouse">{{ __('From Warehouse/Outlet *') }}</label>
                                    <select class="form-select" id="selectWarehouse" name="transfer[from_warehouse]"
                                        required>
                                        <option selected disabled value="">{{ __('Choose...') }}.</option>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        for="selectToWarehouse">{{ __('To Warehouse/Outlet *') }}</label>
                                    <select class="form-select" id="selectToWarehouse" name="transfer[to_warehouse]"
                                        required>
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Date *') }}</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="transfer[date]"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectProduct">{{ __('Product *') }}</label>
                                    <select class="form-select" id="selectProduct" disabled>
                                        <option selected disabled value="">Choose warehouse first...</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Product') }}</th>
                                                    <th>{{ __('Purchase') }} {{ __('Cost') }}</th>
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
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="tax_rate">{{ __('Order Tax') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="tax_rate"
                                                    placeholder="{{ __('input tax') }}" name="transfer[tax_rate]"
                                                    value="{{ old('transfer.tax_rate') }}">
                                                <span class="input-group-text" id="basic-addon1">%</span>
                                            </div>
                                            @error('transfer.tax_rate')
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
                                            placeholder="input tax net" name="transfer[TaxNet]"
                                            value="{{ old('transfer.TaxNet') }}">
                                        <input class="" type="hidden" id="grandTotal" name="GrandTotal">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="discount">{{ __('Discount') }}</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="discount"
                                                    placeholder="{{ __('input discount') }}" name="transfer[discount]"
                                                    value="{{ old('transfer.discount') }}">
                                                <input type="hidden" id="discount_value" name="discount_value">
                                            </div>
                                            @error('transfer.discount')
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
                                                    placeholder="{{ __('input shipping') }}" name="transfer[shipping]"
                                                    value="{{ old('transfer.shipping') }}">
                                                <input type="hidden" id="shipping_value" name="shipping_value">
                                            </div>
                                            @error('transfer.shipping')
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
                                            <label class="form-label" for="status">{{ __('Status *') }}</label>
                                            <select class="form-select select2" id="status" name="transfer[statut]"
                                                required data-placeholder="Select a Status">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="sent">{{ __('Sent') }}</option>
                                                <option value="completed">{{ __('Completed') }}</option>
                                            </select>
                                            @error('transfer.statut')
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
                                        rows="3" name="transfer[notes]" value="{{ old('transfer.notes') }}">
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">{{ __('Add Transfer') }}</button>
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
            // Initialize Select2 for Warehouse Dropdown
            $('#selectWarehouse').select2({
                placeholder: "Choose a warehouse...",
                // allowClear: true
            });

            $('#selectToWarehouse').select2({
                placeholder: "Choose a warehouse...",
                // allowClear: true
            });

            // Custom event listener for Select2 change event
            $('#selectWarehouse').on('change', function() {
                var fromWarehouse = $(this).val();
                var toWarehouse = $('#selectToWarehouse').val();
                if (fromWarehouse === toWarehouse) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'From Warehouse and To Warehouse cannot be the same.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    $(this).val(null).trigger('change');
                }
            });

            $('#selectToWarehouse').on('change', function() {
                var toWarehouse = $(this).val();
                var fromWarehouse = $('#selectWarehouse').val();
                if (fromWarehouse === toWarehouse) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'From Warehouse and To Warehouse cannot be the same.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    $(this).val(null).trigger('change');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function formatRupiah(number) {
                return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            // Initial update on page load
            updateGrandTotal();

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
            $('#selectWarehouse').on('change', function() {
                var warehouseId = $(this).val();
                if (warehouseId) {
                    $.ajax({
                        url: '/adjustment/get_Available_Products_by_warehouse/' + warehouseId,
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
                                '/' + warehouseId,
                            type: "GET",
                            dataType: "json",
                            success: function(data) {
                                var initialQuantity = 1;
                                var quantityUnit = initialQuantity * data.qty;
                                var discountawal = data.Unit_cost * (data.discount_percentage /
                                    100) * initialQuantity;
                                console.log(discountawal);
                                var formattedDiscountCost = formatRupiah(discountawal);
                                console.log("Discount Cost:", formattedDiscountCost, "test",
                                    discountawal);
                                var initialTotal = initialQuantity * data.Unit_cost +
                                    initialQuantity * data.tax_cost - discountawal;
                                var formattedUnitCost = formatRupiah(data.Unit_cost);
                                var formattedTaxCost = formatRupiah(data.tax_cost);
                                var formattedInitialTotal = formatRupiah(initialTotal);
                                var subdiscountawal = data.qty_product_purchase > data.quantity_discount_purchase ? 'discount' : 'nodiscount';
                                // var
                                var row = '<tr>';
                                row += '<td>#</td>';
                                row += '<td>' + data.code + ' ~ ' + data.name + '</td>';
                                row += '<td>' + formattedUnitCost + '</td>';
                                row += '<td>' + data.qty_product_purchase + ' ' + data
                                    .unitPurchase + '</td>';
                                row +=
                                    '<td><input type="number" class="form-control item-quantity" name="details[' +
                                    data.id + '_' + variantId + '][quantity]" value="' +
                                    initialQuantity +
                                    '" data-min-quantity="1" data-max-quantity="' + data
                                    .qty_product_purchase +
                                    '"></td>';
                                row += '<td class="item-discount">' + formattedDiscountCost +
                                    '</td>';
                                row += '<td>' + formattedTaxCost + '</td>';
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
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][product_id]" value="' + data.id + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][product_variant_id]" value="' + (variantId ||
                                        '') + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][purchase_unit_id]" value="' + data
                                    .purchase_unit_id +
                                    '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][Unit_cost]" value="' + data.Unit_cost +
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
                                    data.id + '_' + variantId + '][discount]" value="' + discountawal + '">';
                                row +=
                                    '<input type="hidden" class="item-subdiscountmethod" name="details[' +
                                    data.id + '_' + variantId +
                                    '][discount_method]" value="' + subdiscountawal + '">';
                                row += '<input type="hidden" name="details[' + data.id + '_' +
                                    variantId + '][quantity_discount]" value="' + data
                                    .quantity_discount_purchase + '">';
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

            $('#product-table-body').on('input', '.item-quantity', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat($(this).val()) || 0;
                // var quantityUnit = quantity * data.qty;
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
                var unitCost = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '').replace(/\./g,
                    '')) || 0;
                var taxCost = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '').replace(/\./g,
                    '')) || 0;
                var quantityDiscount = parseFloat(row.find('input[name$="[quantity_discount]"]').val()) ||
                    0;
                var discountPercentage = parseFloat(row.find('input[name$="[discount_percentage]"]')
                    .val()) || 0;
                var discount = 0;
                // Menghitung diskon jika kuantitas memenuhi syarat
                if (quantity >= quantityDiscount) {
                    discount = (unitCost * quantity) * (discountPercentage / 100);
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

                var totalCost = (unitCost + taxCost) * quantity - discount;
                row.find('.item-total').text(formatRupiah(totalCost.toFixed(
                    0))); // Menggunakan toFixed(0) jika tidak menggunakan koma
                console.log("Total Cost:", totalCost.toFixed(
                    0));
                row.find('.item-subtotal').val(totalCost.toFixed(
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
                var discount = getNumericValue('discount');
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
                grandTotal = grandTotal - discount + shipping + taxNet;
                $('#basic-table tr:nth-child(1) th').text(formatRupiah(taxNet.toFixed(0))); // Order Tax
                $('#basic-table tr:nth-child(2) th').text(formatRupiah(discount.toFixed(0))); // Discount
                $('#basic-table tr:nth-child(3) th').text(formatRupiah(shipping.toFixed(0))); // Shipping
                $('#basic-table tr:nth-child(4) th').text(formatRupiah(grandTotal.toFixed(0))); // Grand Total
                $('#grandTotal').val(grandTotal.toFixed(2));

            }
        });
    </script>
@endpush
