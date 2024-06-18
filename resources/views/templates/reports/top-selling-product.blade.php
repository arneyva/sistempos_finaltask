@extends('templates.main')

@section('pages_title')
    <h1>Top Selling Products ~ Reports</h1>
    <p>look up your daily report</p>
@endsection

<style>
    .warehousedeleted {
        padding: 7px;
        border-radius: 7px;
        background-color: #ffefef;
        color: #F24D4D;
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
                    <h4 class="card-title">Top Selling Products
                    </h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('reports.top-selling-product') }}" method="GET">
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
                <div class="header-title">
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Total Sales</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr>
                                    <td>{{ $item['code'] }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['total_sales'] }}</td>
                                    <td>{{ 'Rp ' . number_format($item['total'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{-- {{ $brands->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
