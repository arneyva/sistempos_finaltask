@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Edit Transfer') }}</h1>
    <p>{{ __('Manage your product transfers easily and efficiently') }}</p>
@endsection
<style>
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
                            <h4 class="card-title">{{ __('Edit Transfer') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('transfer.update', ['id' => $transfer['id']]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        for="selectWarehouse">{{ __('From Warehouse/Outlet *') }}</label>
                                    <input type="text" class="form-control" id="selectWarehouseName"
                                        value="{{ $warehouse->firstWhere('id', $transfer['from_warehouse'])->name }}"
                                        readonly>
                                    <input type="hidden" id="selectWarehouse" name="transfer[from_warehouse]"
                                        value="{{ $transfer['from_warehouse'] }}">
                                </div>
                                <div class="col-md-4 mb-3">
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
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td>#</td>
                                                        <td>{{ $detail['code'] }} {{ $detail['name'] }}</td>
                                                        <td>{{ $detail['Net_cost'] }}</td>
                                                        <td>{{ $detail['initial_stock'] }} {{ $detail['unitPurchase'] }}
                                                        </td>
                                                        <td>{{ $detail['stock'] }} {{ $detail['unitPurchase'] }}</td>
                                                        <td>
                                                            <input type="number" class="form-control item-quantity"
                                                                name="details[{{ $loop->index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0"
                                                                data-unit-cost="{{ $detail['Net_cost'] }}"
                                                                data-tax-percent="{{ $detail['tax_percent'] }}"
                                                                data-tax-method="{{ $detail['tax_method'] }}"
                                                                max="{{ $detail['initial_stock'] }}">
                                                        </td>
                                                        <td>Rp. {{ $detail['DiscountNet'] }}</td>
                                                        <td>Rp. {{ $detail['taxe'] }}</td>
                                                        <td class="item-total">Rp. {{ $detail['subtotal'] }}</td>
                                                        <input type="hidden" class="item-subtotal"
                                                            name="details[{{ $loop->index }}][subtotal]"
                                                            value="{{ $detail['subtotal'] }}">
                                                        <input type="hidden" name="details[{{ $loop->index }}][id]"
                                                            value="{{ $detail['id'] }}">
                                                        <input type="hidden" name="details[{{ $loop->index }}][no_unit]"
                                                            value="{{ $detail['no_unit'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][purchase_unit_id]"
                                                            value="{{ $detail['purchase_unit_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][product_variant_id]"
                                                            value="{{ $detail['product_variant_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][product_id]"
                                                            value="{{ $detail['product_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][Unit_cost]"
                                                            value="{{ $detail['Unit_cost'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][tax_percent]"
                                                            value="{{ $detail['tax_percent'] }}">
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
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
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
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="discount">{{ __('Discount') }}</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="discount"
                                                    placeholder="{{ __('input discount') }}" name="transfer[discount]"
                                                    value="{{ $transfer['discount'] }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
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
                                                <input type="number" class="form-control" id="shipping"
                                                    placeholder="{{ __('input shipping') }}" name="transfer[shipping]"
                                                    value="{{ $transfer['shipping'] }}">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
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
                                                <option value="{{ $transfer['statut'] }}">{{ $transfer['statut'] }}
                                                </option>
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
        document.getElementById('selectWarehouse').addEventListener('change', function() {
            var fromWarehouse = this.value;
            var toWarehouse = document.getElementById('selectToWarehouse').value;
            if (fromWarehouse === toWarehouse) {
                alert('From Warehouse and To Warehouse cannot be the same.');
                this.value = '';
            }
        });
        document.getElementById('selectToWarehouse').addEventListener('change', function() {
            var toWarehouse = this.value;
            var fromWarehouse = document.getElementById('selectWarehouse').value;
            if (fromWarehouse === toWarehouse) {
                alert('From Warehouse and To Warehouse cannot be the same.');
                this.value = '';
            }
        });
    </script>
    <script>
        $(document).ready(function() {
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
            $('#selectProduct').on('change', function() {
                var productId = $(this).val();
                var warehouseId = $('#selectWarehouse').val();
                var variantId = $(this).find(':selected').data('variant-id') || null;
                if (productId && warehouseId) {
                    $.ajax({
                        url: '/adjustment/show_product_data/' + productId + '/' + variantId + '/' +
                            warehouseId,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            var row = '<tr>';
                            row += '<td>#</td>';
                            row += '<td>' + data.code + ' ~ ' + data.name + '</td>';
                            row += '<td>' + 'Rp ' + data.Unit_cost + '</td>';
                            row += '<td>' + 'New Data' + '</td>';
                            row += '<td>' + data.qty_product_purchase + ' ' + data
                                .unitPurchase + '</td>';
                            row +=
                                '<td><input type="number" class="form-control item-quantity" name="details[new-' +
                                newIndex +
                                '][quantity]" value="0" min="0" data-max-quantity="' + data
                                .qty_product_purchase +
                                '" data-unit-cost="' + data.Unit_cost + '" data-tax-percent="' +
                                data.tax_percent +
                                '" data-tax-method="' + data.tax_method + '"></td>';
                            row += '<td class="item-discount">Rp 0</td>';
                            row += '<td>' + 'Rp ' + data.tax_cost + '</td>';
                            row += '<td class="item-total">Rp 0</td>';
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
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][id]" value="new">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][no_unit]" value="1">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][product_id]" value="' + data.id + '">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][product_variant_id]" value="' + (variantId || '') + '">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][purchase_unit_id]" value="' + data.purchase_unit_id + '">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][Unit_cost]" value="' + data.Unit_cost + '">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][tax_percent]" value="' + data.tax_percent + '">';
                            row += '<input type="hidden" name="details[new-' + newIndex +
                                '][tax_method]" value="' + data.tax_method + '">';
                            row +=
                                '<input type="hidden" class="item-subtotal" name="details[new-' +
                                newIndex + '][subtotal]" value="0">';
                            row += '</td>';
                            row += '</tr>';
                            $('#product-table-body').append(row);
                            newIndex++;
                            updateGrandTotal();
                        }
                    });
                }
            });
            function loadProductsByWarehouse(warehouseId) {
                if (warehouseId) {
                    $.ajax({
                        url: '/adjustment/get_Products_by_warehouse/' + warehouseId,
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

                var unitCost = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '')) || 0;
                var taxCost = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '')) || 0;
                var totalCost = (unitCost + taxCost) * quantity;

                row.find('.item-total').text('Rp ' + totalCost.toFixed(2));
                row.find('.item-subtotal').val(totalCost.toFixed(2));
                updateGrandTotal();
            });

            $('#tax_rate, #discount, #shipping').on('input', function() {
                updateGrandTotal();
            });

            function updateGrandTotal() {
                var grandTotal = 0;
                var taxNet = 0;
                $('#product-table-body tr').each(function() {
                    var row = $(this);
                    var quantity = parseFloat(row.find('.item-quantity').val()) || 0;
                    var unitCost = parseFloat(row.find('.item-quantity').data('unit-cost')) || 0;
                    var taxPercent = parseFloat(row.find('.item-quantity').data('tax-percent')) || 0;
                    var taxMethod = row.find('.item-quantity').data('tax-method');
                    var total = quantity * unitCost;
                    var tax = 0;

                    if (taxMethod === 'inclusive') {
                        tax = (total * taxPercent) / (100 + taxPercent);
                    } else {
                        tax = (total * taxPercent) / 100;
                    }

                    var subtotal = total + tax;
                    row.find('.item-total').text('Rp ' + subtotal.toFixed(2));
                    row.find('.item-subtotal').val(subtotal.toFixed(2));
                    grandTotal += subtotal;
                    taxNet += tax;
                });

                var discount = parseFloat($('#discount').val()) || 0;
                var shipping = parseFloat($('#shipping').val()) || 0;
                var taxRate = parseFloat($('#tax_rate').val()) || 0;
                var taxNetFromRate = (taxRate / 100) * grandTotal;

                grandTotal = grandTotal - discount + shipping + taxNetFromRate;

                $('#tax_net').val(taxNet.toFixed(2));
                $('#grandTotal').val(grandTotal.toFixed(2));

                // Update the displayed values in the table
                $('#basic-table tr:nth-child(1) th').text('Rp ' + taxNetFromRate.toFixed(2)); // Order Tax
                $('#basic-table tr:nth-child(2) th').text('Rp ' + discount.toFixed(2)); // Discount
                $('#basic-table tr:nth-child(3) th').text('Rp ' + shipping.toFixed(2)); // Shipping
                $('#basic-table tr:nth-child(4) th').text('Rp ' + grandTotal.toFixed(2)); // Grand Total
            }
        });
    </script>
@endpush
