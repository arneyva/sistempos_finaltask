@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Unit Measurement') }}</h1>
    <p>{{ __('Do Something with all your measurement') }}</p>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Unit') }}</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('product.unit.index') }}" method="GET">
                    <div class="input-group search-input">
                        <span class="input-group-text d-inline" id="searchIcon">
                            <svg class="icon-18" width="18" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.7669" cy="11.7666" r="8.98856" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></circle>
                                <path d="M18.0186 18.4851L21.5426 22" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>
                        <input type="search" class="form-control" placeholder="{{ __('Search...') }}."
                            value="{{ request()->input('search') }}" name="search">
                    </div>
                </form>
                <div class="heaCreate+der-title">
                    @role('superadmin|inventaris')
                        <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                            data-bs-target="#createModal">
                            {{ __('Create +') }}
                        </button>
                        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="createModalLabel">{{ __('Create Unit') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('product.unit.store') }}" method="POST">
                                            @csrf
                                            <div class="col mb-3">
                                                <label class="form-label" for="createname">{{ __('Name *') }}</label>
                                                <input type="text" class="form-control" id="createname" required
                                                    placeholder="{{ __('input unit name') }}" name="name">
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label"
                                                    for="createshortname">{{ __('Short Name *') }}</label>
                                                <input type="text" class="form-control" id="createshortname" required
                                                    placeholder="{{ __('input short name') }}" name="ShortName">
                                            </div>
                                            <div class="accordion" id="accordioncreate">
                                                <div class="accordion-item">
                                                    <h4 class="accordion-header" id="headingOne">
                                                        <button class="accordion-button" type="button"
                                                            data-bs-toggle="collapse" data-bs-target="#collapseOnecreate"
                                                            aria-expanded="true" aria-controls="collapseOne">
                                                            {{ __('Base Unit') }}
                                                        </button>
                                                    </h4>
                                                    <div id="collapseOnecreate" class="accordion-collapse collapse show"
                                                        aria-labelledby="headingOne" data-bs-parent="#accordioncreate">
                                                        <div class="accordion-body">
                                                            <label for="productunitcreate"
                                                                class="form-label">{{ __('Product Unit') }}</label>
                                                            <select class="form-select" id="productunitcreate" name="base_unit">
                                                                <option value="" selected disabled>{{ __('Choose...') }}
                                                                </option>
                                                                @foreach ($unit as $item)
                                                                    <option value="{{ $item->id }}">
                                                                        {{ $item->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="accordion-body">
                                                            <label for="operatorcreate"
                                                                class="form-label">{{ __('Operator') }}</label>
                                                            <select class="form-select" id="operatorcreate" name="operator">
                                                                <option selected disabled value="">{{ __('Choose...') }}
                                                                </option>
                                                                <option value="{{ '*' }}">{{ __('Multiply (*)') }}
                                                                </option>
                                                                <option value="{{ '/' }}">{{ __('Devide (/)') }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="accordion-body">
                                                            <label class="form-label"
                                                                for="operatorvaluecreate">{{ __('Operation value *') }}</label>
                                                            <input type="text" class="form-control"
                                                                id="operatorvaluecreate" placeholder="1"
                                                                name="operator_value">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endrole
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive mt-4">
                    <table id="basic-table" class="table table-striped mb-0" role="grid">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Short Name *') }}</th>
                                <th>{{ __('Base Unit') }}</th>
                                <th>{{ __('Operator') }}</th>
                                <th>{{ __('Operation value *') }}</th>
                                @role('superadmin|inventaris')
                                    <th>{{ __('Actions') }}</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unit as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        {{ $item->ShortName }}
                                    </td>
                                    <td>
                                        {{ $item->baseUnit->name ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $item->operator }}
                                    </td>
                                    <td>
                                        {{ $item->operator_value }}
                                    </td>
                                    @role('superadmin|inventaris')
                                        <td>
                                            <div class="inline">
                                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg" data-bs-toggle="modal"
                                                    data-bs-target="#editModal{{ $item->id }}">
                                                    <path d="M13.7476 20.4428H21.0002" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M12.78 3.79479C13.5557 2.86779 14.95 2.73186 15.8962 3.49173C15.9485 3.53296 17.6295 4.83879 17.6295 4.83879C18.669 5.46719 18.992 6.80311 18.3494 7.82259C18.3153 7.87718 8.81195 19.7645 8.81195 19.7645C8.49578 20.1589 8.01583 20.3918 7.50291 20.3973L3.86353 20.443L3.04353 16.9723C2.92866 16.4843 3.04353 15.9718 3.3597 15.5773L12.78 3.79479Z"
                                                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M11.021 6.00098L16.4732 10.1881" stroke="currentColor"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                                <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1"
                                                    aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">
                                                                    {{ __('Update Unit') }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('product.unit.update', $item->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editname{{ $item->id }}">{{ __('Name *') }}</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editname{{ $item->id }}" required
                                                                            placeholder="input unit name" name="name"
                                                                            value="{{ $item->name }}">
                                                                    </div>
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editshortname{{ $item->id }}">{{ __('Short Name *') }}</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editshortname{{ $item->id }}" required
                                                                            placeholder="input short name" name="ShortName"
                                                                            value="{{ $item->ShortName }}">
                                                                    </div>
                                                                    <div class="accordion"
                                                                        id="accordionedit{{ $item->id }}">
                                                                        <div class="accordion-item">
                                                                            <h4 class="accordion-header"
                                                                                id="headingOne{{ $item->id }}">
                                                                                <button class="accordion-button"
                                                                                    type="button" data-bs-toggle="collapse"
                                                                                    data-bs-target="#collapseOneedit{{ $item->id }}"
                                                                                    aria-expanded="true"
                                                                                    aria-controls="collapseOne">
                                                                                    {{ __('Base Unit') }}
                                                                                </button>
                                                                            </h4>
                                                                            <div id="collapseOneedit{{ $item->id }}"
                                                                                class="accordion-collapse collapse show"
                                                                                aria-labelledby="headingOne"
                                                                                data-bs-parent="#accordionedit{{ $item->id }}">
                                                                                <div class="accordion-body">
                                                                                    <label
                                                                                        for="productunitedit{{ $item->id }}"
                                                                                        class="form-label">{{ __('Product Unit') }}</label>
                                                                                    <select class="form-select"
                                                                                        id="productunitedit{{ $item->id }}"
                                                                                        name="base_unit">
                                                                                        <option
                                                                                            value="{{ $item->base_unit }}">
                                                                                            {{ $item->baseUnit->name ?? '-' }}
                                                                                        </option>
                                                                                        @foreach ($unit as $loopItem)
                                                                                            <option
                                                                                                value="{{ $loopItem->id }}">
                                                                                                {{ $loopItem->name }}</option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="accordion-body">
                                                                                    <label
                                                                                        for="operatoredit{{ $item->id }}"
                                                                                        class="form-label">{{ __('Operator') }}</label>
                                                                                    <select class="form-select"
                                                                                        id="operatoredit{{ $item->id }}"
                                                                                        name="operator">
                                                                                        <option value="{{ $item->operator }}">
                                                                                            {{ $item->operator }}
                                                                                        </option>
                                                                                        <option value="{{ '*' }}">
                                                                                            {{ __('Multiply (*)') }}</option>
                                                                                        <option value="{{ '/' }}">
                                                                                            {{ __('Devide (/)') }}</option>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="accordion-body">
                                                                                    <label class="form-label"
                                                                                        for="operatorvalueedit{{ $item->id }}">{{ __('Operation value *') }}</label>
                                                                                    <input type="text" class="form-control"
                                                                                        id="operatorvalueedit{{ $item->id }}"
                                                                                        placeholder="1" name="operator_value"
                                                                                        value="{{ $item->operator_value }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">{{ __('Save Changes') }}</button>
                                                            </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" data-bs-toggle="modal"
                                                    data-bs-target="#staticBackdrop{{ $item->id }}"
                                                    style="border: none; background: none; padding: 0; margin: 0;">
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
                                                <div class="modal fade" id="staticBackdrop{{ $item->id }}"
                                                    data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                    aria-labelledby="staticBackdropLabel{{ $item->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="staticBackdropLabel{{ $item->id }}">
                                                                    {{ $item->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            @if ($item->base_unit == null)
                                                                <div class="modal-body">
                                                                    <p>{{ __('Base Unit Cannot be deleted!') }}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                    <button type="button" class="btn btn-danger"
                                                                        disabled>{{ __('Delete') }}</button>
                                                                </div>
                                                            @else
                                                                <div class="modal-body">
                                                                    <p>{{ __('Are you sure you want to delete this data?') }}</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                    <form
                                                                        action="{{ route('product.unit.destroy', $item->id) }}"
                                                                        method="POST" style="display: inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-primary">{{ __('Delete') }}</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endrole
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $unit->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
