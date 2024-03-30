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
                            <h4 class="card-title">Create Adjustment</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('adjustment.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="validationDefault01">Warehouse/Outlet *</label>
                                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                                        <option selected disabled value="">Choose...</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="exampleInputdate">Date *</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <select class="form-select" id="selectProduct" name="product" required>
                                        <option selected disabled value="">Choose...</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="basic-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Stock</th>
                                                    <th>Qty</th>
                                                    <th>type</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body"> <!-- Tambahkan id pada tbody -->
                                                <!-- Isi dari tbody akan diisi secara dinamis menggunakan JavaScript -->
                                            </tbody>
                                        </table>
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
    <script>
        $(document).ready(function() {
            var dataEntered = false; // Menyimpan status apakah data sudah dimasukkan atau belum
            $("#selectWarehouse").select2({
                placeholder: 'Choose Warehouse',
                ajax: {
                    url: "{{ route('adjustment.warehouse') }}",
                    processResults: function({
                        data
                    }) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.name
                                }
                            })
                        }
                    }
                }
            });

            // Initialize select box for products as Select2
            $("#selectProduct").select2({
                placeholder: 'Choose Product',
                // disabled: true jadi semua ke disable 
            });

            // Add event listener for change in product select
            $("#selectProduct").change(function() {
                var productId = $(this).val();
                var productCode = $("#selectProduct option:selected").data('product_code');
                var productName = $("#selectProduct option:selected").text();
                var stock = 1; // Dapatkan stok dari data yang Anda terima dari server
                addProductToTable(productId, productCode, productName, stock, );
                dataEntered = true; // Setel status bahwa data telah dimasukkan
                $("#selectWarehouse").prop('disabled',
                    true); // Menonaktifkan dropdown gudang setelah data dimasukkan
            });

            // Tambahkan event listener untuk perubahan pada select produk gudang
            $("#selectWarehouse").change(function() {
                updateCategoryDropdown();
            });

            function updateCategoryDropdown() {
                if (!dataEntered) {
                    let warehouseId = $('#selectWarehouse').val();
                    $.ajax({
                        url: "{{ url('adjustment/product-warehouse') }}/" + warehouseId,
                        type: 'GET',
                        success: function(response) {
                            let data = response.data;
                            let options = [];

                            // Tambahkan placeholder secara eksplisit
                            options.push({
                                id: '',
                                text: 'Choose Product'
                            });

                            if (data.length > 0) {
                                // Tambahkan opsi produk dari gudang
                                options = options.concat($.map(data, function(item) {
                                    let cekvariant = item.product.is_variant;
                                    if (cekvariant == 1) {
                                        return {
                                            id: item.id,
                                            // text: item.product.name
                                            text: `${item.variant.code} - ${item.variant.name} (${item.product.name})`
                                        };
                                    } else {
                                        return {
                                            id: item.id,
                                            // text: item.product.name
                                            product_code: item.product.code,
                                            text: `${item.product.code} - ${item.product.name}`
                                        };
                                    }
                                }));
                            } else {
                                options.push({
                                    id: '',
                                    text: 'No Products'
                                });
                            }

                            // Empty the select element first
                            $("#selectProduct").empty();

                            // Set placeholder and add dynamic options
                            $("#selectProduct").select2({
                                placeholder: 'Choose Product',
                                data: options
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            }


            // Fungsi untuk menambahkan produk gudang ke dalam tabel
            function addProductToTable(productId, productCode, productName, stock) {
                var tableBody = $("#product-table-body");
                var rowCount = tableBody.find("tr").length + 1;
                var newRow = "<tr>" +
                    "<td>" + rowCount + "</td>" +
                    "<td>" + productId + "</td>" +
                    "<td>" + productName + "</td>" +
                    "<td>" + stock + "</td>" +
                    "<td><input type='number' class='form-control' name='qty[]' min='0' required></td>" +
                    "<td><select class='form-select' name='type[]'><option value='add'>Add</option><option value='sub'>Subtract</option></select></td>" +
                    "<td><button type='button' class='btn btn-danger btn-sm delete-row'>Delete</button></td>" +
                    "</tr>";
                tableBody.append(newRow);
            }

            // Event listener untuk menghapus baris dari tabel
            $(document).on("click", ".delete-row", function() {
                $(this).closest("tr").remove();
                updateRowNumbers();
            });

            // Fungsi untuk memperbarui nomor urut setelah penghapusan baris
            function updateRowNumbers() {
                $("#product-table-body").find("tr").each(function(index) {
                    $(this).find("td:first").text(index + 1);
                });
            }
        });
    </script>
@endpush
