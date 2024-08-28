@extends('templates.main')

@section('pages_title')
<h1>My Attendances</h1>
<p>Do Something with all your attendances</p>
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
    @if(isset($message))
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
            <label for="month">{{ $message }}</label>
            </div>
        </form>
    @else
    <form action="{{ route('hrm.myattendances.check') }}" method="POST" class="card-header d-flex justify-content-between">
    @csrf        
    <div class="header-title">
            </div>
            <div class="header-title d-flex">
            <label for="month" class="me-2">Pick a Month:</label>
            <input value="@if(isset($month)){{ $month }}@endif" class="form-control" type="month" id="month" name="month" required>
            <!-- <button type="button" class="btn btn-soft-primary" id="filterButton" data-bs-toggle="collapse" href="#filter" aria-controls="filter">Filter</button>
                <button type="button" class="btn btn-soft-success">Excel</button>
                <button type="button" class="btn btn-soft-danger">PDF</button>
                <button type="button" class="btn btn-soft-gray">Import Client</button> -->
                <button type="submit" class="btn btn-soft-primary ms-3">Check</button>
            </div>
</form>
    @endif

        <div class="card-body p-0">
        <div class="collapse p-4 pt-3 " id="filter">
    Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.
</div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive mt-4">
            @if(isset($attendances))
                <table id="basic-table" class="table table-striped mb-0" role="grid">
                    <thead>
                        <tr>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Jadwal Masuk</th>
                        <th>Jadwal Keluar</th>
                        <th>Clock In</th>
                        <th>Clock Out</th>
                        <th>Status</th>
                        <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                    <td>{{ $attendance['date'] }}</td>
                    <td>{{ $attendance['day'] }}</td>
                    <td>{{ $attendance['schedule_in'] }}</td>
                    <td>{{ $attendance['schedule_out'] }}</td>
                    <td>{{ $attendance['clock_in'] }}</td>
                    <td>{{ $attendance['clock_out'] }}</td>
                    <td>{{ $attendance['status'] }}
                        {{ $attendance['on_time'] ? ' -> ' . $attendance['on_time'] : '' }}
                        {{ $attendance['late_in'] ? ' -> ' . $attendance['late_in'] : '' }}{{ $attendance['late_out'] ? ', ' . $attendance['late_out'] : '' }}
                    </td>
                    <td>
                    @if ($attendance['requestButton'] == 'yes')
                    <a href="{{ route('hrm.request.create', $attendance['attendanceId']) }}">
                        <button type="button"  class="btn btn-sm btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"></path>
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"></path>
                            </svg>
                        </button>
                    </a>
                        @endif
                    </td>
                </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
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



