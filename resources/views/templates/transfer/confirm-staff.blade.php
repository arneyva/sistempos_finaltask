@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Confirm') }}{{ __(' Transfer') }}</h1>
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

    .chat-online {
        color: #34ce57
    }

    .chat-offline {
        color: #e4606d
    }

    .chat-messages {
        display: flex;
        flex-direction: column;
        max-height: 800px;
        overflow-y: scroll
    }

    .chat-message-left,
    .chat-message-right {
        display: flex;
        flex-shrink: 0
    }

    .chat-message-left {
        margin-right: auto
    }

    .chat-message-right {
        flex-direction: row-reverse;
        margin-left: auto
    }

    .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    .px-4 {
        padding-right: 1.5rem !important;
        padding-left: 1.5rem !important;
    }

    .flex-grow-0 {
        flex-grow: 0 !important;
    }

    .border-top {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@section('content')
    <div class="col-md-12 col-lg-12">
    </div>
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">History Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container p-0">
                        <button type="button" class="btn btn-primary">{{ $transfer['Ref'] }}</button>
                        <div class="card">
                            <div class="col-12 col-lg-7 col-xl-12">
                                <div class="position-relative">
                                    <div class="chat-messages p-4">
                                        @foreach ($formattedNotes as $formattedNotes)
                                            <div class="chat-message-right pb-4">
                                                <div style="margin-left: 20px">
                                                    <img src="{{ asset('hopeui/html/assets/images/avatars/' . $formattedNotes['avatar']) }}"
                                                        alt="User-Profile" class="rounded-circle mr-1" alt="Chris Wood"
                                                        width="50" height="50">
                                                    <div class="text-muted small text-nowrap mt-2">
                                                        {{ $formattedNotes['created_at'] }}</div>
                                                </div>
                                                <div class="flex-shrink-1 bg-light rounded py-2 px-3 mr-5">
                                                    <div class="font-weight-bold mb-1">{{ $formattedNotes['user'] }}</div>
                                                    {{ $formattedNotes['content'] }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ImportProduct" tabindex="-1" aria-labelledby="ImportProduct" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">{{ __('Instructions') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="col-sm-12 col-form-label"
                        for="name">{{ __('* Pastikan anda telah mengecek barang,sebelum melakukan konfirmasi data Transfer') }}</label>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                disabled>{{ __('Re-Check Admin Required') }}</button>
                        </div>
                        <label class="col-sm-8 col-form-label"
                            for="codeProduct">{{ __('Jika Quantity Tidak Sesuai, anda dapat memilih status ini') }}</label>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-outline-success btn-sm"
                                disabled>{{ __('Completed') }}</button>
                        </div>
                        <label class="col-sm-8 col-form-label"
                            for="codeProduct">{{ __('Jika Quantity Sesuai, anda dapat memilih status ini') }}</label>
                    </div>
                    <label class="col-sm-12 col-form-label"
                        for="name">{{ __('* Selalu gunakan fitur notes dalam setiap aksi') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">{{ __('Confirm') }}{{ __(' Transfer') }}</h4>
                        </div>
                        <div class="header-title">
                            <button type="button" class="btn rounded-pill btn-soft-danger" data-bs-toggle="modal"
                                data-bs-target="#ImportProduct">{{ __('Instructions') }}</button>
                            <button type="button" class="btn rounded-pill btn-soft-primary" data-bs-toggle="modal"
                                data-bs-target="#createModal">{{ __('Notes') }}</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transfer.update-for-staff', ['id' => $transfer['id']]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-4 mb-3" style="display: none">
                                    <input type="hidden" id="selectWarehouse" name="transfer[from_warehouse]"
                                        value="{{ $transfer['from_warehouse'] }}">
                                </div>
                                <div class="col-md-4 mb-3" style="display: none">
                                    <label class="form-label"
                                        for="selectToWarehouse">{{ __('To Warehouse/Outlet *') }}</label>
                                    <select class="form-select" id="selectToWarehouse" name="transfer[to_warehouse]"
                                        required>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}" @selected(old('transfer[to_warehouse]', $transfer['to_warehouse'] ?? '') == $wh->id)>
                                                {{ $wh->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3" style="display: none">
                                    <label class="form-label" for="exampleInputdate">{{ __('Date *') }}</label>
                                    <input type="date" class="form-control" id="exampleInputdate"
                                        name="transfer[date]" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Product') }}</th>
                                                    <th>{{ __('Purchases') }} {{ __('Cost') }}</th>
                                                    <th></th>
                                                    <th>{{ __('Shipped Quantity') }}</th>
                                                    <th>Received {{ __('Quantity') }}</th>
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
                                                        <td>{{ $detail['code'] }} {{ $detail['name'] }}</td>
                                                        <td>{{ 'Rp ' . number_format($detail['Unit_cost'], 0, ',', '.') }}
                                                        <td></td>
                                                        <td>{{ $detail['quantity'] }} {{ $detail['unitPurchase'] }}</td>
                                                        <td>
                                                            <input type="number" class="form-control item-quantity"
                                                                name="details[{{ $index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0"
                                                                data-unit-cost="{{ $detail['Unit_cost'] }}"
                                                                data-tax-percent="{{ $detail['tax_percent'] }}"
                                                                data-tax-method="{{ $detail['tax_method'] }}"
                                                                data-max-quantity="{{ $detail['quantity'] }}">
                                                        </td>
                                                        <td class="item-discount">
                                                            {{ 'Rp ' . number_format($detail['DiscountNet'], 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ 'Rp ' . number_format($detail['taxe'], 0, ',', '.') }}</td>
                                                        {{-- <td class="item-total">Rp. {{ $detail['subtotal'] }}</td> --}}
                                                        <td class="item-total">
                                                            {{ 'Rp ' . number_format($detail['total'], 0, ',', '.') }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-row"><svg
                                                                    xmlns="http://www.w3.org/2000/svg" width="1.5em"
                                                                    height="1.5em" viewBox="0 0 48 48">
                                                                    <g fill="none" stroke="#FFFFFF"
                                                                        stroke-linejoin="round" stroke-width="4">
                                                                        <path d="M9 10v34h30V10z" />
                                                                        <path stroke-linecap="round"
                                                                            d="M20 20v13m8-13v13" />
                                                                        <path d="M4 10h40" />
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
                                                                name="details[{{ $index }}][purchase_unit_id]"
                                                                value="{{ $detail['purchase_unit_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][product_variant_id]"
                                                                value="{{ $detail['product_variant_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][product_id]"
                                                                value="{{ $detail['product_id'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][Unit_cost]"
                                                                value="{{ $detail['Unit_cost'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][tax_percent]"
                                                                value="{{ $detail['tax_percent'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][tax_method]"
                                                                value="{{ $detail['tax_method'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][quantity_discount]"
                                                                value="{{ $detail['quantity_discount_init'] }}">
                                                            <input type="hidden"
                                                                name="details[{{ $index }}][discount_percentage]"
                                                                value="{{ $detail['discount_percentage'] }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
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
                                <div class="col-md-6 mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="shipping">{{ __('Shipping') }}</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="shipping"
                                                    placeholder="{{ __('input shipping') }}" name="transfer[shipping]"
                                                    value="{{ $transfer['shipping'] }}" readonly>
                                                <input type="hidden" id="shipping_value"
                                                    name="transfer[shipping_value]">
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
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="status">{{ __('Status *') }}</label>
                                            <select class="form-select select2" id="status" name="transfer[statut]"
                                                required data-placeholder="Select a Status">
                                                <option value="completed">{{ __('Completed') }}</option>
                                                <option value="Re-Check Admin Required">
                                                    {{ __('Re-Check Admin Required') }}</option>
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
                                    <div class="row">
                                        <div class="col-md-4 mb-3" style="display: none">
                                            <label class="form-label" for="tax_rate">{{ __('Order Tax') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="tax_rate"
                                                    placeholder="{{ __('input tax') }}" name="transfer[tax_rate]"
                                                    value="{{ $transfer['tax_rate'] }}">
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
                                            value="{{ $transfer['TaxNet'] }}">
                                        <input class="" type="hidden" id="grandTotal" name="GrandTotal"
                                            value="{{ $transfer['GrandTotal'] }}">
                                        <div class="col-md-4 mb-3" style="display: none">
                                            <label class="form-label" for="discount">{{ __('Discount') }}</label>
                                            <div class="form-group input-group">
                                                <input type="text" class="form-control" id="discount"
                                                    placeholder="{{ __('input discount') }}" name="transfer[discount]"
                                                    value="{{ $transfer['discount'] }}">
                                                <input type="hidden" id="discount_value"
                                                    name="transfer[discount_value]">
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

                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label"
                                        for="exampleFormControlTextarea1">{{ __('Note') }}</label>
                                    <input type="text" class="form-control" id="exampleFormControlTextarea1"
                                        rows="3" name="transfer[notes]" value="{{ $transfer['notes'] }}">
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
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('status');
            const inputs = document.querySelectorAll('#product-table input');
            const buttons = document.querySelectorAll('#product-table button');

            statusSelect.addEventListener('change', function() {
                if (statusSelect.value === 'Re-Check Admin Required') {
                    inputs.forEach(input => input.setAttribute('readonly', 'true'));
                    buttons.forEach(button => button.setAttribute('disabled', 'true'));
                } else {
                    inputs.forEach(input => input.removeAttribute('readonly'));
                    buttons.forEach(button => button.removeAttribute('disabled'));
                }
            });

            // Initial check to disable inputs if needed when the page loads
            // if (statusSelect.value === 'Re-Check Admin Required') {
            //     inputs.forEach(input => input.setAttribute('disabled', 'true'));
            //     buttons.forEach(button => button.setAttribute('disabled', 'true'));
            // }
        });
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for Warehouse Dropdown
            $('#selectToWarehouse').select2({
                placeholder: "Choose a warehouse...",
                allowClear: true
            });

            // Custom event listener for Select2 change event
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
            let newIndex = 0;
            // Initial update on page load
            updateGrandTotal();

            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateGrandTotal();
            });
            $('#selectWarehouse').on('change', function() {
                var warehouseId = $(this).val();
                if (warehouseId) {
                    // Mengirimkan nilai warehouse_id ke server
                    $('input[name="transfer[from_warehouse]"]').val(warehouseId);
                    loadProductsByWarehouse(warehouseId);
                } else {
                    $('#selectProduct').empty().prop('disabled', true);
                }
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
                                var quantityUnit = initialQuantity * data.qty;
                                var discountawal = data.Unit_cost * (data.discount_percentage /
                                    100) * initialQuantity;
                                console.log(discountawal);
                                var formattedDiscountCost = formatRupiah(discountawal);
                                console.log("Discount Cost:", formattedDiscountCost);
                                var initialTotal = initialQuantity * data.Unit_cost +
                                    initialQuantity * data.tax_cost - discountawal;
                                var formattedUnitCost = formatRupiah(data.Unit_cost);
                                var formattedTaxCost = formatRupiah(data.tax_cost);
                                var formattedInitialTotal = formatRupiah(initialTotal);
                                var row = '<tr>';
                                row += '<td>#</td>';
                                row += '<td>' + data.code + ' ~ ' + data.name + '</td>';
                                row += '<td>' + formattedUnitCost + '</td>';
                                row += '<td>' + 'New Data' + '</td>';
                                row += '<td>' + data.qty_product_purchase + ' ' + data
                                    .unitPurchase + '</td>';
                                row +=
                                    '<td><input type="number" class="form-control item-quantity" name="details[new-' +
                                    newIndex + '][quantity]" value="' + initialQuantity +
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
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][id]" value="new">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][no_unit]" value="1">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][product_id]" value="' + data.id + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][product_variant_id]" value="' + (variantId || '') + '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][purchase_unit_id]" value="' + data.purchase_unit_id +
                                    '">';
                                row += '<input type="hidden" name="details[new-' + newIndex +
                                    '][Unit_cost]" value="' + data.Unit_cost + '">';
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
                                    '][quantity_discount]" value="' + data.
                                quantity_discount_purchase +
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
                var unitCost = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '').replace(/\./g,
                    '')) || 0;
                var taxCost = parseFloat(row.find('td:eq(7)').text().replace('Rp ', '').replace(/\./g,
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
