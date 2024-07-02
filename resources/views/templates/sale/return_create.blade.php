@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Create Sales Return') }}</h1>
    <p>{{ __('Create sales return transaction data easily and efficiently') }}</p>
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
                            <h4 class="card-title">{{ __('Create Sales Return') }}</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('sale.return.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="client_id" value="{{ $sale_return['client_id'] }}">
                            <input type="hidden" name="warehouse_id" value="{{ $sale_return['warehouse_id'] }}">
                            <input type="hidden" name="sale_id" value="{{ $sale_return['sale_id'] }}">
                            <div class="row">

                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Date *') }}</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Sale Reference') }}</label>
                                    <input type="text" class="form-control" id="exampleInputdate"
                                        value="{{ $sale_return['sale_ref'] }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Status *') }}</label>
                                    <input type="text" class="form-control" id="exampleInputdate" name="statut"
                                        value="{{ $sale_return['statut'] }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Product Name') }}</th>
                                                    <th>{{ __('Price') }}</th>
                                                    <th>{{ __('Quantity Sold') }}</th>
                                                    <th>{{ __('Quantity Returned') }}</th>
                                                    <th>{{ __('Discount') }}</th>
                                                    <th>{{ __('Tax') }}</th>
                                                    <th>{{ __('SubTotal') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                <!-- Isi dari tbody akan diisi secara dinamis menggunakan JavaScript -->
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td>#</td>
                                                        <td>{{ $detail['code'] }} ~ {{ $detail['name'] }}</td>
                                                        <td>{{ $detail['Unit_price'] }}</td>
                                                        <td>{{ $detail['sale_quantity'] }}</td>
                                                        <td> <input type="number" class="form-control item-quantity"
                                                                name="details[{{ $loop->index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0"
                                                                data-unit-price="{{ $detail['Net_price'] }}"
                                                                data-tax-percent="{{ $detail['tax_percent'] }}"
                                                                data-tax-method="{{ $detail['tax_method'] }}"
                                                                max="{{ $detail['sale_quantity'] }}"></td>
                                                        <td class="item-discount">Rp. {{ $detail['DiscountNet'] }}</td>
                                                        <td>{{ $detail['taxe'] }}</td>
                                                        <td class="item-total">Rp {{ $detail['subtotal'] }}</td>
                                                        <input type="hidden" class="item-subtotal"
                                                            name="details[{{ $loop->index }}][subtotal]"
                                                            value="{{ $detail['subtotal'] }}">
                                                        <td>
                                                            <input type="hidden" class="item-subdiscount"
                                                                name="details[{{ $loop->index }}][discount]"
                                                                value="{{ $detail['discount'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="hidden" class="item-subdiscountmethod"
                                                                name="details[{{ $loop->index }}][discount_method]"
                                                                value="{{ $detail['discount_method'] }}">
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
                                                    </tr>
                                                @endforeach
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
                                                    value="{{ $sale_return['tax_rate'] }}">
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
                                                    value="{{ $sale_return['discount'] }}">
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
                                                    value="{{ $sale_return['shipping'] }}">
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
            // Initial update on page load
            updateGrandTotal();

            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
                updateGrandTotal();
            });

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
                                    .id + '" data-variant-id="' + value
                                    .product_variant_id + '">' + value.name +
                                    '</option>');
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
                                '<td><input type="number" class="form-control item-quantity" name="details[' +
                                data.id + '_' + variantId +
                                '][quantity]" value="0" min="0"></td>'; // quantity
                            row += '<td class="item-discount">Rp 0</td>';
                            row += '<td>' + 'Rp ' + data.tax_price + '</td>';
                            row += '<td class="item-total">Rp 0</td>';
                            row +=
                                '<td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>';
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][product_id]" value="' + data.id +
                                '"></td>'; // product_id
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][product_variant_id]" value="' + (variantId ||
                                    '') + '"></td>'; // product_variant_id
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][sale_unit_id]" value="' + data
                                .sale_unit_id + '"></td>'; // sale_unit_id
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][Unit_price]" value="' + data.Unit_price +
                                '"></td>'; // Unit_price
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][tax_percent]" value="' + data.tax_percent +
                                '"></td>'; // tax_percent
                            row += '<td><input type="hidden" name="details[' + data.id + '_' +
                                variantId + '][tax_method]" value="' + data.tax_method +
                                '"></td>'; // tax_method
                            row +=
                                '<td><input type="hidden" class="item-subtotal" name="details[' +
                                data.id + '_' + variantId +
                                '][subtotal]" value="0"></td>'; // subtotal
                            row += '</tr>';

                            $('#product-table-body').append(row);
                            updateGrandTotal();
                        }
                    });
                }
            });

            // $('#product-table-body').on('input', '.item-quantity', function() {
            //     var row = $(this).closest('tr');
            //     var quantity = parseFloat($(this).val()) || 0;
            //     var unitPrice = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '')) || 0;
            //     var taxPrice = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '')) || 0;
            //     var totalPrice = (unitPrice + taxPrice) * quantity;

            //     row.find('.item-total').text('Rp ' + totalPrice.toFixed(2));
            //     row.find('.item-subtotal').val(totalPrice.toFixed(2));
            //     updateGrandTotal();
            // });

            // $('#tax_rate, #discount, #shipping').on('input', function() {
            //     updateGrandTotal();
            // });

            // function updateGrandTotal() {
            //     var grandTotal = 0;
            //     $('#product-table-body tr').each(function() {
            //         var total = parseFloat($(this).find('.item-total').text().replace('Rp ', '')) || 0;
            //         if (!isNaN(total)) {
            //             grandTotal += total;
            //         }
            //     });

            //     var discount = parseFloat($('#discount').val()) || 0;
            //     var shipping = parseFloat($('#shipping').val()) || 0;
            //     var taxRate = parseFloat($('#tax_rate').val()) || 0;
            //     var taxNet = (taxRate / 100) * grandTotal;

            //     $('#tax_net').val(taxNet.toFixed(2));
            //     grandTotal = grandTotal - discount + shipping + taxNet;

            //     $('#grandTotal').val(grandTotal.toFixed(2));
            //     $('#paying_amount').val(grandTotal.toFixed(2));

            //     // Update the displayed values in the table
            //     $('#basic-table tr:nth-child(1) th').text('Rp ' + taxNet.toFixed(2)); // Order Tax
            //     $('#basic-table tr:nth-child(2) th').text('Rp ' + discount.toFixed(2)); // Discount
            //     $('#basic-table tr:nth-child(3) th').text('Rp ' + shipping.toFixed(2)); // Shipping
            //     $('#basic-table tr:nth-child(4) th').text('Rp ' + grandTotal.toFixed(2)); // Grand Total
            // }
            // Item quantity change event handler
            $('#product-table-body').on('input', '.item-quantity', function() {
                var row = $(this).closest('tr');
                var quantity = parseFloat($(this).val()) || 0;
                // var maxQuantity = parseFloat($(this).data('max-quantity')) || 0;

                // if (quantity > maxQuantity) {
                //     alert('The quantity cannot exceed the available stock.');
                //     $(this).val(maxQuantity);
                //     quantity = maxQuantity;
                // }

                var unitPrice = parseFloat(row.find('td:eq(2)').text().replace('Rp ', '')) || 0;
                var taxPrice = parseFloat(row.find('td:eq(6)').text().replace('Rp ', '')) || 0;

                var discount = 0;
                if (quantity > 10) {
                    discount = unitPrice * 0.05 * quantity; // 5% discount for quantity > 10
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

            // Function to update grand total
            function updateGrandTotal() {
                var grandTotal = 0;

                // Iterate through each row in the product table
                $('#product-table-body tr').each(function() {
                    var total = parseFloat($(this).find('.item-total').text().replace('Rp ', '')) || 0;
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
