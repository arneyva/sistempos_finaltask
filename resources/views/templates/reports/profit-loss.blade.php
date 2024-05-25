@extends('templates.main')
<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffefef;
        color: #F24D4D;
    }
</style>
@section('content')
    <div class="col-md-12">
        <div class="card">
            {{-- <input class="input-group-text d-inline"
            style="align-self:center;margin-top:20px;border-radius:5px;padding-left:20px;border-color:#b19785" type="text"
            name="daterange" value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' /> --}}
            <input class="input-group-text d-inline"
                style="align-self:center;margin-top:20px;border-radius:5px;padding-left:20px;border-color:#b19785"
                type="text" name="daterange" value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Profit and Loss</h4>
                </div>
                <div class="col-md-4 mb-3">
                    {{-- <label class="form-label" for="selectWarehouse">Warehouse/Outlet *</label> --}}
                    <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                        <option selected disabled value="">Warehouse/Outlet</option>
                        <option value="">Warehouse 1</option>
                        {{-- @foreach ($warehouse as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach --}}
                    </select>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="card" style="background-color: #fbf9f6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/sales.svg') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">(4) Sales</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">(5) Purchases</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/sales-return.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">(0) Sales Return</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase-return.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">(0) Purchases Return</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Expenses</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Revenue</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Profit Net (Using FIFO METHOD)</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Profit Net (Using Average Cost)</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Payments Received</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Payments Sent</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="swiper-slide card card-slide">
                                            <div class="card-body">
                                                <div class="progress-widget">
                                                    <div
                                                        class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                        <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                            alt="purchase" style="max-height: 70px;max-width: 70px">
                                                    </div>
                                                    <div class="progress-detail">
                                                        <p class="mb-2">Payments Net</p>
                                                        <h4 class="counter">$185K</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left'
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end
                    .format('YYYY-MM-DD'));
            });
        });
    </script>
@endpush
