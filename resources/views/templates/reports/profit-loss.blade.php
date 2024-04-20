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
        <div class="row">
            <div class="col-md-12">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="swiper-slide card card-slide">
                                <div class="card-body">
                                    <div class="progress-widget">
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
                                        <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                            <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                                style="max-height: 70px;max-width: 70px">
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
@endsection
