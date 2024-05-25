@extends('templates.main')

@section('pages_title')
<h1>Reports</h1>
<p>look up your daily report</p>
@endsection

<style>
    .status-completed {
        padding: 7px;
        border-radius: 7px;
        background-color: #c9f1c4;
        color: #0f4c11;
    }

    .status-ordered {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff1c4;
        color: #4c4b0f;
    }

    .status-pending {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4c4;
        color: #4c0f0f;
    }

    .payment-paid {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4d9f1;
        color: #105e7f;
    }

    .payment-unpaid {
        padding: 7px;
        border-radius: 7px;
        background-color: #f0c4f1;
        color: #7f107b;
    }

    .payment-partial {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1dcc4;
        color: #7f6710;
    }

    .shipping-shipped {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4c8f1;
        color: #33107f;
    }

    .shipping-delivered {
        padding: 7px;
        border-radius: 7px;
        background-color: #c4f1d1;
        color: #107f2c;
    }

    .shipping-packed {
        padding: 7px;
        border-radius: 7px;
        background-color: #b19785;
        color: #583606;
    }

    .shipping-cancelled {
        padding: 7px;
        border-radius: 7px;
        background-color: #f1c4e1;
        color: #7f104f;
    }
</style>
@section('content')
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div style="align-self:center;margin-top:20px;">
                        <select class="form-select" id="selectWarehouse" name="warehouse_id" required>
                            <option selected disabled value="">Choose Warehouse/Outlet</option>
                            <option value="">Warehouse 1</option>
                        </select>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/sales.svg') }}" alt="purchase"
                                                    style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Sales</p>
                                                <h4 class="counter">$185K</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Purchases</p>
                                                <h4 class="counter">$185K</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/purchase-return.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Purchases Return</p>
                                                <h4 class="counter">$185K</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="swiper-slide card card-slide">
                                    <div class="card-body">
                                        <div class="progress-widget">
                                            <div
                                                class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                                <img src="{{ asset('hopeui/html/assets/images/sales-return.png') }}"
                                                    alt="purchase" style="max-height: 70px;max-width: 70px">
                                            </div>
                                            <div class="progress-detail">
                                                <p class="mb-2">Sales Return</p>
                                                <h4 class="counter">$375K</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--  --}}
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="card-body" style="background-color:bisque">
                                <ul class="d-flex nav nav-pills mb-0 text-center profile-tab" data-toggle="slider-tab"
                                    id="profile-pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active show" data-bs-toggle="tab" href="#profile-feed"
                                            role="tab" aria-selected="false">Sales</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#profile-friends" role="tab"
                                            aria-selected="false">Sales Return</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#adjustment" role="tab"
                                            aria-selected="false">Purchases Return</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#expenses" role="tab"
                                            aria-selected="false">Expenses</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-12">
                                <div class="profile-content tab-content">
                                    <div id="profile-feed" class="tab-pane fade active show">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="input-group search-input" style="width: 30%">
                                                <span class="input-group-text d-inline" id="search-input">
                                                    <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="11.7669" cy="11.7666" r="8.98856"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></circle>
                                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <input type="search" class="form-control" placeholder="Search...">
                                            </div>
                                            <div class="header-title">
                                                <button type="button" class="btn btn-soft-success">PDF</button>
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>Customer</th>
                                                            <th>Warehouse</th>
                                                            <th>Grand Total</th>
                                                            <th>Paid</th>
                                                            <th>Due</th>
                                                            <th>Status</th>
                                                            <th>Payment</th>
                                                            <th>Shipping </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>SL-01</td>
                                                            <td>Budi Pranomo</td>
                                                            <td>Warehouse 1</td>
                                                            <td>Rp. 50000</td>
                                                            <td>Rp 0</td>
                                                            <td>Rp 50000</td>
                                                            <td> <span class="status-completed">completed</span>
                                                            </td>
                                                            <td><span class="payment-paid">paid</span></td>
                                                            <td><span class="shipping-shipped">shipped</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>SL-01</td>
                                                            <td>Budi Pranomo</td>
                                                            <td>Warehouse 1</td>
                                                            <td>Rp. 50000</td>
                                                            <td>Rp 0</td>
                                                            <td>Rp 50000</td>
                                                            <td> <span class="status-ordered">ordered</span>
                                                            </td>
                                                            <td><span class="payment-unpaid">unpaid</span></td>
                                                            <td><span class="shipping-packed">packed</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>SL-01</td>
                                                            <td>Budi Pranomo</td>
                                                            <td>Warehouse 1</td>
                                                            <td>Rp. 50000</td>
                                                            <td>Rp 0</td>
                                                            <td>Rp 7000</td>
                                                            <td> <span class="status-pending">pending</span>
                                                            </td>
                                                            <td><span class="payment-partial">partial</span></td>
                                                            <td><span class="shipping-cancelled">cancelled</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td>SL-01</td>
                                                            <td>Budi Pranomo</td>
                                                            <td>Warehouse 1</td>
                                                            <td>Rp. 50000</td>
                                                            <td>Rp 0</td>
                                                            <td>Rp 7000</td>
                                                            <td> <span class="status-pending">pending</span>
                                                            </td>
                                                            <td><span class="payment-partial">partial</span></td>
                                                            <td><span class="shipping-delivered">delivered</span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{-- {{ $sales->links() }} --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="profile-friends" class="tab-pane fade">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="input-group search-input" style="width: 30%">
                                                <span class="input-group-text d-inline" id="search-input">
                                                    <svg class="icon-18" width="18" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="11.7669" cy="11.7666" r="8.98856"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></circle>
                                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <input type="search" class="form-control" placeholder="Search...">
                                            </div>
                                            <div class="header-title">
                                                <button type="button" class="btn btn-soft-success">PDF</button>
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>Customer Name</th>
                                                            <th>Sale Ref</th>
                                                            <th>Warehouse</th>
                                                            <th>Grand Total</th>
                                                            <th>Paid</th>
                                                            <th>Due</th>
                                                            <th>Status</th>
                                                            <th>Payment</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{-- {{ $sales->links() }} --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="adjustment" class="tab-pane fade">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="input-group search-input" style="width: 30%">
                                                <span class="input-group-text d-inline" id="search-input">
                                                    <svg class="icon-18" width="18" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="11.7669" cy="11.7666" r="8.98856"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></circle>
                                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <input type="search" class="form-control" placeholder="Search...">
                                            </div>
                                            <div class="header-title">
                                                <button type="button" class="btn btn-soft-success">PDF</button>
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>Reference</th>
                                                            <th>Supplier</th>
                                                            <th>Warehouse</th>
                                                            <th>Purchase ref</th>
                                                            <th>Grand Total</th>
                                                            <th>Paid</th>
                                                            <th>Due</th>
                                                            <th>Status</th>
                                                            <th>Payment Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{-- {{ $sales->links() }} --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="expenses" class="tab-pane fade">
                                        <div class="card-header d-flex justify-content-between">
                                            <div class="input-group search-input" style="width: 30%">
                                                <span class="input-group-text d-inline" id="search-input">
                                                    <svg class="icon-18" width="18" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="11.7669" cy="11.7666" r="8.98856"
                                                            stroke="currentColor" stroke-width="1.5"
                                                            stroke-linecap="round" stroke-linejoin="round"></circle>
                                                        <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                                <input type="search" class="form-control" placeholder="Search...">
                                            </div>
                                            <div class="header-title">
                                                <button type="button" class="btn btn-soft-success">PDF</button>
                                                <button type="button" class="btn btn-soft-danger">Excel</button>
                                            </div>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive mt-4">
                                                <table id="basic-table" class="table table-striped mb-0" role="grid">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Reference</th>
                                                            <th>Warehouse</th>
                                                            <th>Details</th>
                                                            <th>Amount</th>
                                                            <th>Category</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                                <div class="bd-example"
                                                    style="margin-left: 10px; margin-top:10px; margin-right:10px">
                                                    {{-- {{ $sales->links() }} --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-12 mt-5">
                                <div class="row">
                                    <div class="col-md-12 col-xl-6">
                                        <div class="card" data-aos="fade-up" data-aos-delay="900">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h4 class="card-title">Total Items & Quantity</h4>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="flex-wrap d-flex align-items-center justify-content-between">
                                                    <div> <canvas id="chartjs1"
                                                            class="col-md-8 col-lg-8 myChart"></canvas></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xl-6">
                                        <div class="card" data-aos="fade-up" data-aos-delay="900">
                                            <div class="flex-wrap card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h4 class="card-title">Value by Cost and Price</h4>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="flex-wrap d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <canvas id="chartjs2" class="col-md-8 col-lg-8 myChart"></canvas>
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
        var ctx = document.getElementById('chartjs1').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Warehouse 1', 'Warehouse 2', 'Warehouse 3'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3],
                    // backgroundColor: [
                    //     'red',
                    //     'blue',
                    //     'yellow'
                    // ],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right', // Atur posisi legenda di sini
                        labels: {
                            font: {
                                size: 16 // Atur ukuran font legenda di sini
                            }
                        }
                    }
                }
            }
        });
    </script>
    <script>
        const ctx1 = document.getElementById('chartjs2');
        new Chart(ctx1, {
            type: 'pie',
            data: {
                labels: ['warehouse1', 'warehouse2', 'Warehouse 3'],
                datasets: [{
                    label: 'warehouse1',
                    data: [12, 30, 40],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'right', // Atur posisi legenda di sini
                        labels: {
                            font: {
                                size: 16 // Atur ukuran font legenda di sini
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
