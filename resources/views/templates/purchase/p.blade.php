<html>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <form method="POST" class="mb-5" action="{{ route('purchases.makePayment', 62) }}" id="purchase_payment" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                        <div class="form-group col-sm-12">
                            <label class="form-label" for="name">Note:</label>
                            <input type="text" class="form-control form-control-sm" id="payment_note" name="payment_note" ></input> 
                        </div>
                        <div class="d-flex mb-1">
                            <div class="form-group col-sm-6 mb-1">
                                <label class="mb-0" >Balance Total:</label>
                            </div>
                            <div class="form-group col-sm-6 mb-1">
                                <label for="total-pay" class="mb-0">Paying Amount:</label>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="form-group col-sm-6 pe-2 mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"  id="basic-addon2">Rp</span>
                                    <input type="tel" aria-describedby="basic-addon2"  class="form-control form-control-sm @error('date') is-invalid @enderror" name="total_pay" id="total_pay" disabled>
                                </div>
                                <small id="p" class="text-danger font-italic"></small>
                            </div>
                            <div class="form-group col-sm-6 ps-2 mb-0">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"  id="basic-addon2">Rp</span>
                                    <input type="tel" aria-describedby="basic-addon2"  class="form-control form-control-sm @error('date') is-invalid @enderror" name="paying_amount" id="paying_amount">
                                </div>
                                <small id="excess" class="text-danger font-italic">Payment is Excess</small>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <label for="total-pay" class="mb-2">Due:</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text" id="basic-addon2">Rp</span>
                                <input type="tel" aria-describedby="basic-addon2" class="form-control form-control-sm @error('date') is-invalid @enderror" id="due" name="due" disabled>
                            </div>
                        </div>
                        <div class="form-group col-sm-12 d-flex justify-content-end mb-4">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                </form>
    </div>
    </body>
    <script>
    function Midtrans() {
        var balance = $('#order_total_input').val();
        $.ajax({
            url: `/cashier/midtrans/`,
            type: 'get',
            dataType: 'json',
            data: {
                GrandTotal: balance
            },
            headers: { 
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') 
            },
            success: function (response) {
                snap.embed(response.token, {
                    embedId: 'snap-container',

                    onSuccess: function (result) {
                    
                        saveSale(result.order_id);
                    
                    },
                    onError: function (result) {
                        /* You may add your own implementation here */
                        alert("payment failed!"); console.log(result);
                    },
                });
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terdapat error pada server'
                });
                // Log the error for debugging
                console.error('Error: ', error);
                console.error('Response: ', xhr.responseText);
            }
        });
    }
</script>
</html>