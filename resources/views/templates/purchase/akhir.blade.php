<div class="card-header d-flex justify-content-between px-0" id="shipping-1">
                                <div class="header-title">
                                    <h6 class="card-title">Shipping Request Information</h6>
                                </div>
                            </div>
                            <div class="card-body py-3" style="padding-left:0px;" id="shipping-2">
                                <div class="new-user-info">
                                    <div class="row">
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Destination Address</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <textarea class="form-control" id="address" name="address" disabled>{{ $purchase->request_address ? $purchase->request_address : '' }}</textarea>
                                                <span class="print-value" id="print-address"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important" >Request Arrive date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="text" value="{{ $purchase->request_req_arrive_date ? $purchase->request_req_arrive_date->translatedFormat('d, F Y') : '-' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="req_arrive_date" name="req_arrive_date" disabled>
                                                <span class="print-value" id="print-req_arrive_date"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <select name="courier" id="courier" class="form-control form-control-sm" required >
                                                    <option value="" selected disabled hidden>Courier</option>
                                                    <option value="jne" {{ old('courier', $purchase->request_courier) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                    <option value="j&t" {{ old('courier', $purchase->request_courier) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                    <option value="sicepat" {{ old('courier', $purchase->request_courier) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                    <option value="anteraja" {{ old('courier', $purchase->request_courier) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                    <option value="posindo" {{ old('courier', $purchase->request_courier) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                    <option value="own" {{ old('courier', $purchase->request_courier) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                                </select> 
                                                <span class="print-value" id="print-courier"></span>                                           
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="driver_phone">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ $purchase->request_driver_contact ? $purchase->request_driver_contact : '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_driver_phone" name="driver_phone" >
                                                <span class="print-value" id="print-driver_phone"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: none; align-items: center;" id="shipment_number">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{ $purchase->request_shipment_number ? $purchase->request_shipment_number : '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="input_shipment_number" name="shipment_number" >
                                                <span class="print-value" id="print-shipment_number"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="tel" value="{{$purchase->request_shipment_cost ? $purchase->request_shipment_cost : '' }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="shipment_cost" name="shipment_cost" required>
                                                <span class="print-value" id="print-shipment_cost"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="date" value="{{ $purchase->request_estimate_arrive_date ? $purchase->request_estimate_arrive_date : ''}}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="est_arrive_date" name="est_arrive_date" >
                                                <span class="print-value" id="print-est_arrive_date"></span>
                                            </div>
                                        </div>
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <div class="col-sm-3 p-0">
                                                <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                            </div>
                                            <div class="col-sm-9 p-0" style="float:right;">
                                                <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="delivery_file" name="delivery_file" >
                                                <span class="print-value" id="print-delivery_file"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>