@extends('templates.main')

@section('pages_title')
<h1>{{ $data[0]['brand'] }} {{$data[0]['name']}} Detail</h1>
<p>Do Something with all your measurement</p>
@endsection

@section('content')
    {{-- part 1 --}}
    <div class="col-md-12 col-lg-12">
        <div class="row row-cols-1">
            <div class="overflow-hidden d-slider1 ">
                <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                    @if ($data[0]['is_variant'] == 'Yes')
                        @foreach ($data[0]['CountQTY_variants'] as $variant)
                            <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                                <div class="card-body">
                                    <div class="progress-widget">
                                        <div class="progress-detail">
                                            <p class="mb-2">{{ $variant['mag'] }}</p>
                                            <p class="mb-2">{{ $variant['variant'] }}</p>
                                            <span class="mb-2"
                                                style="padding: 7px;
                                            border-radius: 7px;
                                            background-color: #ffefef;
                                            color: #F24D4D;">
                                                Stock Alert : 15</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @else
                        @foreach ($data[0]['CountQTY'] as $qty)
                            <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                                <div class="card-body">
                                    <div class="progress-widget">
                                        <div class="progress-detail">
                                            <p class="mb-2">{{ $qty['mag'] }}</p>
                                            <span class="mb-2"
                                                style="padding: 7px;
                                        border-radius: 7px;
                                        background-color: #ffefef;
                                        color: #F24D4D;">
                                                Stock Alert : 15</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
                <div class="swiper-button swiper-button-next"></div>
                <div class="swiper-button swiper-button-prev"></div>
            </div>
        </div>
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
                                    <tr>
                                        <td>Product Cost</td>
                                        <th>{{ $data[0]['cost'] }}</th>
                                    </tr>
                                    <tr>
                                        <td>Product Price</td>
                                        <th>{{ $data[0]['price'] }}</th>
                                    </tr>
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
                            @if ($data[0]['is_variant'] == 'Yes')
                                <table class="table table-hover table-bordered table-sm" style="margin-top: 30px">
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
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
@endsection
