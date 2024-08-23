@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Transfers') }}</h1>
    <p>{{ __('Look All your transfer') }}</p>
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
                    <h4 class="card-title">{{ __('All Transfers') }}
                    </h4>
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-warning" data-bs-toggle="modal"
                        data-bs-target="#createModal">{{ __('Filter') }}</button>
                    @role('superadmin|inventaris')
                        <a href="{{ route('transfer.pdf', request()->query()) }}" class="btn btn-soft-success">PDF</a>
                        <a href="{{ route('transfer.export', request()->query()) }}" class="btn btn-soft-danger">Excel</a>
                        <a href="{{ route('transfer.create') }}"><button type="button"
                                class="btn btn-soft-primary">{{ __('Create +') }}</button></a>
                    @endrole
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
                                    <form action="{{ route('transfer.index') }}" method="GET" id="filterForm">
                                        <div class="col mb-3">
                                            <label class="form-label" for="date">{{ __('Date *') }}</label>
                                            <input type="date" class="form-control" id="date" name="date"
                                                value="{{ request()->input('date') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="Ref">{{ __('Reference *') }}</label>
                                            <input type="text" class="form-control" id="Ref" name="Ref"
                                                value="{{ request()->input('Ref') }}"
                                                placeholder="{{ __('Input Ref ...') }}">
                                        </div>
                                        @role('superadmin|inventaris')
                                            <div class="col mb-3">
                                                <label class="form-label"
                                                    for="from_warehouse_id">{{ __('From Warehouse/Outlet') }}
                                                    *</label>
                                                <select class="form-select" id="from_warehouse_id" name="from_warehouse_id">
                                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                                    @foreach ($warehouse as $wh)
                                                        <option value="{{ $wh->id }}"
                                                            {{ request()->input('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                            {{ $wh->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label"
                                                    for="to_warehouse_id">{{ __('To Warehouse/Outlet') }}</label>
                                                <select class="form-select" id="to_warehouse_id" name="to_warehouse_id">
                                                    <option selected disabled value="">{{ __('Choose...') }}</option>
                                                    @foreach ($warehouse as $wh)
                                                        <option value="{{ $wh->id }}"
                                                            {{ request()->input('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                                            {{ $wh->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endrole
                                        <div class="col mb-3">
                                            <label class="form-label" for="statut">{{ __('Status') }}</label>
                                            <select class="form-select" id="statut" name="statut">
                                                <option selected disabled value="">{{ __('Choose...') }}</option>
                                                <option value="completed"
                                                    {{ request()->input('statut') == 'completed' ? 'selected' : '' }}>
                                                    {{ __('Completed') }}</option>
                                                <option value="sent"
                                                    {{ request()->input('statut') == 'sent' ? 'selected' : '' }}>
                                                    {{ __('Sent') }}
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
                                <th>{{ __('From Warehouse/Outlet') }}</th>
                                <th>{{ __('To Warehouse/Outlet') }}</th>
                                <th>{{ __('Total Products') }}</th>
                                <th>{{ __('Grand Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transfer as $item)
                                <tr>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['Ref'] }}</td>
                                    <td>{{ $item['from_warehouse']['name'] }}</td>
                                    <td>{{ $item['to_warehouse']['name'] }}</td>
                                    <td>{{ $item['items'] }} {{ __('Items') }}</td>
                                    <td>{{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}</td>
                                    <td>{{ $item['statut'] }}</td>
                                    <td>
                                        <div class="inline">
                                            <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg" data-bs-toggle="modal"
                                                data-bs-target="#detailModal{{ $item['id'] }}">
                                                <path
                                                    d="M22.4541 11.3918C22.7819 11.7385 22.7819 12.2615 22.4541 12.6082C21.0124 14.1335 16.8768 18 12 18C7.12317 18 2.98759 14.1335 1.54586 12.6082C1.21811 12.2615 1.21811 11.7385 1.54586 11.3918C2.98759 9.86647 7.12317 6 12 6C16.8768 6 21.0124 9.86647 22.4541 11.3918Z"
                                                    stroke="#130F26"></path>
                                                <circle cx="12" cy="12" r="5" stroke="#130F26"></circle>
                                                <circle cx="12" cy="12" r="3" fill="#130F26"></circle>
                                                <mask mask-type="alpha" maskUnits="userSpaceOnUse" x="9" y="9"
                                                    width="6" height="6">
                                                    <circle cx="12" cy="12" r="3" fill="#130F26"></circle>
                                                </mask>
                                                <circle opacity="0.89" cx="13.5" cy="10.5" r="1.5"
                                                    fill="white"></circle>
                                            </svg>
                                            <div class="modal fade" id="detailModal{{ $item['id'] }}" tabindex="-1"
                                                role="dialog" aria-labelledby="showDetailsLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="showDetailsLabel">
                                                                {{ __('Transfer Detail') }}
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-lg-5 col-md-12 col-sm-12 mt-3">
                                                                    <table
                                                                        class="table table-hover table-bordered table-sm">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>{{ __('Date') }}</td>
                                                                                <th>{{ $item['date'] }}</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('Reference') }}</td>
                                                                                <th>{{ $item['Ref'] }}</th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('From Warehouse/Outlet') }}</td>
                                                                                <th>{{ $item['from_warehouse']['name'] }}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('To Warehouse/Outlet') }}</td>
                                                                                <th>{{ $item['to_warehouse']['name'] }}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('Grand Total') }}</td>
                                                                                <th>{{ 'Rp ' . number_format($item['GrandTotal'], 2, ',', '.') }}
                                                                                </th>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ __('Status') }}</td>
                                                                                <th>{{ $item['statut'] }}
                                                                                </th>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-lg-7 col-md-12 col-sm-12 mt-3">
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class="table table-hover table-bordered table-sm">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">
                                                                                        {{ __('ProductName') }}</th>
                                                                                    <th scope="col">
                                                                                        {{ __('CodeProduct') }}</th>
                                                                                    <th scope="col">
                                                                                        {{ __('Quantity') }}</th>
                                                                                    <th scope="col">{{ __('Cost') }}
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($item->details as $detail)
                                                                                    <tr>
                                                                                        <td>{{ $detail->product->name }}
                                                                                        </td>
                                                                                        <td>{{ $detail->product->code }}
                                                                                        </td>
                                                                                        <td>{{ $detail->quantity }}</td>
                                                                                        <td>{{ 'Rp ' . number_format($detail->cost, 2, ',', '.') }}
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @role('superadmin|inventaris')
                                                {{-- <a href="{{ $item['statut'] !== 'completed' ? route('transfer.edit', $item['id']) : '#' }}"
                                                    class="{{ $item['statut'] === 'completed' ? 'disabled' : '' }}">
                                                    <svg class="icon-32 {{ $item['statut'] === 'completed' ? 'text-danger' : '' }}"
                                                        width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </a>
                                                @if ($item['statut'] !== 'completed')
                                                @endif --}}
                                                <a href="{{ route('transfer.edit', $item['id']) }}">
                                                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#modaldeleteTransfer{{ $item['id'] }}"
                                                    style="border: none; background: none; padding: 0; margin: 0;color:red">
                                                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M14.737 2.76196H7.979C5.919 2.76196 4.25 4.43196 4.25 6.49096V17.34C4.262 19.439 5.973 21.13 8.072 21.117C8.112 21.117 8.151 21.116 8.19 21.115H16.073C18.141 21.094 19.806 19.409 19.802 17.34V8.03996L14.737 2.76196Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path
                                                            d="M14.4736 2.75024V5.65924C14.4736 7.07924 15.6216 8.23024 17.0416 8.23424H19.7966"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M13.5759 14.6481L10.1099 11.1821" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path d="M10.1108 14.6481L13.5768 11.1821" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <div class="modal fade" id="modaldeleteTransfer{{ $item['id'] }}"
                                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="staticBackdropLabelTransfer{{ $item['id'] }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="staticBackdropLabelTransfer{{ $item['id'] }}">
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>{{ __('Are you sure you want to delete this data?') }}
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                <form action="{{ route('transfer.destroy', $item['id']) }}"
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-danger">{{ __('Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endrole
                                            @role('staff')
                                                <a href="{{ $item['statut'] !== 'completed' ? route('transfer.edit-for-staff', $item['id']) : '#' }}"
                                                    class="{{ $item['statut'] === 'completed' ? 'disabled' : '' }}">
                                                    <svg class="icon-32 {{ $item['statut'] === 'completed' ? 'text-danger' : '' }}"
                                                        width="32" viewBox="0 0 24 24" fill="none"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round"></path>
                                                        <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg>
                                                </a>
                                            @endrole
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $transfer->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function resetFilters() {
            // Reset nilai-nilai input dari formulir jika elemen ada
            if (document.getElementById('date')) {
                document.getElementById('date').value = '';
            }
            if (document.getElementById('Ref')) {
                document.getElementById('Ref').value = '';
            }
            if (document.getElementById('statut')) {
                document.getElementById('statut').value = '';
            }
            if (document.getElementById('from_warehouse_id')) {
                document.getElementById('from_warehouse_id').value = '';
            }
            if (document.getElementById('to_warehouse_id')) {
                document.getElementById('to_warehouse_id').value = '';
            }

            // Submit formulir secara otomatis untuk menghapus filter
            document.getElementById('filterForm').submit();
        }
    </script>
@endpush
