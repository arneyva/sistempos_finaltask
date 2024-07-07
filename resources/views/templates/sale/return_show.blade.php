@extends('templates.main')
@section('pages_title')
    <h1>{{ $sale_Return['Ref'] }} Detail</h1>
    <p>{{ __('Your hard work connects us all. Stay motivated!') }}
    </p>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-3">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row"style="box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); padding: 15px;">
                                <div class="col-md-4">
                                    <h6 class="card-title">{{ __('Customer Information') }}</h6>
                                    <p class="mb-0">{{ __('Name') }} : {{ $sale_Return['client_name'] }}</p>
                                    <p class="mb-0">{{ __('Phone') }} : {{ $sale_Return['client_phone'] }}</p>
                                    <p class="mb-0">{{ __('Email') }} : {{ $sale_Return['client_email'] }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="card-title">{{ __('Company Information') }}</h6>
                                    <p class="mb-0">{{ __('Name') }} : {{ $company['CompanyName'] }}</p>
                                    <p class="mb-0">{{ __('Email') }} :{{ $company['email'] }}</p>
                                    <p class="mb-0">{{ __('Phone') }} :{{ $company['CompanyPhone'] }}</p>
                                    <p class="mb-0">{{ __('Address') }} :{{ $company['CompanyAdress'] }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="card-title">{{ __('Invoice Information') }}</h6>
                                    <p class="mb-0">{{ __('Reference') }} : {{ $sale_Return['Ref'] }}</p>
                                    <p class="mb-0">{{ __('Payment Status') }} : {{ $sale_Return['payment_status'] }}
                                    </p>
                                    <p class="mb-0">{{ __('Statut') }} : {{ $sale_Return['statut'] }}</p>
                                    <p class="mb-0">{{ __('Warehouse/Outlet') }} : {{ $sale_Return['warehouse'] }}</p>
                                </div>
                            </div>
                            {{-- </div> --}}
                        </div>
                        <div class="table-responsive mt-4">
                            <table id="basic-table" class="table table-striped table-hover mb-0" role="grid">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>{{ __('Product Name') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th>{{ __('Tax') }}</th>
                                        <th>{{ __('SubTotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <th>Rp. {{ number_format($item['Unit_price'], 2, ',', '.') }}</th>
                                            <td>{{ $item['quantity'] }}</td>
                                            <th>Rp. {{ number_format($item['DiscountNet'], 2, ',', '.') }}</th>
                                            <th>Rp. {{ number_format($item['taxe'], 2, ',', '.') }}</th>
                                            <th>Rp. {{ number_format($item['total'], 2, ',', '.') }}</th>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 mt-4">
                            <table id="basic-table" class="table table-hover table-bordered table-sm" role="grid">
                                <tbody>
                                    <tr>
                                        <td>{{ __('Order Tax') }}</td>
                                        <th>Rp. {{ number_format($sale_Return['TaxNet'], 2, ',', '.') }}
                                            ({{ $sale_Return['tax_rate'] }}%)
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Discount') }}</td>
                                        <th>Rp. {{ number_format($sale_Return['discount'], 2, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Shipping') }}</td>
                                        <th>Rp. {{ number_format($sale_Return['shipping'], 2, ',', '.') }}</th>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Grand Total') }}</td>
                                        <th>Rp. {{ number_format($sale_Return['GrandTotal'], 2, ',', '.') }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6 mt-4">
                            <p class="mb-0">{{ __('Note') }} : {{ $sale_Return['note'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
