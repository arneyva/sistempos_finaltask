<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Purchase Order</title>
    <style>
        @media (max-width: 400px) and (min-width: 300px) {
            .container {
                width: 80vw !important;
                max-width: 80vw !important;
                min-width: 80vw !important;
            }

            .button {
                padding: 3.6vw 4.8vw !important;
                font-size: 2.5vw !important;
            }
            .pin {
                font-size: 2.5vw !important;
            }
        }
        @media (max-width: 1420px) and (min-width: 1000px) {
            .container {
                width: 60vw !important;
                max-width: 60vw !important;
                min-width: 60vw !important;
            }

            .header {
                margin-bottom: 4vw !important;
            }

            .button {
                padding: 1.8vw 2.4vw !important;
                font-size: 1.3vw !important;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80vw;
            max-width: 80vw;
            min-width: 80vw;
            background-color: white;
            margin: 0 auto;
            padding: 2vw;
            box-shadow: 0 0 1vw rgba(0, 0, 0, 0.1);
        }
        .header {
            display:flex;
            justify-content:left;
            align-items:center;
            margin-bottom: 6vw;
        }
        .header img {
            height: 6vw;
        }
        .header .company-name {
            font-size: 3.6vw;
            font-weight: bold;
            float: left;
            margin-left:1.8vw;
        }
        .content {
            margin-bottom: 2vw;
            font-size: 1.3vw !important;
        }
        .button-container {
            text-align: center;
            margin: 2vw 0;
        }
        .button {
            background-color: #dbdbdb;
            color: white;
            padding: 2vw 3vw;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 1.2vw;
            margin: 1.5vw 1vw 1vw 1vw;
            cursor: pointer;
            border-radius: 0.8vw;
        }
        .pin {
            text-align: center;
            font-size: 1.5vw;
            margin-bottom: 1.4vw;
        }
        .signature {
            text-align: right;
            font-size:1.3vw !important;
            margin-top: 2vw;
        }
        pre {
            font-family: Arial, sans-serif;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png" alt="Company Logo">
            <div class="company-name">{{ $company_name }}</div>
        </div>
        <div class="content">
            <p style="margin-bottom: 0.15vw;">Dear {{ $supplier_name }},</p>
            <p style="margin-top: 0.15vw;">{{ $supplier_adresse }}</p>
            <p>{{ $date }}</p>
            @if ($status === 'pending')
                <pre>Our company initiate an agreement to purchase order with the following information

    buyer's name    : {{ $company_name }}
    vendor name     : {{ $supplier_name }}
    purchase code   : {{ $purchase_ref }}

To view details and confirm the order, click the following button
</pre>
            @elseif ($status === 'ordered')
                <pre>Our company initiate an agreement to purchase order with the following information

    buyer's name    : {{ $company_name }}
    vendor name     : {{ $supplier_name }}
    purchase code   : {{ $purchase_ref }}
To view details and confirm the order, click the following button
</pre>
            @elseif ($status === 'shipped')
                <pre>Our company initiate an agreement to purchase order with the following information

    buyer's name    : {{ $company_name }}
    vendor name     : {{ $supplier_name }}
    purchase code   : {{ $purchase_ref }}
To view details and confirm the order, click the following button
</pre>
            @elseif ($status === 'arrived')
                <pre>Our company initiate an agreement to purchase order with the following information

    buyer's name    : {{ $company_name }}
    vendor name     : {{ $supplier_name }}
    purchase code   : {{ $purchase_ref }}
To view details and confirm the order, click the following button
</pre>
            @elseif ($status === 'complete')
                <pre>Our company initiate an agreement to purchase order with the following information

    buyer's name    : {{ $company_name }}
    vendor name     : {{ $supplier_name }}
    purchase code   : {{ $purchase_ref }}
To view details and confirm the order, click the following button
</pre>
            @endif
        </div>
        <div class="button-container">
            <a href="{{ $url }}" class="button">Click Here</a>
        </div>
        <div class="signature">
            <p>Best Regards,</p>
            <br>
            <p>{{ $company_name }}</p>
        </div>
    </div>
</body>
</html>
