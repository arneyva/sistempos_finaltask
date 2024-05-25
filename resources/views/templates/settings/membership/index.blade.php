@extends('templates.main')

@section('pages_title')
<h1>Membership Settings</h1>
<p>Look All your membership</p>
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
</div>
    <div class="col" style=" display:flex;">
        <div class="card" style=" flex-grow: 1;">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                <h4 class="card-title"> One Score for </h4>
                </div>
            </div>
            <div class="card-body">
                <form action=" route('settings.membership.update', $membership['id'])">
                    @csrf
                    @method('patch')
                    <div class="form-group input-group">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                        <input type="text" id="spend_every" name="spend_every" aria-label="Username"  class="form-control bg-transparent @error('spend_every') is-invalid @enderror"  value="{{ old('spend_every', $membership->spend_every) }}" aria-describedby="basic-addon1" required>
                        <small class=" text-danger font-italic">
                            @error('spend_every')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <p>Customer will get 1 score for every Rp {{ number_format(old('spend_every', $membership->spend_every)) }} they spend.</p>
            </div>
            <div class="card-footer">
                    <div class="form-group" style="float: right;">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col" style=" display:flex;">
        <div class="card" style=" flex-grow: 1;">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                <h4 class="card-title"> One Score Equal</h4>
                </div>
            </div>
            <div class="card-body">
                <form action=" route('settings.membership.update', $membership['id'])">
                    @csrf
                    @method('patch')
                    <div class="form-group input-group">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                        <input type="text"  id="one_score_equal" aria-label="Username" name="one_score_equal" class="form-control bg-transparent @error('one_score_equal') is-invalid @enderror"  value="{{ old('one_score_equal', $membership->one_score_equal) }}" aria-describedby="basic-addon1" required>
                        <small class=" text-danger font-italic">
                            @error('one_score_equal')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <p>for 1 Score is equal to Rp {{ number_format(old('one_score_equal', $membership->one_score_equal)) }} discount</p>
            </div>
            <div class="card-footer">
                    <div class="form-group" style="float: right;">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col" style=" display:flex;">
        <div class="card" style=" flex-grow: 1;">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                <h4 class="card-title"> Send Email Every</h4>
                </div>
            </div>
            <div class="card-body">
                <form action=" route('settings.membership.update', $membership['id'])">
                    @csrf
                    @method('patch')
                    <div class="form-group input-group">
                        <input type="text"  id="score_to_email" aria-label="Username" name="score_to_email" class="form-control bg-transparent @error('score_to_email') is-invalid @enderror"  value="{{ old('score_to_email', $membership->score_to_email) }}" aria-describedby="basic-addon1" required>
                        <span class="input-group-text" id="basic-addon1">Score</span>
                        <small class=" text-danger font-italic">
                            @error('score_to_email')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <p>Whenever customer makes a sale, they will receive an email notification to redeem their score when they have reached {{ number_format(old('score_to_email', $membership->score_to_email)) }} points</p>
                </div>
            <div class="card-footer">
                    <div class="form-group" style="float: right;">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/autonumeric@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new AutoNumeric('#spend_every', {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 0,
            minimumValue: '1',
            maximumValue: '9999999999'
        });
        new AutoNumeric('#one_score_equal', {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 0,
            minimumValue: '1',
            maximumValue: '9999999999'
        });
        new AutoNumeric('#score_to_email', {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            decimalPlaces: 0,
            minimumValue: '1',
            maximumValue: '9999999999'
        });
    });
</script>

<script>
    $('a[href="#"]').click(function(e) {
        e.preventDefault(); 
    });
</script>

@if($errors->any())
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var errorModal = new bootstrap.Modal(document.getElementById('editClient'));
            errorModal.show();
        });
    </script>
@endif

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
