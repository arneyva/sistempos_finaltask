@extends('templates.main')

@section('pages_title')
<h1>{{ __("All Expense Category") }}</h1>
<p>{{ __("Do Something with all your expense category") }}</p>
@endsection

@section('content')
<style type="text/css">
    .background {
        position: fixed; /* atau 'absolute', tergantung kebutuhan */
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Warna gelap dengan transparansi */
        z-index: 1; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }
    .overlay {
        z-index: 2; /* Pastikan lebih tinggi dari elemen lain kecuali modal */
    }
</style>
<div class="col-sm-12">
    <div class="mt-3">
        <!-- @include('templates.alert') -->
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
            </div>
            <div class="header-title">
            <button type="button" class="btn btn-soft-primary" id="filterButton" data-bs-toggle="collapse" href="#filter" aria-controls="filter">{{ __("Filter") }}</button>
                <button type="button" class="btn btn-soft-success">{{ __("Excel") }}</button>
                <button type="button" class="btn btn-soft-danger">{{ __("PDF") }}</button>
                <button type="button" class="btn btn-soft-gray">{{ __("Import Category") }}</button>
                <a href="#" style="margin-left: 30px;">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#createCategory" class="btn btn-soft-primary">{{ __("Create +") }}</button>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
        <div class="collapse p-4 pt-3 " id="filter">
    {{ __("Some placeholder content for the collapse component. This panel is hidden by default but revealed when the client activates the relevant trigger.") }}
