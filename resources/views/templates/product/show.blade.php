@extends('templates.main')
@push('style')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
@endpush
@section('pages_title')
    <h1>{{ $data[0]['brand'] }} {{ $data[0]['name'] }} Detail</h1>
    <p>Do Something with all your measurement</p>
@endsection
<style>
    .swiper-button-next,
    .swiper-button-prev {
        top: 50%;
        transform: translateY(-50%);
        border-radius: 50% !important;
        color: rgb(80, 104, 209) !important;
        width: 15px !important;
        height: 15px !important;
        background: none;
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 15px !important;
    }

    .swiper-container {
        padding: 0 40px;
    }
</style>
@section('content')
    <div class="col-md-12 col-lg-12">
        <form action="{{ route('updateAlertStock') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $data[0]['id'] }}">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @if ($data[0]['is_variant'] == 'Yes')
                        @foreach ($data[0]['CountQTY_variants'] as $variant)
                            <div class="swiper-slide">
                                <div class="card card-slide" data-aos="fade-up" data-aos-delay="700">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div class="progress-detail">
                                                <p class="mb-2">{{ $variant['mag'] }} ~ {{ $variant['variant'] }}</p>
                                                <p class="mb-2">Stock Alert</p>
                                                <div class="form-group">
                                                    <input type="number"
                                                        name="stock_alert[{{ $variant['variant'] }}][{{ $variant['mag'] }}]"
                                                        id="stock_alert_variant_{{ $variant['variant'] }}_{{ $variant['mag'] }}"
                                                        class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $variant['stock_alert'] ?? 0 }}" min="0">
                                                </div>
                                                <p class="mb-2">Discount Threshold</p>
                                                <div class="form-group input-group">
                                                    <span class="input-group-text" id="basic-addon1">%</span>
                                                    <input type="number"
                                                        name="discount_percentage[{{ $variant['variant'] }}][{{ $variant['mag'] }}]"
                                                        id="discount_percentagevariant{{ $variant['variant'] }}_{{ $variant['mag'] }}"
                                                        class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $variant['discount_percentage'] ?? 0 }}" min="0">
                                                </div>
                                                <div class="form-group input-group">
                                                    <span class="input-group-text" id="basic-addon1"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                            viewBox="0 0 24 24">
                                                            <path fill="none" stroke="#3b39d0" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m5 18l14-4M5 14l14-4L5 6" />
                                                        </svg></span>
                                                    <input type="number"
                                                        name="quantity_discount[{{ $variant['variant'] }}][{{ $variant['mag'] }}]"
                                                        id="quantity_discountvariant{{ $variant['variant'] }}_{{ $variant['mag'] }}"
                                                        class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $variant['quantity_discount'] ?? 0 }}" min="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @foreach ($data[0]['CountQTY'] as $qty)
                            <div class="swiper-slide">
                                <div class="card card-slide" data-aos="fade-up" data-aos-delay="700">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div class="progress-detail">
                                                <p class="mb-2">{{ $qty['mag'] }}</p>
                                                <p class="mb-2">Stock Alert</p>
                                                <div class="form-group">
                                                    <input type="number" name="stock_alert[{{ $qty['mag'] }}]"
                                                        id="stock_alert_qty_{{ $qty['mag'] }}" class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $qty['stock_alert'] ?? 0 }}">
                                                </div>
                                                <p class="mb-2">Discount Threshold</p>
                                                <div class="form-group input-group">
                                                    <span class="input-group-text" id="basic-addon1">%</span>
                                                    <input type="number" name="discount_percentage[{{ $qty['mag'] }}]"
                                                        id="discount_percentage{{ $qty['mag'] }}" class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $qty['discount_percentage'] ?? 0 }}">
                                                </div>
                                                <div class="form-group input-group">
                                                    <span class="input-group-text" id="basic-addon1"><svg
                                                            xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                            viewBox="0 0 24 24">
                                                            <path fill="none" stroke="#3b39d0" stroke-linecap="round"
                                                                stroke-linejoin="round" stroke-width="2"
                                                                d="m5 18l14-4M5 14l14-4L5 6" />
                                                        </svg></span>
                                                    <input type="number" name="quantity_discount[{{ $qty['mag'] }}]"
                                                        id="quantity_discount{{ $qty['mag'] }}" class="form-control"
                                                        style="padding: 7px; border-radius: 7px; background-color: #ffefef; color: #F24D4D;"
                                                        value="{{ $qty['quantity_discount'] ?? 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <button type="submit" class="btn btn-primary mt-3" style="display: none">Simpan</button>
            </div>
        </form>
    </div>
    <div class="col-md-12 col-lg-8">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Product Information</h4>
                        </div>
                        <a href="#"><button type="button" class="btn btn-soft-primary">Print</button></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-4">
                            <table id="basic-table" class="table table-striped mb-0" role="grid">
                                <tbody>
                                    <tr>
                                        <td>Product Name</td>
                                        <th>{{ $data[0]['name'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <th>{{ $data[0]['type_name'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Code Product</td>
                                        <th>{{ $data[0]['code'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Category</td>
                                        <th>{{ $data[0]['cateogry'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Brand</td>
                                        <th>{{ $data[0]['brand'] }}</th>
                                    </tr>
                                    @if ($data[0]['type'] == 'is_single')
                                        <tr>
                                            <td>Product Cost</td>
                                            <th>{{ $data[0]['cost'] }}</th>
                                        </tr>
                                        <tr>
                                            <td>Product Price</td>
                                            <th>{{ $data[0]['price'] }}</th>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Unit</td>
                                        <th>{{ $data[0]['unit'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Tax</td>
                                        <th>{{ $data[0]['tax'] }} %</th>
                                    </tr>
                                </tbody>
                            </table>
                            {{-- handle for variant --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($data[0]['is_variant'] == 'Yes')
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">Variant Detail</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-4">
                            <table id="product-table" class="table table-striped mb-0" role="grid">
                                <thead>
                                    <tr>
                                        <th>Variant Name</th>
                                        <th>Variant code</th>
                                        <th>Variant cost</th>
                                        <th>Variant price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data[0]['products_variants_data'] as $variant)
                                        <tr>
                                            <td>{{ $variant['name'] }}</td>
                                            <td>{{ $variant['code'] }}</td>
                                            <td>{{ $variant['cost'] }}</td>
                                            <td>{{ $variant['price'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- part 3 sisi kanan --}}
    <div class="col-md-12 col-lg-4">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="card credit-card-widget" data-aos="fade-up" data-aos-delay="900">
                    <div class="pb-4 border-0 card-header">
                        <div class="p-4 border border-white rounded primary-gradient-card">
                            <div class="d-flex justify-content-between align-items-center">
                                <img src="{{ asset('hopeui/html/assets/images/truck.png') }}" alt=""
                                    style="max-width: 100%;max-height: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <table class="table table-striped mb-0" role="grid">
                        @if ($data[0]['is_variant'] == 'Yes')
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data[0]['CountQTY_variants'] as $variant)
                                    <tr>
                                        <td>{{ $variant['mag'] }} </td>
                                        <td>{{ $variant['variant'] }}</td>
                                        <th>{{ $variant['qte'] }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <thead>
                                <tr>
                                    <th>Warehouse</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data[0]['CountQTY'] as $qty)
                                    <tr>
                                        <td>{{ $qty['mag'] }}</td>
                                        <th>{{ $qty['qty'] }}</th>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 'auto',
                spaceBetween: 20,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                watchOverflow: true,
                slidesOffsetBefore: 0,
                slidesOffsetAfter: 0,
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                    },
                    480: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 3,
                    },
                    1024: {
                        slidesPerView: 4,
                    }
                }
            });

            // Sembunyikan tombol navigasi jika tidak diperlukan
            function updateNavigationVisibility() {
                const totalSlides = swiper.slides.length;
                const visibleSlides = swiper.params.slidesPerView;
                const nextButton = document.querySelector('.swiper-button-next');
                const prevButton = document.querySelector('.swiper-button-prev');

                if (totalSlides <= visibleSlides) {
                    nextButton.style.display = 'none';
                    prevButton.style.display = 'none';
                } else {
                    nextButton.style.display = '';
                    prevButton.style.display = '';
                }
            }

            updateNavigationVisibility();
            window.addEventListener('resize', updateNavigationVisibility);
        });
    </script>
@endpush
