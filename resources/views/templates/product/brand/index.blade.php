@extends('templates.main')

@section('pages_title')
    <h1>{{ __('All Product Brand') }}</h1>
    <p>{{ __('Do Something with all your product brands') }}</p>
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="mt-3" style="justify-content-center">
            <!-- @include('templates.alert') -->
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title">{{ __('All Brand') }}</h4>
                </div>
            </div>
            <div class="card-header d-flex justify-content-between">
                <form action="{{ route('product.brand.index') }}" method="GET">
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
                            placeholder="{{ __('Search...') }}">
                    </div>
                </form>
                <div class="header-title">
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
                                        <h5 class="modal-title" id="createModalLabel">{{ __('Create') }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('product.brand.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="col mb-3">
                                                <label class="form-label" for="name">{{ __('Brand Name*') }}</label>
                                                <input type="text" class="form-control" id="name" required
                                                    placeholder="{{ __('input brands') }}" name="name">
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label" for="description">{{ __('Description*') }}</label>
                                                <input type="text" class="form-control" id="description" required
                                                    placeholder="{{ __('input description') }}" name="description">
                                            </div>
                                            <div class="col mb-3">
                                                <label class="form-label" for="image">{{ __('Image') }}</label>
                                                <input type="file" class="form-control" id="image" name="image">
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                        <button type="submit" class="btn btn-primary">{{ __('Save changes') }}</button>
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
                                <th>{{ __('Brand Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                @role('superadmin|inventaris')
                                    <th>{{ __('Actions') }}</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($brands as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img class="rounded img-fluid avatar-40 me-3 bg-soft-primary"
                                                src="{{ asset('hopeui/html/assets/images/brands/sample-brands.png') }}"
                                                alt="profile">
                                            <h6>{{ $item->name }}</h6>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $item->description }}
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
                                                    aria-labelledby="exampleModalLabel{{ $item->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="exampleModalLabel{{ $item->id }}">{{ __('Update Brand') }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ route('product.brand.update', $item->id) }}"
                                                                    method="POST" enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editname{{ $item->id }}">{{ __('Brand Name*') }}</label>
                                                                        <input type="text" class="form-control"
                                                                            id="editname{{ $item->id }}" required
                                                                            placeholder="{{ __('input brands') }}" name="name"
                                                                            value="{{ $item->name }}">
                                                                    </div>
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editdescription{{ $item->id }}">{{ __('Description*') }}
                                                                            </label>
                                                                        <input type="text" class="form-control"
                                                                            id="editdescription{{ $item->id }}" required
                                                                            placeholder="{{ __('input description') }}" name="description"
                                                                            value="{{ $item->description }}">
                                                                    </div>
                                                                    <div class="col mb-3">
                                                                        <label class="form-label"
                                                                            for="editimage{{ $item->id }}">{{ __('Image') }}</label>
                                                                        <input type="file" class="form-control"
                                                                            id="editimage{{ $item->id }}" name="image">
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
                                                {{-- button delete --}}
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
                                                                <p>{{ __('Are you sure you want to delete this data?') }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">{{ __('Close') }}</button>
                                                                <form action="{{ route('product.brand.destroy', $item->id) }}"
                                                                    method="POST" style="display: inline;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-primary">{{ __('Delete') }}</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    @endrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="bd-example" style="margin-left: 10px; margin-right: 10px; margin-top:10px">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
