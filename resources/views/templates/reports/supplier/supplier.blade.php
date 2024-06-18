@extends('templates.main')

@section('pages_title')
    <h1>SUppliers ~ Reports</h1>
    <p>look up your daily report</p>
@endsection

<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #eff8ff;
        color: #377b9d;
    }

    .pdfstyle {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffeff1;
        color: #9d3798;
    }
</style>
@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">Supplier Report</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('reports.supplier.index') }}" method="GET">
                    <div class="input-group search-input">
                        <span class="input-group-text d-inline" id="search-input">
                            <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></circle>
                                <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                        <input type="search" class="form-control" name="search" value="{{ request()->input('search') }}"
                            placeholder="Search...">
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>Phone</th>
                                <th>Purchases</th>
                                <th>Total Amount</th>
                                <th>Paid</th>
                                <th>Total Purchase Due</th>
                                <th>Total Purchase Return Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($report as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['phone'] }}</td>
                                    <td>{{ $item['total_purchase'] }}</td>
                                    <td>{{ 'Rp ' . number_format($item['total_amount'], 2, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['total_paid'], 2, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['due'], 2, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['return_Due'], 2, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('reports.supplier.purchases', $item['id']) }}"
                                            class="warehousedeleted" style="margin-right: 10px">Report
                                        </a>
                                        <a href="#" class="pdfstyle">Pdf
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td style="font-weight: bold">Total</td>
                                <td>{{ $total_purchase }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_amount, 2, ',', '.') }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_paid, 2, ',', '.') }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_due, 2, ',', '.') }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_return_due, 2, ',', '.') }}
                                </td>
                                <td style="font-weight: bold"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{ $providers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