</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mt-4">
                <table id="basic-table" class="table table-striped mb-0" role="grid">
                    <thead>
                        <tr>
                            <th>{{ __("Name") }}</th>
                            <th>{{ __("Description") }}</th>
                            <th>{{ __("Created By") }}</th>
                            <th>{{ __("Actions") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expense_category as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->description }}</td>
                                <td>{{ $data->user->firstname }} {{ $data->user->lastname }}</td>
                                <td>
                                    <div class="inline">
                                        <button class="editBtn" data-id="{{$data['id']}}" data-name="{{$data->name}}" data-desc="{{$data->description}}" style="background-color: transparent; border: none; display: inline-block;">
                                        <!-- <button type="button" data-bs-toggle="modal" data-bs-target="#editClient"  style="background-color: transparent; border: none; display: inline-block;"> -->
                                            <a href="#">
                                                <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
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
                                        </a>
                                        <button type="button" onclick="confirmDelete({{ $data['id'] }})" data-bs-toggle="modal" data-bs-target="#staticBackdrop" style="background-color: transparent; border: none; display: inline-block;">
                                        <a href="#">
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
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                                <path d="M10.1108 14.6481L13.5768 11.1821" stroke="currentColor"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                </path>
                                            </svg>
                                        </a>
                                        </button>
                                        <form id="delete-form-{{ $data['id'] }}" action="{{ route('expenses.categories.destroy', $data['id']) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        </div>
                                        </td>
                                        </tr>
                                        <!-- modal edit -->
                                        <div class="modal fade" id="editCategory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="background">
                                            <div class="modal-dialog modal-dialog-centered modal-lg overlay">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="staticBackdropLabel">{{ __("Edit Expense Category") }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="{{ route('expenses.categories.update', $data['id']) }}" enctype="multipart/form-data" novalidate>
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="form-group">
                                                            <label class="form-label" for="name">{{ __("Name:") }}</label>
                                                            <input type="text" class="form-control bg-transparent @error('name') is-invalid @enderror"
                                                                id="name" name="name" placeholder="{{ __('name') }}" value="{{ old('name', $data->name) }}" required>
                                                            <small class="text-danger font-italic">
                                                                @error('name')
                                                                    {{ $message }}
                                                                @enderror
                                                            </small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label" for="email">{{ __("Description:") }}</label>
                                                            <input type="text" class="form-control bg-transparent @error('email') is-invalid @enderror"
                                                                id="description" name="description" placeholder="{{ __('Description') }}" value="{{ old('description', $data->description) }}" required>
                                                            <small class="text-danger font-italic">
                                                                @error('description')
                                                                    {{ $message }}
                                                                @enderror
                                                            </small>
                                                        </div>
                                                        <div id="formErrors"></div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-soft-primary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                                                            <button type="submit" class="btn btn-soft-success">{{ __("Save") }}</button>
                                                        </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
                <div class="bd-example" style="margin-left: 10px; margin-top:10px; margin-right:10px">
                    {{ $expense_category->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="createCategory" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="background">
        <div class="modal-dialog modal-dialog-centered modal-lg overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ __("Create Expense Category") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <form class="needs-validation" action="{{ route('expenses.categories.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="name">{{ __("Name:") }}</label>
                            <input type="text" class="form-control bg-transparent @error('name') is-invalid @enderror"
                                id="name" name="name_create" placeholder="{{ __('name') }}" value="{{ old('name_create')}}" required>
                            <small class=" text-danger font-italic">
                                @error('name_create')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">{{ __("Description:") }}</label>
                            <input type="text" class="form-control bg-transparent @error('email') is-invalid @enderror"
                                id="description_create" name="description_create" placeholder="{{ __('Description') }}" value="{{ old('description_create')}}" required>
                            <small class=" text-danger font-italic">
                                @error('description_create')
                                    {{ $message }}
                                @enderror
                            </small>
                        </div>
                </div>
                <div class="modal-footer">
                        <button type="button" class="btn btn-soft-primary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                        <button type="submit" id="submit_create" class="btn btn-soft-success">{{ __("Save") }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $('a[href="#"]').click(function(e) {
        e.preventDefault(); 
    });
</script>

<script>
    document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function() {
        let userId = this.getAttribute('data-id');
        let userName = this.getAttribute('data-name');
        let userEmail = this.getAttribute('data-desc');

        Swal.fire({
        title: 'Edit Data',
        html: `
            <input type="text" id="swal-input1" class="swal2-input" value="${userName}">
            <input type="text" id="swal-input2" class="swal2-input" value="${userEmail}">`,
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
            const name = document.getElementById('swal-input1').value;
            const email = document.getElementById('swal-input2').value;

            // Validasi inputan
            if (!name) {
                Swal.showValidationMessage(`Mohon isi nama`);
                return false;}

            // Kirim data yang sudah diedit ke controller
            fetch(`/expenses/categories/update/${userId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({name: name, description: email})
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Network response was not ok');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log(data);
                location.reload();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
        });
    });
    });
</script>

<script>
    @if(session('success'))
    localStorage.removeItem('modalShown');
    @endif
    $('#createCategory').on('shown.bs.modal', function () {
    localStorage.setItem('modalShown', 'true');
    });

    $('#createCategory').on('hidden.bs.modal', function () {
    localStorage.removeItem('modalShown');
    });

    $(document).ready(function() {
    if (localStorage.getItem('modalShown') === 'true') {
        $('#createCategory').modal('show');
    }
    });
</script>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: "{{ __('This action cannot be undone!') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ __("Yes, delete it!") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var filterCollapse = document.getElementById('filter');
        
        // Check if filter state is stored in localStorage
        var storedState = localStorage.getItem('filterState');
        if (storedState === 'true') {
            filterCollapse.classList.add('show');
            // filterCollapse.classList.toggle('show');
            // filterCollapse.classList.remove('hide');
        } 
        //  else {
        // //     filterCollapse.classList.toggle('hide');
        //  filterCollapse.classList.remove('show');
        //  }

    // Listen for click events on the filter button
    document.getElementById('filterButton').addEventListener('click', function () {
        // filterCollapse.addEventListener('show.bs.collapse', function () {
        //     // Toggle the collapse state
        //     // filterCollapse.classList.toggle('show');
        //     // Store the current collapse state in localStorage
        //     // localStorage.setItem('filterState', 'true');
        //     localStorage.setItem('filterState', filterCollapse.classList.contains('show') ? 'true' : 'false');
        // });
        filterCollapse.addEventListener('shown.bs.collapse', function () {
            // Store the current collapse state in localStorage when the collapse is shown
            localStorage.setItem('filterState', 'true');
        });
        filterCollapse.addEventListener('hidden.bs.collapse', function () {
            // Remove collapse state from localStorage when the collapse is hidden
            // localStorage.setItem('filterState', 'false');
            localStorage.removeItem('filterState');
        });
    });
    });
</script>
@endpush
