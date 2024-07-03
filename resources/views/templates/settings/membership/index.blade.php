@extends('templates.main')

@section('pages_title')
    <h1>{{ __('Membership') }} {{ __('Settings') }}</h1>
    <p>{{ __('Set your membership bonuses') }}</p>
@endsection

@section('content')
    <style type="text/css">
        .background {
            position: fixed;
            /* atau 'absolute', tergantung kebutuhan */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Warna gelap dengan transparansi */
            z-index: 1;
            /* Pastikan lebih tinggi dari elemen lain kecuali modal */
        }

        .overlay {
            z-index: 2;
            /* Pastikan lebih tinggi dari elemen lain kecuali modal */
        }
    </style>
    <div class="col-sm-12">
        <div class="mt-3">
            <!-- @include('templates.alert') -->
        </div>
    </div>

    <div class="col-xl-4 col-lg-5">
        <div class="card">
            <div class="card-header">
                <div class="header-title">
                    <h4 class="card-title">{{ __('Membership') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ __('Spend every') }} <strong>Rp
                        {{ number_format(floatval($membership->spend_every)) }}</strong>
                    {{ __('they have spent, customer will get') }} <strong>Rp
                        {{ number_format(floatval($membership->one_score_equal)) }}</strong>
                    {{ __('discount. Customer can claim their discount just when they have reached') }} <strong>Rp
                        {{ number_format(floatval($membership->one_score_equal) * floatval($membership->score_to_email)) }}</strong>
                    {{ __('discount, which is') }} <strong>Rp
                        {{ number_format(floatval($membership->spend_every) * floatval($membership->score_to_email)) }}</strong>
                    {{ __('total spent') }}</p>
                <br>
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title"> {{ __('Satu Score untuk') }} </h4>
                </div>
            </div>
            <div class="card-body">
                <form action=" {{ route('settings.membership.update', $membership['id']) }}" method="POST">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="action_type" value="spend_every">
                    <div class="form-group input-group">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                        <input type="tel" id="spend_every" name="spend_every" aria-label="Username"
                            class="form-control bg-transparent @error('spend_every') is-invalid @enderror"
                            value="{{ $membership->spend_every }}" aria-describedby="basic-addon1" required>
                        <small class=" text-danger font-italic">
                            @error('spend_every')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <p>{{ __('Customer will get 1 score for every Rp') }}
                        {{ number_format(floatval($membership->spend_every)) }} {{ __('they spend') }}.</p>
            </div>
            <div class="card-footer">
                <div class="form-group" style="float: right;">
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <div class="header-title">
                    <h4 class="card-title"> {{ __('One Score Equal') }}</h4>
                </div>
            </div>
            <div class="card-body">
                <form action=" {{ route('settings.membership.update', $membership['id']) }}" method="POST">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="action_type" value="one_score_equal">
                    <div class="form-group input-group">
                        <span class="input-group-text" id="basic-addon1">Rp</span>
                        <input type="tel" id="one_score_equal" aria-label="Username" name="one_score_equal"
                            class="form-control bg-transparent @error('one_score_equal') is-invalid @enderror"
                            value="{{ $membership->one_score_equal }}" aria-describedby="basic-addon1" required>
                        <small class=" text-danger font-italic">
                            @error('one_score_equal')
                                {{ $message }}
                            @enderror
                        </small>
                    </div>
                    <p></p>
                    <p class="text-number"></p>
                    <p></p>
                    <p>{{ __('for 1 Score is equal to Rp') }} {{ number_format(floatval($membership->one_score_equal)) }} discount</p>
            </div>
            <div class="card-footer">
                <div class="form-group" style="float: right;">
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="col" style=" display:flex;">
                <div class="card" style=" flex-grow: 1;">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title"> {{ __('Send Email Every') }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action=" {{ route('settings.membership.update', $membership['id']) }}" method="POST">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="action_type" value="score_to_email">
                            <div class="form-group input-group">
                                <input type="tel" id="score_to_email" aria-label="Username" name="score_to_email"
                                    class="form-control bg-transparent @error('score_to_email') is-invalid @enderror"
                                    value="{{ $membership->score_to_email }}" aria-describedby="basic-addon1" required>
                                <span class="input-group-text" id="basic-addon1">Score</span>
                                <small class=" text-danger font-italic">
                                    @error('score_to_email')
                                        {{ $message }}
                                    @enderror
                                </small>
                            </div>
                            <p>{{ __('Whenever customer makes a sale, they will receive an email notification to redeem their score when they have reached') }} {{ number_format(floatval($membership->score_to_email)) }} points
                            </p>
                    </div>
                    <div class="card-footer">
                        <div class="form-group" style="float: right;">
                            <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/autonumeric@latest"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new AutoNumeric('#spend_every', 'commaDecimalCharDotSeparator');
                new AutoNumeric('#one_score_equal', 'commaDecimalCharDotSeparator');
                new AutoNumeric('#score_to_email', {
                    decimalPlaces: 0, // Tidak menggunakan tempat desimal
                    digitGroupSeparator: '.', // Menggunakan titik sebagai pemisah ribuan
                    decimalCharacter: ',', // Karakter desimal, disetel ke koma jika perlu
                    allowDecimalPadding: false, // Tidak memperbolehkan padding desimal
                    minimumValue: '10' // Nilai minimum adalah 0, untuk memastikan bilangan positif
                });
            });
        </script>

        <script>
            $('a[href="#"]').click(function(e) {
                e.preventDefault();
            });
        </script>
    @endpush
