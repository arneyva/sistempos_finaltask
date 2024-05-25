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
    <div class="col-sm-12">
        <div class="card">
            <input class="input-group-text d-inline"
                style="align-self:center;margin-top:20px;border-radius:5px;padding-left:20px;border-color:#b19785"
                type="text" name="daterange" value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Purchase Report</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="input-group search-input" style="width: 30%">
                    <span class="input-group-text d-inline" id="search-input">
                        <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></circle>
                            <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                    <input type="search" class="form-control" placeholder="Search...">
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
                    <button type="button" class="btn btn-soft-success">PDF</button>
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                    <button type="button" class="btn btn-soft-gray">Import Product</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Supplier</th>
                                <th>Warehouse</th>
                                <th>Status</th>
                                <th>Grand Total</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Payment Status</th>
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
                                <td>Rp 10000 </td>
                                <td>Rp 10000</td>
                                <td><span class="shipping-shipped">shipped</span></td>
                            </tr>
                            <tr>
                                <td>SL-01</td>
                                <td>Budi Pranomo</td>
                                <td>Warehouse 1</td>
                                <td>Rp. 50000</td>
                                <td>Rp 0</td>
                                <td>Rp 50000</td>
                                <td>Rp 50000</td>
                                <td>Rp 10000 </td>
                                <td><span class="shipping-packed">packed</span></td>
                            </tr>
                            <tr>
                                <td>SL-01</td>
                                <td>Budi Pranomo</td>
                                <td>Warehouse 1</td>
                                <td>Rp. 50000</td>
                                <td>Rp 0</td>
                                <td>Rp 7000</td>
                                <td>Rp 50000</td>
                                <td>Rp 10000 </td>
                                <td><span class="shipping-cancelled">cancelled</span></td>
                            </tr>
                            <tr>
                                <td>SL-01</td>
                                <td>Budi Pranomo</td>
                                <td>Warehouse 1</td>
                                <td>Rp. 50000</td>
                                <td>Rp 0</td>
                                <td>Rp 7000</td>
                                <td>Rp 50000</td>
                                <td>Rp 10000 </td>
                                <td><span class="shipping-delivered">delivered</span></td>
                            </tr>
                        </tbody>
                        <tr>
                            <td style=""></td>
                            <td style=""></td>
                            <td style=""></td>
                            <td style=""></td>
                            <td style="font-weight: bold">Total</td>
                            <td style="font-weight: bold">Rp 10000</td>
                            <td style="font-weight: bold">Rp 10000</td>
                            <td style="font-weight: bold">Rp 10000</td>
                            <td style="font-weight: bold">Rp 10000</td>
                            <td style="font-weight: bold"></td>
                        </tr>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $adjustment->links() }} --}}
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
