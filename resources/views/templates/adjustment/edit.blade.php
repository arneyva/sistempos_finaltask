@extends('templates.main')

@section('pages_title')
    <h1>Edit Adjustment</h1>
    <p>Do Something with all your adjustment</p>
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
                            <h4 class="card-title">Create Adjustment</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('adjustment.update', ['id' => $adjustment['id']]) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="selectWarehouse">Warehouse/Outlet *</label>
                                    <input type="text" class="form-control" id="selectWarehouseName"
                                        value="{{ $warehouses->firstWhere('id', $adjustment['warehouse_id'])->name }}"
                                        readonly>
                                    <input type="hidden" id="selectWarehouse" name="warehouse_id"
                                        value="{{ $adjustment['warehouse_id'] }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="exampleInputdate">Date *</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ $adjustment['date']->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectProduct">Product *</label>
                                    {{-- <select class="form-select" id="selectProduct" disabled required> --}}
                                    <select class="form-select" id="selectProduct">
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
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Initial Stock</th>
                                                    <th>Current Stock</th>
                                                    <th>Quantity</th>
                                                    <th>Type</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                <!-- Isi dari tbody akan diisi secara dinamis menggunakan JavaScript -->
                                                @foreach ($details as $detail)
                                                    <tr>
                                                        <td>#</td>
                                                        <td>{{ $detail['code'] }}</td>
                                                        <td>{{ $detail['name'] }}</td>
                                                        <td>{{ $detail['ex'] }} {{ $detail['unit'] }}</td>
                                                        <td>{{ $detail['current'] }} {{ $detail['unit'] }}</td>
                                                        <td>
                                                            <input type="number" class="form-control"
                                                                name="details[{{ $loop->index }}][quantity]"
                                                                value="{{ $detail['quantity'] }}" min="0">
                                                        </td>
                                                        <td>
                                                            <select class="form-select"
                                                                name="details[{{ $loop->index }}][type]">
                                                                <option value="add"
                                                                    @if ($detail['type'] === 'add') selected @endif>Add
                                                                </option>
                                                                <option value="sub"
                                                                    @if ($detail['type'] === 'sub') selected @endif>
                                                                    Subtract</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="hidden" name="details[{{ $loop->index }}][id]"
                                                                value="{{ $detail['id'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="hidden"
                                                                name="details[{{ $loop->index }}][product_id]"
                                                                value="{{ $detail['product_id'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="hidden"
                                                                name="details[{{ $loop->index }}][product_variant_id]"
                                                                value="{{ $detail['product_variant_id'] }}">
                                                        </td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-row">Delete</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationDefault05">Description</label>
                                    <input type="text" class="form-control" id="validationDefault05" name="notes"
                                        required value="{{ $adjustment['notes'] }}">
                                </div>
                            </div>
                            <div class="form-group mt-2">
                                <button class="btn btn-primary" type="submit">Update Adjustment</button>
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
            let newIndex = 0;

            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr').remove();
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
                            row += '<td>' + data.code + '</td>';
                            row += '<td>' + data.name + '</td>';
                            row += '<td>' + 'New Data' + '</td>';
                            row += '<td>' + data.qty + '</td>';
                            row +=
                                '<td><input type="number" class="form-control" name="details[new-' +
                                newIndex + '][quantity]" value="0" min="0"></td>';
                            row += '<td><select class="form-select" name="details[new-' +
                                newIndex +
                                '][type]"><option value="add">Add</option><option value="sub">Subtract</option></select></td>';
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][id]" value="new"></td>'; //ini id coy
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][product_id]" value="' + data.id + '"></td>';
                            row += '<td><input type="hidden" name="details[new-' + newIndex +
                                '][product_variant_id]" value="' + (variantId || '') +
                                '"></td>';
                            row +=
                                '<td><button type="button" class="btn btn-danger btn-sm delete-row">Delete</button></td>';
                            row += '</tr>';

                            $('#product-table-body').append(row);
                            newIndex++;
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
        });
    </script>
@endpush
