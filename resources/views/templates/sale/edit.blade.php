@extends('templates.main')

@section('pages_title')
    <h1>Edit sale</h1>
    <p>Look All your sale</p>
@endsection

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
                            <h4 class="card-title">Edit sale</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('sale.update', ['id' => $sale['id']]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="selectWarehouse">From Warehouse/Outlet *</label>
                                    <input type="text" class="form-control" name="warehouse_id" id="selectWarehouse"
                                        value="{{ $sale['warehouse_id'] }}" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="customer">Customer *</label>
                                    <select class="form-select" id="customer" name="client_id" required>
                                        <option selected disabled value="">Choose...</option>
                                        @foreach ($client as $cl)
                                            <option value="{{ $cl->id }}" data-status="{{ $cl->is_poin_activated }}">
                                                {{ $cl->name }}</option>
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
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Product</th>
                                                    <th>Net Unit price</th>
                                                    <th>Stock</th>
                                                    <th>Quantity</th>
                                                    <th>Discount</th>
                                                    <th>Tax</th>
                                                    <th>SubTotal</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td>#</td>
                                                        <td>{{ $detail['code'] }} ~ {{ $detail['name'] }}</td>
                                                        <td>Rp {{ $detail['Net_price'] }}</td>
                                                        <td>{{ $detail['stock'] }} {{ $detail['unitSale'] }}</td>
                                                        <td>
                                                            <input type="number" class="form-control item-quantity"
                                                                name="details[{{ $loop->index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0"
                                                                data-unit-price="{{ $detail['Net_price'] }}"
                                                                data-tax-percent="{{ $detail['tax_percent'] }}"
                                                                data-tax-method="{{ $detail['tax_method'] }}">
                                                        </td>
                                                        <td>Rp {{ $detail['DiscountNet'] }}</td>
                                                        <td>Rp {{ $detail['taxe'] }}</td>
                                                        <td class="item-total">Rp {{ $detail['subtotal'] }}</td>
                                                        <td>
                                                            <input type="hidden" class="item-subtotal"
                                                                name="details[{{ $loop->index }}][subtotal]"
                                                                value="{{ $detail['subtotal'] }}">
                                                        </td>
                                                        <input type="hidden" name="details[{{ $loop->index }}][id]"
                                                            value="{{ $detail['id'] }}">
                                                        <input type="hidden" name="details[{{ $loop->index }}][no_unit]"
                                                            value="{{ $detail['no_unit'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][sale_unit_id]"
                                                            value="{{ $detail['sale_unit_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][product_variant_id]"
                                                            value="{{ $detail['product_variant_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][product_id]"
                                                            value="{{ $detail['product_id'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][Unit_price]"
                                                            value="{{ $detail['Unit_price'] }}">
                                                        <input type="hidden"
                                                            name="details[{{ $loop->index }}][tax_percent]"
                                                            value="{{ $detail['tax_percent'] }}">
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-row">Delete</button>
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
                                                <td>Order Tax</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>Discount</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>Shipping</td>
                                                <th></th>
                                            </tr>
                                            <tr>
                                                <td>Grand Total</td>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="tax_rate">Order Tax *</label>
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
                                            placeholder="input tax net" name="TaxNet"
                                            value="{{ $sale['TaxNet'] }}">
                                        <input class="" type="hidden" id="grandTotal" name="GrandTotal"
                                            value="{{ $sale['GrandTotal'] }}">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="discount">Discount *</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="discount"
                                                    placeholder="input discount" name="discount"
                                                    value="{{ $sale['discount'] }}">
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
                                            <label class="form-label" for="shipping">Shipping *</label>
                                            <div class="form-group input-group">
                                                <input type="number" class="form-control" id="shipping"
                                                    placeholder="input shipping" name="shipping"
                                                    value="{{ $sale['shipping'] }}">
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
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="typeStatus">Status</label>
                                            <select class="form-select select2" id="typeStatus" required name="statut"
                                                data-placeholder="Select a Status">
                                                <option value="completed"
                                                    {{ $sale['statut'] == 'completed' ? 'selected' : '' }}>Completed
                                                </option>
                                                <option value="pending"
                                                    {{ $sale['statut'] == 'pending' ? 'selected' : '' }}>Pending</option>
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
                                            <label class="form-label" for="payment_method">Payment Method</label>
                                            <select class="form-select select2" name="payment_method" id="payment_method"
                                                data-placeholder="Select a payment_method">
                                                <option value="cash">Cash</option>
                                                <option value="midtrans">Other</option>
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
                                            <label class="form-label" for="received_amount">Received Amount *</label>
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
                                            <label class="form-label" for="paying_amount">Paying Amount *</label>
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
                                            <label class="form-label" for="change_return">Change Return *</label>
                                            <div class="form-group input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp. </span>
                                                <input type="text" class="form-control"
                                                    placeholder="input change return" id="change_return"
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
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="exampleFormControlTextarea1">Note</label>
                                    <input type="text" class="form-control" id="exampleFormControlTextarea1"
                                        rows="3" name="notes" value="{{ $sale['notes'] }}">
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Add sale</button>
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
                    $('input[name="warehouse_id"]').val(warehouseId);
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
                            row += '<td>' + 'Rp ' + data.Unit_price + '</td>';
                            row += '<td>' + data.qty + ' ' + data.unitSale + '</td>';
                            row +=
                                '<td><input type="number" class="form-control item-quantity" name="details[new-' +
                                newIndex +
                                '][quantity]" value="0" min="0" data-unit-price="' + data
                                .Unit_price + '" data-tax-percent="' + data.tax_percent +
                                '" data-tax-method="' + data.tax_method + '"></td>'; //quantity
                            row += '<td class="item-discount">Rp 0</td>';
                            row += '<td>' + 'Rp ' + data.tax_price + '</td>';
                            row += '<td class="item-total">Rp 0</td>';
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][id]" value="new"></td>'; //ini id coy
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][no_unit]" value="1"></td>'; //ini id coy
                            row +=
                                '<td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>';
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][product_id]" value="' + data.id +
                                '"></td>'; //produk id
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][product_variant_id]" value="' + (variantId ||
                                    '') + '"></td>'; //variant id
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][sale_unit_id]" value="' + data
                                .sale_unit_id + '"></td>'; //sale unit id
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][Unit_price]" value="' + data.Unit_price +
                                '"></td>'; //unit price
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][tax_percent]" value="' + data.tax_percent +
                                '"></td>';
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][tax_method]" value="' + data.tax_method +
                                '"></td>';
                            row +=
                                '<td><input type="hidden" class="item-subtotal" name="details[new-' +
                                newIndex + '][subtotal]" value="0"></td>';
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
                var unitprice = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '')) || 0;
                var taxprice = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '')) || 0;
                var totalprice = (unitprice + taxprice) * quantity;

                row.find('.item-total').text('Rp ' + totalprice.toFixed(2));
                row.find('.item-subtotal').val(totalprice.toFixed(2));
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
                    var unitprice = parseFloat(row.find('.item-quantity').data('unit-price')) || 0;
                    var taxPercent = parseFloat(row.find('.item-quantity').data('tax-percent')) || 0;
                    var taxMethod = row.find('.item-quantity').data('tax-method');
                    var total = quantity * unitprice;
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
                $('#paying_amount').val(grandTotal.toFixed(2));
                // Update the displayed values in the table
                $('#basic-table tr:nth-child(1) th').text('Rp ' + taxNetFromRate.toFixed(2)); // Order Tax
                $('#basic-table tr:nth-child(2) th').text('Rp ' + discount.toFixed(2)); // Discount
                $('#basic-table tr:nth-child(3) th').text('Rp ' + shipping.toFixed(2)); // Shipping
                $('#basic-table tr:nth-child(4) th').text('Rp ' + grandTotal.toFixed(2)); // Grand Total
            }
        });
    </script>
@endpush
