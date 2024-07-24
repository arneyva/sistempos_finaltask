<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>{{ $company['CompanyName'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('invoice/main/assets/css/bootstrap.min.css') }}">
    <link type="text/css" rel="stylesheet" href="assets/fonts/font-awesome/css/font-awesome.min.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="{{ asset('invoice/main/assets/img/favicon.ico') }}" type="image/x-icon">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('invoice/main/assets/css/style.css') }}">
</head>

<body>

    <!-- Invoice 2 start -->
    <div class="invoice-2 invoice-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="invoice-inner clearfix">
                        <div class="invoice-info clearfix" id="invoice_wrapper">
                            <div class="invoice-headar">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="invoice-logo">
                                            <!-- logo started -->
                                            <div class="logo">
                                                <img src="{{ asset('hopeui/html/assets/images/avatars/logo-default.png') }}" alt="logo">
                                            </div>
                                            <!-- logo ended -->
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="invoice-id">
                                            <div class="info">
                                                <h1 class="inv-header-1">Invoice</h1>
                                                <p class="mb-1">Invoice Number: <span>{{ $sale['Ref'] }}</span></p>
                                                <p class="mb-0">Invoice Date: <span>{{ $sale['date'] }}</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-top">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="invoice-number mb-30">
                                            <h4 class="inv-title-1">Invoice To</h4>
                                            <h2 class="name">{{ $sale['client_name'] }}</h2>
                                            <p class="invo-addr-1">
                                                {{ $sale['client_phone'] }} <br />
                                                {{ $sale['client_email'] }} <br />
                                                {{-- 21-12 Green Street, Meherpur, Bangladesh <br /> --}}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="invoice-number mb-30">
                                            <div class="invoice-number-inner">
                                                <h4 class="inv-title-1">Invoice From</h4>
                                                <h2 class="name">{{ $company['CompanyName'] }}</h2>
                                                <p class="invo-addr-1">
                                                    {{ $company['CompanyPhone'] }} <br />
                                                    {{ $company['email'] }}<br />
                                                    {{ $company['CompanyAdress'] }} <br />
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-center">
                                <div class="table-responsive">
                                    <table class="table mb-0 table-striped invoice-table">
                                        <thead class="bg-active">
                                            <tr class="tr">
                                                <th>No.</th>
                                                <th class="pl0 text-start">{{ __('Product Name') }}</th>
                                                <th class="text-center">{{ __('Price') }}</th>
                                                <th class="text-center">{{ __('Quantity') }}</th>
                                                <th class="text-center">{{ __('Discount') }}</th>
                                                <th class="text-center">{{ __('Tax') }}</th>
                                                <th class="text-end">{{ __('SubTotal') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($details as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item['name'] }}</td>
                                                    <th>Rp. {{ number_format($item['Unit_price'], 2, ',', '.') }}</th>
                                                    <td>{{ $item['quantity'] }} {{ $item['unit_sale'] }}</td>
                                                    <th>Rp. {{ number_format($item['DiscountNet'], 2, ',', '.') }}</th>
                                                    <th>Rp. {{ number_format($item['taxe_total'], 2, ',', '.') }}</th>
                                                    <th>Rp. {{ number_format($item['total'], 2, ',', '.') }}</th>
                                                </tr>
                                            @endforeach
                                            <tr class="tr2">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">Order Tax</td>
                                                <td class="text-end">Rp.
                                                    {{ number_format($sale['TaxNet'], 2, ',', '.') }}
                                                    ({{ $sale['tax_rate'] }}%)</td>
                                            </tr>
                                            <tr class="tr2">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">Dsicount</td>
                                                <td class="text-end">Rp.
                                                    {{ number_format($sale['discount'], 2, ',', '.') }}</td>
                                            </tr>
                                            <tr class="tr2">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center">Shipping</td>
                                                <td class="text-end">Rp.
                                                    {{ number_format($sale['shipping'], 2, ',', '.') }}</td>
                                            </tr>
                                            <tr class="tr2">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-center f-w-600 active-color">Grand Total</td>
                                                <td class="f-w-600 text-end active-color">Rp.
                                                    {{ number_format($sale['GrandTotal'], 2, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="invoice-bottom">
                                <div class="row">
                                    <div class="col-lg-6 col-md-5 col-sm-5">
                                        <div class="payment-method mb-30">
                                            <h3 class="inv-title-1">Payment</h3>
                                            <ul class="payment-method-list-1 text-14">
                                                <li><strong>Reference: </strong>{{ $sale['PaymentRef'] }}</li>
                                                <li><strong>Status:</strong> {{ $sale['PaymentStatus'] }}</li>
                                                <li><strong>Status:</strong> {{ $sale['PaymentBayar'] }}</li>
                                                <li><strong>Charge:</strong> {{ $sale['PaymentCharge']  }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-7 col-sm-7">
                                        <div class="terms-conditions mb-30">
                                            <h3 class="inv-title-1">Notes</h3>
                                            <p>{{ $sale['note'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="invoice-contact clearfix">
                                <div class="row g-0">
                                    <div class="col-sm-12">
                                        {{-- <div class="contact-info clearfix">
                                            <a href="tel:+55-4XX-634-7071" class="d-flex"><i class="fa fa-phone"></i>
                                                +00 123 647 840</a>
                                            <a href="tel:info@themevessel.com" class="d-flex"><i
                                                    class="fa fa-envelope"></i> info@themevessel.com</a>
                                            <a href="tel:info@themevessel.com" class="mr-0 d-flex d-none-580"><i
                                                    class="fa fa-map-marker"></i> 169 Teroghoria, Bangladesh</a>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="invoice-btn-section clearfix d-print-none">
                            <a href="javascript:window.print()" class="btn btn-lg btn-print">
                                <i class="fa fa-print"></i> Print Invoice
                            </a>
                            <a id="invoice_download_btn" class="btn btn-lg btn-download btn-theme">
                                <i class="fa fa-download"></i> Download Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice 2 end -->

    <script src="{{ asset('invoice/main/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('invoice/main/assets/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('invoice/main/assets/js/html2canvas.js') }}"></script>
    <script src="{{ asset('invoice/main/assets/js/app.js') }}"></script>
</body>

</html>
