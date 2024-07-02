8991609119892
@extends('templates.main')

@section('pages_title')
    <h1>Reports</h1>
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
        <div class="card">
            <h4 class="card-title" style="align-self:center;margin-top:20px;">Payments Report
            </h4>
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Purchase
                    </h5>
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
                <input class="input-group-text d-inline" style="border-color:#b19785" type="text" name="daterange"
                    value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
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
                                <th>Purchase</th>
                                <th>Supplier</th>
                                <th>Paid by</th>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: #dfd5ce">Total</td>
                            <td style="background-color: #dfd5ce">Rp 10000</td>
                        </tr>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $sales->links() }} --}}
                    </div>
                </div>
            </div>
            {{--  --}}
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Purchase Return
                    </h5>
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
                <input class="input-group-text d-inline" style="border-color:#b19785" type="text" name="daterange"
                    value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
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
                                <th>Return</th>
                                <th>Supplier</th>
                                <th>Paid by</th>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: #dfd5ce">Total</td>
                            <td style="background-color: #dfd5ce">Rp 10000</td>
                        </tr>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $sales->links() }} --}}
                    </div>
                </div>
            </div>
            {{--  --}}
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Sale
                    </h5>
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
                <input class="input-group-text d-inline" style="border-color:#b19785" type="text" name="daterange"
                    value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
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
                                <th>Sale</th>
                                <th>Customer</th>
                                <th>Paid by</th>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td>Rp 10000</td>
                        </tr>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $sales->links() }} --}}
                    </div>
                </div>
            </div>
            {{--  --}}
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h5 class="card-title">Sale Return
                    </h5>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <div class="input-group search-input" style="width: 30%">
                    <span class="input-group-text d-inline" id="search-input">
                        <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11.7669" cy="11.7666" r=" 8.98856" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                            </circle>
                            <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
                    <input type="search" class="form-control" placeholder="Search...">
                </div>
                <input class="input-group-text d-inline" style="border-color:#b19785" type="text" name="daterange"
                    value='{{ date('m/d/Y') }} - {{ date('m/d/Y') }}' />
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary">Filter</button>
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
                                <th>Return</th>
                                <th>Supplier</th>
                                <th>Paid by</th>
                                <th>Account</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                            <tr>
                                <td>12-01-2024</td>
                                <td>70171027</td>
                                <td>1</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Budi</td>
                                <td>Rp. 50000</td>
                            </tr>
                        </tbody>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="background-color: #dfd5ce">Total</td>
                            <td style="background-color: #dfd5ce">Rp 10000</td>
                        </tr>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{-- {{ $sales->links() }} --}}
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
