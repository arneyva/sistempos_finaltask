@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Purchases') }} ~ {{ __('Reports') }}</h1>
    <p>{{ __('look up your daily reports') }}</p>
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
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('Purchases') }} {{ __('Reports') }}</h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">Filter</button>
                    <a href="{{ route('sale.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a>
                    </button></a>
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModalLabel">{{ __('Filter') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('reports.purchase') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="from_date">{{ __('From Date') }}</label>
                                            <input type="date" class="form-control" id="from_date" name="from_date"
                                                value="{{ request()->input('from_date') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="to_date">{{ __('To Date') }}</label>
                                            <input type="date" class="form-control" id="to_date" name="to_date"
                                                value="{{ request()->input('to_date') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="search">{{ __('Search') }}</label>
                                            <input type="text" class="form-control" id="search" name="search"
                                                value="{{ request()->input('search') }}"
                                                placeholder="{{ __('Search...') }}">
                                        </div>
                                        @role('superadmin|inventaris')
                                            <div class="col mb-3">
                                                <label class="form-label" for="warehouse_id">{{ __('Warehouse') }}
                                                </label>
                                                <select class="form-select" id="warehouse_id" name="warehouse_id">
                                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                                    @foreach ($warehouse as $wh)
                                                        <option value="{{ $wh->id }}"
                                                            {{ request()->input('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                            {{ $wh->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endrole
                                        <div class="col mb-3">
                                            <label class="form-label" for="provider_id">{{ __('Supplier') }}
                                            </label>
                                            <select class="form-select" id="provider_id" name="provider_id">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                @foreach ($provider as $wh)
                                                    <option value="{{ $wh->id }}"
                                                        {{ request()->input('provider_id') == $wh->id ? 'selected' : '' }}>
                                                        {{ $wh->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="statut">{{ __('Status') }}</label>
                                            <select class="form-select" id="statut" name="statut">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="completed"
                                                    {{ request()->input('statut') == 'completed' ? 'selected' : '' }}>
                                                    {{ __('Completed') }}</option>
                                                <option value="pending"
                                                    {{ request()->input('statut') == 'pending' ? 'selected' : '' }}>
                                                    {{ __('Pending') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label"
                                                for="payment_statut">{{ __('Payment Status') }}</label>
                                            <select class="form-select" id="payment_statut" name="payment_statut">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="paid"
                                                    {{ request()->input('payment_statut') == 'paid' ? 'selected' : '' }}>
                                                    {{ __('Paid') }}</option>
                                                <option value="unpaid"
                                                    {{ request()->input('payment_statut') == 'unpaid' ? 'selected' : '' }}>
                                                    {{ __('Unpaid') }}
                                                </option>
                                            </select>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="resetFilters()"
                                        data-bs-dismiss="modal">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Reference') }}</th>
                                <th>{{ __('Supplier') }}</th>
                                <th>{{ __('Warehouse') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Grand Total') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Due') }}</th>
                                <th>{{ __('Payment') }} {{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases_data as $item)
                                <tr>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['Ref'] }}</td>
                                    <td>{{ $item['provider_name'] }}</td>
                                    <td>{{ $item['warehouse_name'] }}</td>
                                    <td>
                                        @if ($item['statut'] == 'completed')
                                            <span class="status-completed">{{ __('Completed') }}</span>
                                        @elseif($item['statut'] == 'ordered')
                                            <span class="status-ordered">{{ __('Ordered') }}</span>
                                        @else
                                            <span class="status-pending">{{ __('Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['paid_amount'], 2, ',', '.') }} </td>
                                    <td>{{ 'Rp ' . number_format($item['due'], 2, ',', '.') }}</td>
                                    <td>
                                        @if ($item['payment_status'] == 'paid')
                                            <span class="payment-paid">{{ __('Paid') }}</span>
                                        @else
                                            <span class="payment-unpaid">{{ __('Unpaid') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #f9f9f9;">
                                <td></td>
                                <td style="font-weight: bold">Total</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_amount, 2, ',', '.') }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_paid, 2, ',', '.') }}</td>
                                <td style="font-weight: bold">{{ 'Rp ' . number_format($total_due, 2, ',', '.') }}</td>
                                </td>
                                <td style="font-weight: bold"></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $purchases->links() }}
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
