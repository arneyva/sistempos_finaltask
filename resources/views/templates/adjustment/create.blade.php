@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Add Adjustment') }}</h1>
    <p>{{ __('Do Something with all your adjustment') }}</p>
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

    /* .swal2-container {
    width: 620px !important;
} */
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
                            <h4 class="card-title">{{ __('Create Adjustment') }}</h4>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="card-body">
                        <form action="{{ route('adjustment.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="selectWarehouse">{{ __('Warehouse/Outlet') }} *</label>
                                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                                        <option selected disabled value="">{{ __('Choose...') }}</option>
                                        @foreach ($warehouse as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="exampleInputdate">{{ __('Date') }} *</label>
                                    <input type="date" class="form-control" id="exampleInputdate" name="date"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="selectProduct">{{ __('Product') }} *</label>
                                    <select class="form-select" id="selectProduct" disabled>
                                        <option selected disabled value="">
                                            {{ __('Scan/Search Product by Code or Name') }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <div class="table-responsive">
                                        <table id="product-table" class="table table-striped mb-0" role="grid">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ __('Code') }}</th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Stock') }}</th>
                                                    <th>{{ __('Quantity') }}</th>
                                                    <th>{{ __('Type') }}</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="product-table-body">
                                                <!-- Isi dari tbody akan diisi secara dinamis menggunakan JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="validationDefault05">{{ __('Description') }}</label>
                                    <input type="text" class="form-control" id="validationDefault05" name="notes"
                                        placeholder="{{ __('a few words...') }}">
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
        });
    </script>
    <script>
        // Fungsi untuk menambahkan event listener untuk tombol delete di dalam tbody
        $(document).ready(function() {
            $('#product-table-body').on('click', '.delete-row', function() {
                $(this).closest('tr')
                    .remove(); // Menghapus baris tabel yang berisi tombol delete yang diklik
                $('#selectProduct').val('').trigger('change');
            });
            $('#selectWarehouse').on('change', function() {
                // Kosongkan tabel produk ketika warehouse diubah
                $('#product-table-body').empty();
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
                    var isDuplicate = false;
                    $('#product-table-body tr').each(function() {
                        var existingProductId = $(this).find('input[name$="[product_id]"]').val();
                        var existingVariantId = $(this).find('input[name$="[product_variant_id]"]')
                            .val() || null;
                        if (existingProductId == productId && existingVariantId == variantId) {
                            isDuplicate = true;
                            var quantityInput = $(this).find('input[name$="[quantity]"]');
                            var currentQuantity = parseInt(quantityInput.val());
                            quantityInput.val(currentQuantity + 1); // Increase the quantity by 1
                            $('#selectProduct').val('').trigger('change');
                            return false; // Stop the loop
                        }
                    });
                    if (isDuplicate) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Produk sudah ditambahkan. Jumlah produk telah ditingkatkan.',
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
                                var row = '<tr>';
                                row += '<td>#</td>';
                                row += '<td>' + data.code + '</td>';
                                row += '<td>' + data.name + '</td>';
                                row += '<td>' + data.qty + ' ' + data.unit + '</td>';
                                row +=
                                    '<td><input type="number" class="form-control item-quantity" name="details[' +
                                    data.id + '_' + variantId + '][quantity]" value="' +
                                    initialQuantity +
                                    '" data-min-quantity="1" ></td>';
                                row +=
                                    '<td><select class="form-select" name="details[' +
                                    data
                                    .id +
                                    '_' + variantId +
                                    '][type]"><option value="add">Add</option><option value="sub">Subtract</option></select></td>';
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
                                row += '</td>';
                                row += '</tr>';
                                $('#product-table-body').append(row);
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
                var minQuantity = parseFloat($(this).data('min-quantity')) || 1;
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
            });
        });
    </script>
@endpush
