@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Warehouse/Outlet') }}</h1>
    <p>{{ __('Look All your Warehouse/Outlet') }}</p>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Warehouse/Outlet') }}</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('settings.warehouses.index') }}" method="GET">
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
                        <input type="search" class="form-control" name="search" value="{{ request()->input('search') }}"
                            placeholder="{{ __('Search...') }}">
                    </div>
                </form>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-danger">Excel</button>
                    <button type="button" class="btn btn-soft-primary" data-bs-toggle="modal"
                        data-bs-target="#createModal">
                        {{ __('Create +') }}
                    </button>
                    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="createModalLabel">{{ __('Create') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('settings.warehouses.store') }}" method="POST">
                                        @csrf
                                        <div class="col mb-3">
                                            <label class="form-label" for="name">{{ __('Name') }} *</label>
                                            <input type="text" class="form-control" id="name" required
                                                name="name" placeholder="{{ __('Input') }} {{ __('Name') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="city">{{ __('City') }} *</label>
                                            <input type="text" class="form-control" id="city" required
                                                name="city" placeholder="{{ __('Input') }} {{ __('City') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="phone">{{ __('Phone') }} *</label>
                                            <input type="text" class="form-control" id="phone" required
                                                name="mobile" placeholder="{{ __('Input') }} {{ __('Phone') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="postcode">{{ __('Zip Postcode') }} *</label>
                                            <input type="text" class="form-control" id="postcode" required
                                                name="zip" placeholder="{{ __('Input') }} {{ __('Zip Postcode') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="email">{{ __('Email') }} *</label>
                                            <input type="email" class="form-control" id="email" required
                                                placeholder="{{ __('Input') }} {{ __('Email') }}" name="email">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="country">{{ __('Country') }} *</label>
                                            <input type="text" class="form-control" id="country" required
                                                name="country" placeholder="{{ __('Input') }} {{ __('Country') }}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="address">{{ __('Address') }} *</label>
                                            <textarea class="form-control" id="address" required name="address"
                                                placeholder="{{ __('Input') }} {{ __('Address') }}"></textarea>
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label" for="google_maps">Google Maps Link *</label>
                                            <input type="url" class="form-control mb-1" id="google_maps" required
                                                name="google_maps" placeholder="input Link">
                                            <p class="mx-3">
                                                {{ __('Use link from share feature in Google Maps') }}<br>{{ __('Link Example') }}:<br><a
                                                    href="#">https://maps.app.goo.gl/Bwz1W2ULp1xx3wWx5</a>
                                            </p>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
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
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('City') }}</th>
                                <th>{{ __('Phone') }}</th>
                                <th>{{ __('Zip Postcode') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($warehouses as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        {{ $item->city }}
                                    </td>
                                    <td>
                                        {{ $item->mobile }}
                                    </td>
                                    <td>
                                        {{ $item->zip }}
                                    </td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td>
                                        {{ $item->country }}
                                    </td>
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
                                                                {{ __('Update') }} {{ __('Warehouse/Outlet') }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form
                                                                action="{{ route('settings.warehouses.update', $item->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="editname{{ $item->id }}">{{ __('Name') }}
                                                                        *</label>
                                                                    <input type="text" class="form-control"
                                                                        id="editname{{ $item->id }}" required
                                                                        placeholder="input unit name" name="name"
                                                                        value="{{ $item->name }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="city{{ $item->id }}">{{ __('City') }}
                                                                        *</label>
                                                                    <input type="text" class="form-control"
                                                                        id="city{{ $item->id }}" required
                                                                        name="city" placeholder="input city"
                                                                        value="{{ $item->city }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="phone{{ $item->id }}">{{ __('Phone') }}
                                                                        *</label>
                                                                    <input type="text" class="form-control"
                                                                        id="phone{{ $item->id }}" required
                                                                        name="mobile" placeholder="input mobile phone"
                                                                        value="{{ $item->mobile }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="postcode{{ $item->id }}">{{ __('Zip Postcode') }}
                                                                        *</label>
                                                                    <input type="text" class="form-control"
                                                                        id="postcode{{ $item->id }}" required
                                                                        name="zip" placeholder="input postcode"
                                                                        value="{{ $item->zip }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="email{{ $item->id }}">{{ __('Email') }}
                                                                        *</label>
                                                                    <input type="email" class="form-control"
                                                                        id="email{{ $item->id }}" required
                                                                        placeholder="input email" name="email"
                                                                        value="{{ $item->email }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="country{{ $item->id }}">{{ __('Country') }}
                                                                        *</label>
                                                                    <input type="text" class="form-control"
                                                                        id="country{{ $item->id }}" required
                                                                        name="country" placeholder="input country"
                                                                        value="{{ $item->country }}">
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="address{{ $item->id }}">{{ __('Address') }}
                                                                        *</label>
                                                                    <textarea class="form-control" id="address{{ $item->id }}" required name="address" placeholder="input address">{{ $item->address }}</textarea>
                                                                </div>
                                                                <div class="col mb-3">
                                                                    <label class="form-label"
                                                                        for="google_maps{{ $item->id }}">Google Maps
                                                                        Link *</label>
                                                                    <input type="url" class="form-control mb-1"
                                                                        id="google_maps{{ $item->id }}" required
                                                                        name="google_maps" placeholder="input Link"
                                                                        value="{{ $item->google_maps }}">
                                                                    <p class="mx-3">
                                                                        {{ __('Use link from share feature in Google Maps') }}<br>{{ __('Link Example') }}:<br><a
                                                                            href="#">https://maps.app.goo.gl/Bwz1W2ULp1xx3wWx5</a>
                                                                    </p>
                                                                </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                            <button type="submit"
                                                                class="btn btn-primary">{{ __('Save changes') }}</button>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $item->id }}"
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
                                            {{-- modal delete --}}
                                            <div class="modal fade" id="deleteModal{{ $item->id }}"
                                                data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                                                aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="deleteModalLabel{{ $item->id }}">
                                                                {{ $item->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>"Are you sure you want to delete this data?"</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <form
                                                                action="{{ route('settings.warehouses.destroy', $item->id) }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-primary">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
