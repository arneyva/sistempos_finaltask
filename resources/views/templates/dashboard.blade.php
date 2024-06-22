@extends('templates.main')
@section('content')
    <style>
        .status-completed {
            padding: 7px;
            border-radius: 7px;
            background-color: #c9f1c4;
            color: #0f4c11;
            border: 1px solid #0f4c11;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .status-ordered {
            padding: 7px;
            border-radius: 7px;
            background-color: #eff1c4;
            color: #4c4b0f;
            border: 1px solid #4c4b0f;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .status-pending {
            padding: 7px;
            border-radius: 7px;
            background-color: #f1c4c4;
            color: #4c0f0f;
            border: 1px solid #4c0f0f;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .payment-paid {
            padding: 7px;
            border-radius: 7px;
            background-color: #c4d9f1;
            color: #105e7f;
            border: 1px solid #105e7f;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .payment-unpaid {
            padding: 7px;
            border-radius: 7px;
            background-color: #f0c4f1;
            color: #7f107b;
            border: 1px solid #7f107b;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .payment-partial {
            padding: 7px;
            border-radius: 7px;
            background-color: #f1dcc4;
            color: #7f6710;
            border: 1px solid #7f6710;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .shipping-shipped {
            padding: 7px;
            border-radius: 7px;
            background-color: #c4c8f1;
            color: #33107f;
            border: 1px solid #33107f;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .shipping-delivered {
            padding: 7px;
            border-radius: 7px;
            background-color: #c4f1d1;
            color: #107f2c;
            border: 1px solid #107f2c;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .shipping-packed {
            padding: 7px;
            border-radius: 7px;
            background-color: #b19785;
            color: #583606;
            border: 1px solid #583606;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }

        .shipping-cancelled {
            padding: 7px;
            border-radius: 7px;
            background-color: #f1c4e1;
            color: #7f104f;
            border: 1px solid #7f104f;
            /* Outline color */
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            /* Shadow */
        }
    </style>
    {{-- slider content --}}
    <div class="col-md-12 col-lg-12">
        <div class="row row-cols-1">
            <div class="overflow-hidden d-slider1 ">
                <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline">
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="700">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div class="">
                                    <img src="{{ asset('hopeui/html/assets/images/sales.svg') }}" alt=""
                                        style="max-height: 70px;max-width: 70px">
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Today Sales</p>
                                    <h4 class="counter">{{ $report['today_sales'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                    <img src="{{ asset('hopeui/html/assets/images/purchase.png') }}" alt="purchase"
                                        style="max-height: 70px;max-width: 70px">
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Today Sales Returns</p>
                                    <h4 class="counter">{{ $report['return_sales'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                    <img src="{{ asset('hopeui/html/assets/images/sales-return.png') }}" alt="purchase"
                                        style="max-height: 70px;max-width: 70px">
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Today Purchases</p>
                                    <h4 class="counter">{{ $report['today_purchases'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="1000">
                        <div class="card-body">
                            <div class="progress-widget">
                                <div class="text-center circle-progress-01 circle-progress circle-progress-primary">
                                    <img src="{{ asset('hopeui/html/assets/images/purchase-return.png') }}" alt="purchase"
                                        style="max-height: 70px;max-width: 70px">
                                </div>
                                <div class="progress-detail">
                                    <p class="mb-2">Today Purchases Returns</p>
                                    <h4 class="counter">{{ $report['return_purchases'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
                <div class="swiper-button swiper-button-next"></div>
                <div class="swiper-button swiper-button-prev"></div>
            </div>
        </div>
    </div>
    {{-- left section --}}
    <div class="col-md-12 col-lg-8">
        <div class="row">
            <div class="col-md-12">

                <div class="card" data-aos="fade-up" data-aos-delay="800">
                    <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h6 class="card-title">This Week Payment Sent & Received</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="lineChart" style="height: 400px;"></canvas>
                    </div>
                </div>
            </div>
            {{--  --}}
            <div class="col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Stock Alert Products
                            </h4>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive mt-4">
                            <table id="basic-table" class="table table-striped mb-0" role="grid">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Warehouse</th>
                                        <th>Current Stock</th>
                                        <th style="color: red">Stock Alert</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stock as $item)
                                        <tr>
                                            <td>{{ $item['product_name'] }}</td>
                                            <td>{{ $item['warehouse_name'] }}</td>
                                            <td>{{ $item['stock'] }}</td>
                                            <td>{{ $item['alert'] }} ~ <a href="{{ route('adjustment.create') }}"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                                        viewBox="0 0 24 24">
                                                        <path fill="#7d6bd6"
                                                            d="M20 17H6q-.825 0-1.412-.587T4 15V5H3q-.425 0-.712-.288T2 4t.288-.712T3 3h1q.825 0 1.413.588T6 5v10h14q.425 0 .713.288T21 16t-.288.713T20 17M6 22q-.825 0-1.412-.587T4 20t.588-1.412T6 18t1.413.588T8 20t-.587 1.413T6 22m2-8q-.425 0-.712-.288T7 13V9q0-.425.288-.712T8 8h4q.425 0 .713.288T13 9v4q0 .425-.288.713T12 14zm7 0q-.425 0-.712-.288T14 13V9q0-.425.288-.712T15 8h4q.425 0 .713.288T20 9v4q0 .425-.288.713T19 14zm4 8q-.825 0-1.412-.587T17 20t.588-1.412T19 18t1.413.588T21 20t-.587 1.413T19 22" />
                                                    </svg></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--  --}}

            {{-- clients content --}}
            <div class="col-md-12 col-lg-12">
                <div class="overflow-hidden card" data-aos="fade-up" data-aos-delay="600">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="mb-2 card-title">Daily Employee Attendance Report</h4>
                            <p class="mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em"
                                    viewBox="0 0 2048 2048">
                                    <path fill="#7d6bd6"
                                        d="M1792 993q60 41 107 93t81 114t50 131t18 141q0 119-45 224t-124 183t-183 123t-224 46q-91 0-176-27t-156-78t-126-122t-85-157H128V128h256V0h128v128h896V0h128v128h256zM256 256v256h1408V256h-128v128h-128V256H512v128H384V256zm643 1280q-3-31-3-64q0-86 24-167t73-153h-97v-128h128v86q41-51 91-90t108-67t121-42t128-15q100 0 192 33V640H256v896zm573 384q93 0 174-35t142-96t96-142t36-175q0-93-35-174t-96-142t-142-96t-175-36q-93 0-174 35t-142 96t-96 142t-36 175q0 93 35 174t96 142t142 96t175 36m64-512h192v128h-320v-384h128zM384 1024h128v128H384zm256 0h128v128H640zm0-256h128v128H640zm-256 512h128v128H384zm256 0h128v128H640zm384-384H896V768h128zm256 0h-128V768h128zm256 0h-128V768h128z" />
                                </svg>
                                {{ $today }}
                            </p>
                        </div>
                    </div>
                    <div class="p-0 card-body">
                        <div class="mt-4 table-responsive">
                            <table id="basic-table" class="table mb-0 table-striped" role="grid">
                                <thead>
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Warehouse Name</th>
                                        <th>Clock In</th>
                                        <th>Clock Out</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendance as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($item['employee_image'] == null)
                                                        <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                            src="{{ asset('hopeui/html/assets/images/shapes/01.png') }}"
                                                            alt="profile">
                                                    @else
                                                        <img class="rounded bg-soft-primary img-fluid avatar-40 me-3"
                                                            src="" alt="profile-ada">
                                                    @endif
                                                    <h6>{{ $item['employee_name'] }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                {{ $item['warehouse_name'] }}
                                            </td>
                                            <td>{{ $item['clock_in'] }}</td>
                                            <td>
                                                {{ $item['clock_out'] }}
                                            </td>
                                            <td>{{ $item['status'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
                <div class="card" data-aos="fade-up" data-aos-delay="900">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Top Customers ~ {{ $currentMonth }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-wrap d-flex align-items-center justify-content-between">
                            <div class="col-md-8 col-lg-8">
                                <canvas id="myChart"></canvas>
                            </div>
                            <div class="d-grid gap col-md-4 col-lg-4">
                                @foreach ($topClients as $client)
                                    <div class="d-flex align-items-start">
                                        <div class="ms-3">
                                            <span class="text-gray">{{ $client->name }}</span>
                                            <h6>{{ $client->sales_count }}</h6>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-12">
                <div class="card" data-aos="fade-up" data-aos-delay="900">
                    <div class="flex-wrap card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Top Selling Products ~ {{ $currentMonth }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="flex-wrap d-flex align-items-center justify-content-between">
                            <div class="col-md-8 col-lg-8">
                                <canvas id="myProductChart"></canvas> <!-- ID canvas untuk chart produk -->
                            </div>
                            <div class="d-grid gap col-md-4 col-lg-4">
                                @foreach ($products as $product)
                                    <div class="d-flex align-items-start">
                                        <div class="ms-3">
                                            <span class="text-gray">{{ $product->name }}</span>
                                            <h6>{{ $product->value }}</h6> <!-- Menampilkan jumlah penjualan produk -->
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- sisi bawah --}}
    <div class="col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Recent Sales
                    </h4>
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
                                <th>Status</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentsales as $item)
                                <tr>
                                    <td>{{ $item['Ref'] }}</td>
                                    <td>{{ $item['client_name'] }}</td>
                                    <td>{{ $item['warehouse_name'] }}</td>
                                    <td>
                                        @if ($item['statut'] == 'completed')
                                            <span class="status-completed">completed</span>
                                        @elseif($item['statut'] == 'ordered')
                                            <span class="status-ordered">ordered</span>
                                        @else
                                            <span class="status-pending">pending</span>
                                        @endif
                                    </td>
                                    <td>{{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['paid_amount'], 2, ',', '.') }} </td>
                                    <td>{{ 'Rp ' . number_format($item['due'], 2, ',', '.') }}</td>
                                    <td>
                                        @if ($item['payment_status'] == 'paid')
                                            <span class="payment-paid">paid</span>
                                        @else
                                            <span class="payment-unpaid">unpaid</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- end --}}
@endsection
@push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('myChart').getContext('2d');
            const data = {
                labels: @json($topClients->pluck('name')),
                datasets: [{
                    label: 'Sales Count',
                    data: @json($topClients->pluck('sales_count')),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                },
            };

            new Chart(ctx, config);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('myProductChart').getContext('2d');
            const data = {
                labels: @json($products->pluck('name')), // Nama produk sebagai label
                datasets: [{
                    label: 'Sales Count', // Label untuk dataset
                    data: @json($products->pluck('value')), // Jumlah penjualan produk
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)', // Warna latar belakang
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)', // Warna garis
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                    ],
                    borderWidth: 1
                }]
            };

            const config = {
                type: 'doughnut', // Jenis chart
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed !== null) {
                                        label += context.parsed;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                },
            };

            new Chart(ctx, config); // Membuat instance Chart
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data dari controller
            const payment_sent = @json($payment_sent);
            const payment_received = @json($payment_received);
            const days = @json($days);

            const ctx = document.getElementById('lineChart').getContext('2d');
            const myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [{
                        label: 'Payment Sent',
                        data: payment_sent,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        fill: false,
                    }, {
                        label: 'Payment Received',
                        data: payment_received,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        fill: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Days',
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Amount',
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
