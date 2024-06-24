@extends('templates.main')

@section('pages_title')
<h1>All Shifts</h1>
<p>Do Something with all your shifts</p>
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
    .card-body-scroll {
        height: 221px; /* Atur tinggi maksimal sesuai kebutuhan Anda */
        overflow-y: auto;
    }
    .button-delete {
    padding: 0;
    margin: 0;
    border: none;
    background: none;
    cursor: pointer;
    }

    .button-delete {
    --primary-color: #de7c7c;
    --hovered-color: #c84747;
    position: relative;
    display: flex;
    font-weight: 600;
    font-size: 20px;
    gap: 0.5rem;
    align-items: center;
    }

    .button-delete p {
    margin: 0;
    position: relative;
    font-size: 20px;
    color: var(--primary-color);
    }

    .button-delete::after {
    position: absolute;
    content: "";
    width: 0;
    left: 0;
    bottom: -4px;
    background: var(--hovered-color);
    height: 2px;
    transition: 0.15s ease-out;
    }

    .button-delete p::before {
    position: absolute;
    /*   box-sizing: border-box; */
    content: "Delete";
    width: 0%;
    inset: 0;
    color: var(--hovered-color);
    overflow: hidden;
    transition: 0.15s ease-out;
    }

    .button-delete:hover::after {
    width: 100%;
    }

    .button-delete:hover p::before {
    width: 100%;
    }
    
    .button-edit {
    padding: 0;
    margin: 0;
    border: none;
    background: none;
    cursor: pointer;
    }

    .button-edit {
    --primary-color: #eecd72;
    --hovered-color: #eab934;
    position: relative;
    display: flex;
    font-weight: 600;
    font-size: 20px;
    gap: 0.5rem;
    align-items: center;
    }

    .button-edit p {
    margin: 0;
    position: relative;
    font-size: 20px;
    color: var(--primary-color);
    }

    .button-edit::after {
    position: absolute;
    content: "";
    width: 0;
    left: 0;
    bottom: -4px;
    background: var(--hovered-color);
    height: 2px;
    transition: 0.15s ease-out;
    }

    .button-edit p::before {
    position: absolute;
    /*   box-sizing: border-box; */
    content: "Edit";
    width: 0%;
    inset: 0;
    color: var(--hovered-color);
    overflow: hidden;
    transition: 0.15s ease-out;
    }

    .button-edit:hover::after {
    width: 100%;
    }

    .button-edit:hover p::before {
    width: 100%;
    }
    .card-list {
    display: flex;
    flex-direction: column;
    height: 349px; /* Tetapkan tinggi tetap untuk card */
    }

    .card-header {
    flex-shrink: 0; /* Pastikan header tidak menyusut */
    max-height: 21%; /* Tetapkan tinggi maksimum untuk header, misalnya 50% dari card */
    overflow: hidden; /* Sembunyikan overflow jika teks terlalu banyak */
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2; /* Batas jumlah baris (ganti sesuai kebutuhan) */
    text-overflow: ellipsis;
    white-space: normal;
    color:#000000;
    }

    .card-body-list {
        flex-grow: 1; /* Pastikan body mengambil sisa ruang */
    }
</style>
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex flex-wrap align-items-center">
                </div>
                <div class="header-title">
                    <button type="button" class="btn btn-soft-primary" id="filterButton" data-bs-toggle="collapse" href="#filter" aria-controls="filter">Filter</button>
                    <button type="button" class="btn btn-soft-success">Excel</button>
                    <button type="button" class="btn btn-soft-danger">PDF</button>
                    <button type="button" class="btn btn-soft-gray">Import Client</button>
                    <a href="#" style="margin-left: 30px;">
                        <a href="{{ route('hrm.shifts.create') }}">
                            <button type="button" class="btn btn-soft-primary">Create +</button>
                        </a>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@foreach ($shifts as $data)
<div class="col-lg-3">
    <div class="card card-list">
        <h5 class="card-header"> {{$data->name}} </h5>
        <div class="card-body card-body-scroll card-body-list mt-3 py-0">
            <ul class="list-group list-group-flush">  
                @if (!is_null($data->monday_in) || !is_null($data->monday_out)) 
                <li class="list-group-item text-center pt-0 "> Monday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->monday_in}}
Out-TIme:   {{$data->monday_out}}
</pre>
                    </ul>
                @endif
                @if (!is_null($data->tuesday_in) || !is_null($data->tuesday_out)) 
                <li class="list-group-item text-center pt-0 "> Tuesday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->tuesday_in}}
Out-TIme:   {{$data->tuesday_out}}
</pre>
                    </ul>
                    @endif
                @if (!is_null($data->wednesday_in) || !is_null($data->wednesday_out)) 
                <li class="list-group-item text-center pt-0 "> Wednesday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->wednesday_in}}
Out-TIme:   {{$data->wednesday_out}}
</pre>
                    </ul>
                    @endif
                @if (!is_null($data->thursday_in) || !is_null($data->thursday_out)) 
                <li class="list-group-item text-center pt-0 "> Thursday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->thursday_in}}
Out-TIme:   {{$data->thursday_out}}
</pre>
                    </ul>
                    @endif
                @if (!is_null($data->friday_in) || !is_null($data->friday_out)) 
                <li class="list-group-item text-center pt-0 "> Friday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->friday_in}}
Out-TIme:   {{$data->friday_out}}
</pre>
                    </ul>
                    @endif
                @if (!is_null($data->saturday_in) || !is_null($data->saturday_out)) 
                <li class="list-group-item text-center pt-0 "> Saturday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->saturday_in}}
Out-TIme:   {{$data->saturday_out}}
</pre>
                    </ul>
                    @endif
                @if (!is_null($data->sunday_in) || !is_null($data->sunday_out)) 
                <li class="list-group-item text-center pt-0 "> Sunday</li>
                    <ul class="list-group list-group-flush mx-2 text-center">   
                        <pre class="text-muted">In-Time:    {{$data->sunday_in}}
Out-TIme:   {{$data->sunday_out}}
</pre>
                    </ul>
                @endif
            </ul>
        </div>
        <div class="card-body pt-2">   
            <div class="d-flex flex-wrap align-items-center" style="float: right;">
                <a class="button-edit" href="{{ route('hrm.shifts.show', $data['id']) }}">
                    <p>
                        Edit
                    </p>
                </a>
                <a class="button-delete" onclick="confirmDelete({{ $data['id'] }})" href="#" style="margin-left: 15px;">
                    <p>
                        Delete
                    </p>
                </a>
                <form id="delete-form-{{  $data['id'] }}" action="{{ route('hrm.shifts.destroy',  $data['id']) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('script')
<script>
    $('a[href="#"]').click(function(e) {
        e.preventDefault(); 
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
