@if ($purchase->statut == "completed" || $purchase->purchase_returns->isNotEmpty())
            <div class="card-body py-5 tab-pane fade" id="return" role="tabpanel">
            @if (isset($returpurchase)) 
            <form method="POST" action="{{ route('purchases.updateReturn', $returpurchase['id']) }}" id="returpurchase_order" enctype="multipart/form-data">
            @else 
            <form method="POST" action="{{ route('purchases.makeReturn', $purchase['id']) }}" id="returpurchase_order" enctype="multipart/form-data">
            @endif 
                @csrf
                @method('PATCH')
                <div class="row">
                    <div class="form-group col-sm-4">
                        <label class="form-label" for="name">Date:</label>
                        <input type="date" value="{{  old('returdate') ?: (isset($returpurchase) ? $returpurchase->date->format('Y-m-d') : '') }}" class="form-control @error('date') is-invalid @enderror" id="returdate" name="returdate" required>
                    </div>
                    <div class="form-group col-sm-12 mb-3">
                        <table id="returTable" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th class="col-4">Name</th>
                                    <th class="col-1">Qty Unpassed</th>
                                    <th class="col-1">Qty Return</th>
                                    <th class="col-1">Qty Request</th>
                                    <th class="col-1">Price</th>
                                    <th class="col-1">Subtotal</th>
                                    <th class="col-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($products_returned as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="d-flex flex-column"> 
                                                <div style="margin-bottom: 5px; word-wrap: break-word; word-break: break-all; white-space: normal;">
                                                    @if (!empty($data->product_variant_id))
                                                    <div>{{ $data->product->name }} {{ $data->product_variant->name }}</div>
                                                    @else
                                                    <div>{{ $data->product->name }}</div>
                                                    @endif
                                                </div>
                                                @if (!empty($data->product_variant_id))
                                                <div>{{ $data->product_variant->code }}</div>
                                                @else
                                                <div>{{ $data->product->code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @if (!empty($data->product_variant_id))
                                        <td class="variant-id" data-variant="{{ $data->product_variant->id }}" style="display:none;" hidden></td>
                                    @endif
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control unpassedqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_unpassed : 1 }}" value="{{$data->purchase_return_id ? $data->qty_unpassed : 1 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_unpassed[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control returnqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_return : 0 }}" value="{{$data->purchase_return_id ? $data->qty_return : 0 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_return[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    <td style="text-align: start;">
                                        <input type="number" class="form-control requestqty px-0" data-previous-qty="{{$data->purchase_return_id ? $data->qty_request : 0 }}" value="{{$data->purchase_return_id ? $data->qty_request : 0 }}" style="width: 5vw; display: inline-block; text-align: center;" name="qty_request[{{ $data->product_variant_id ? $data->product_variant_id : $data->product_id }}]">
                                        <span>{{ $data->unit->ShortName }}</span>
                                    </td>
                                    @if (!empty($data->product_variant_id))
                                    <td class="returcost">{{ $data->product_variant->cost  }}</td>
                                    @else
                                    <td class="returcost">{{ $data->product->cost }}</td>
                                    @endif
                                    @if (!empty($data->product_variant_id))
                                    <td class="retursubtotal">{{$data->purchase_return_id ? -floatval($data->total) : -floatval($data->product_variant->cost) }}</td>
                                    @else
                                    <td class="retursubtotal">{{$data->purchase_return_id ? -floatval($data->total) : -floatval($data->product->cost) }}</td>
                                    @endif
                                    <td>
                                        <div class="flex align-items-center list-user-action">
                                        <a class="btn btn-sm btn-icon btn-danger returdelete" 
                                            data-value="{{ $data->product_id }}" 
                                            data-code="{{ !empty($data->product_variant_id) ? $data->product_variant->code : $data->product->code }}">
                                                <span class="btn-inner">
                                                    <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                        <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="card-header d-flex justify-content-between px-0">
                            <div class="header-title">
                                <h6 class="card-title">Shipping Return Information</h6>
                            </div>
                        </div>
                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Destination Address</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="returaddress" name="returaddress" required>{{  old('returaddress') ? old('returaddress') : (isset($returpurchase->address) ? $returpurchase->address : $purchase->provider->adresse) }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="returcourier" id="returcourier" class="form-control form-control-sm"  >
                                                <option value="" selected disabled hidden>Courier</option>
                                                <option value="jne" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                <option value="j&t" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                <option value="sicepat" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                <option value="anteraja" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                <option value="posindo" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                <option value="own" {{ old('returcourier', isset($returpurchase) ? $returpurchase->courier : null) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                            </select> 
                                            <span class="print-value" id="print-courier"></span>                                           
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="returdriver_phone">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('returdriver_phone') ? old('returdriver_phone') : (isset($returpurchase->driver_contact) ? $returpurchase->driver_contact : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_driver_phone" name="returdriver_phone">
                                            <span class="print-value" id="returprint-driver_phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="returshipment_number">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('returshipment_number') ? old('returshipment_number') : (isset($returpurchase->shipment_number) ? $returpurchase->shipment_number : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_shipment_number" name="returshipment_number">
                                            <span class="print-value" id="returprint-shipment_number"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('returshipment_cost') ? old('returshipment_cost') : (isset($returpurchase->shipment_cost) ? $returpurchase->shipment_cost : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returshipment_cost" name="returshipment_cost">
                                            <span class="print-value" id="returprint-shipment_cost"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header d-flex justify-content-between p-0">
                            <div class="header-title">
                                <h6 class="card-title">Shipping Request Information</h6>
                            </div>
                        </div>

                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Destination Address</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <textarea class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestaddress" name="retur_requestaddress" required>{{ old('retur_requestaddress') ?: (isset($returpurchase) ? $returpurchase->request_address : $purchase->address)}}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" placeholder="-" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Request Arrive date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{ old('retur_requestreq_arrive_date') ? old('retur_requestreq_arrive_date') : (isset($returpurchase->request_req_arrive_date) ? $returpurchase->request_req_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestreq_arrive_date" name="retur_requestreq_arrive_date" >
                                        </div>
                                    </div>
                                    @if (isset($returpurchase)) 
                                    @if ($returpurchase->statut !== "pending" && 
                                            $returpurchase->statut !== "ordered" && 
                                            $returpurchase->statut !== "canceled" && 
                                            $returpurchase->statut !== "refused")
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Courier</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <select name="retur_requestcourier" id="retur_requestcourier" class="form-control form-control-sm"  >
                                                <option value="" selected disabled hidden>Courier</option>
                                                <option value="jne" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'jne' ? 'selected' : '' }} >JNE</option>
                                                <option value="j&t" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'j&t' ? 'selected' : '' }} >J&T</option>
                                                <option value="sicepat" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'sicepat' ? 'selected' : '' }} >SiCepat</option>
                                                <option value="anteraja" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'anteraja' ? 'selected' : '' }} >Anteraja</option>
                                                <option value="posindo" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'posindo' ? 'selected' : '' }} >Pos Infonesia</option>
                                                <option value="own" {{ old('retur_requestcourier', isset($returpurchase) ? $returpurchase->request_courier : null) == 'own' ? 'selected' : '' }} >Own Courier</option>
                                            </select> 
                                            <span class="print-value" id="retur_requestprint-courier"></span>                                           
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retur_requestdriver_phone">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Driver Contact</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retur_requestdriver_phone') ? old('retur_requestdriver_phone') : (isset($returpurchase->request_driver_contact) ? $returpurchase->request_driver_contact : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestinput_driver_phone" name="retur_requestdriver_phone">
                                            <span class="print-value" id="retur_requestprint-driver_phone"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retur_requestshipment_number">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('retur_requestshipment_number') ? old('retur_requestshipment_number') : (isset($returpurchase->request_shipment_number) ? $returpurchase->request_shipment_number : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestinput_shipment_number" name="retur_requestshipment_number">
                                            <span class="print-value" id="retur_requestprint-shipment_number"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Shipment Cost</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retur_requestshipment_cost') ? old('retur_requestshipment_cost') : (isset($returpurchase->request_shipment_cost) ? $returpurchase->request_shipment_cost : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestshipment_cost" name="retur_requestshipment_cost">
                                            <span class="print-value" id="retur_requestprint-shipment_cost"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Estimate Arrive Date</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="date" value="{{  old('retur_requestest_arrive_date') ? old('retur_requestest_arrive_date') : (isset($returpurchase->request_estimate_arrive_date) ? $returpurchase->request_estimate_arrive_date->format('Y-m-d') : '') }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestest_arrive_date" name="retur_requestest_arrive_date">
                                            <span class="print-value" id="retur_requestprint-est_arrive_date"></span>
                                        </div>
                                    </div>
                                    <div class="form-group mb-2" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label custom-file-input" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Delivery File</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror" id="retur_requestdelivery_file" name="retur_requestdelivery_file" >
                                            <span class="print-value" id="request_print-delivery_file"></span>
                                        </div>
                                    </div>
                                    @if (isset($returpurchase))
                                    @if ($returpurchase->delivery_file)
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <p><strong>Download File: </strong><a href="{{route('purchases.file', $returpurchase['id'])}}">Download</a></p>
                                        </div>
                                    </div>
                                    @endif
                                    @endif
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="card-header d-flex justify-content-between px-0">
                            <div class="header-title">
                                <h6 class="card-title">Payment Information</h6>
                            </div>
                        </div>
                        <div class="card-body py-3" style="padding-left:0px;">
                            <div class="new-user-info">
                                <div class="row">
                                    
                                    
                                    <div class="form-group" style="display: flex; align-items: center;">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Payment Method</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                        <select name="returpayment_method" id="returpayment_method" class="form-control">
                                            <option value="" selected disabled hidden>Payment Method</option>
                                            <option value="bni" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bni' ? 'selected' : '' }}>BNI</option>
                                            <option value="bri" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bri' ? 'selected' : '' }}>BRI</option>
                                            <option value="mandiri" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'mandiri' ? 'selected' : '' }}>Mandiri</option>
                                            <option value="permata" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'permata' ? 'selected' : '' }}>Permata</option>
                                            <option value="bca" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'bca' ? 'selected' : '' }}>BCA</option>
                                            <option value="gopay" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'gopay' ? 'selected' : '' }}>Gopay</option>
                                            <option value="ovo" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'ovo' ? 'selected' : '' }}>OVO</option>
                                            <option value="cash" {{ old('returpayment_method', isset($returpurchase) ? $returpurchase->payment_method : null) == 'cash' ? 'selected' : '' }}>Cash</option>
                                        </select>                                            
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center; " id="retursupplier_ewalet">
                                        <div class="col-sm-3 p-0">
                                            <label class="form-label" for="name" style="margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">E-Walet Number</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{old('retursupplier_ewalet') ? old('retursupplier_ewalet') : (isset($returpurchase->supplier_ewalet) ? $returpurchase->supplier_ewalet : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_ewalet" name="retursupplier_ewalet" >
                                            <span class="print-value" id="returprint-input_ewalet"></span>
                                        </div>
                                    </div>
                                    <div class="form-group" style="display: none; align-items: center;" id="retursupplier_bank_account">
                                        <div class="col-sm-3 p-0" >
                                            <label class="form-label" for="name" style=" margin-right: 10px; margin-bottom: 0px !important;  font-size: 15px !important">Bank Account</label>
                                        </div>
                                        <div class="col-sm-9 p-0" style="float:right;">
                                            <input type="tel" value="{{ old('retursupplier_bank_account') ? old('retursupplier_bank_account') : (isset($returpurchase->supplier_bank_account) ? $returpurchase->supplier_bank_account : '')  }}" class="form-control form-control-sm @error('date') is-invalid @enderror" id="returinput_bank_account" name="retursupplier_bank_account">
                                            <span class="print-value" id="returprint-input_bank_account"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body py-0" style="padding-left:0px;">
                            <table id="returbasic-table" class="table table-bordered table-sm"
                                role="grid">
                                <tbody>
                                    <tr>
                                        <td class="col-3">Order Subtotal</td>
                                        <td id="returorder_subtotal" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="returorder_subtotal_input" id="returorder_subtotal_input">
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td class="col-3">Shipping</td>
                                        <td id="returorder_shipping" class="col-7"style="text-align:right;">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="col-3">Grand Total</td>
                                        <td id="returorder_total" class="col-7"style="text-align:right;">Rp 0</td>
                                        <input type="hidden" name="returorder_total_input" id="returorder_total_input">
                                    </tr>
                                    
                            </table>
                        </div>
                    </div>
                    <div class="form-group col-sm-12 mb-2">
                        <label class="form-label custom-file-input mb-2" for="name">Return Proof:</label>
                        <input type="file" onchange="checkFileSize(this)" class="form-control form-control-sm @error('date') is-invalid @enderror mb-2" id="retur_proof" name="retur_proof" >
                        @if (isset($returpurchase))
                        @if ($returpurchase->delivery_file)
                                <p><strong>Download File: </strong><a href="{{route('purchases.file', $returpurchase['id'])}}">Download</a></p>
                        @endif
                        @endif
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Retur Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="returnotes" name="returnotes" >{{ old('returnotes') ? old('returnotes') : (isset($returpurchase->notes) ? $returpurchase->notes : '') }}</textarea> 
                    </div>
                    <div class="form-group col-sm-12">
                        <label class="form-label" for="name">Supplier Note:</label>
                        <textarea  class="form-control @error('date') is-invalid @enderror" id="retursupplier_notes" name="retursupplier_notes" >{{ old('retursupplier_notes') ? old('retursupplier_notes') : (isset($returpurchase->supplier_notes) ? $returpurchase->supplier_notes : '') }}</textarea> 
                    </div>
                    @if (isset($returpurchase))
                    <div class="form-group col-sm-12">
                    <label class="form-label" for="name">Status:</label>
                    <select name="returstatut" id="returstatut" class="form-control" required>
                        @if($returpurchase->statut == 'pending' || $returpurchase->statut == 'ordered' || $returpurchase->statut == 'refused' || $returpurchase->statut == 'canceled')
                            @if($returpurchase->statut == 'refused')
                                <option value="refused" selected>Refused</option>
                            @endif
                            <option value="pending" {{ old('returstatut', $returpurchase->statut) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ordered" {{ old('returstatut', $returpurchase->statut) == 'ordered' ? 'selected' : '' }}>Ordered</option>
                            <option value="canceled" {{ old('returstatut', $returpurchase->statut) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        @endif
                        <option value="shipped" {{ old('returstatut', $returpurchase->statut) == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="arrived" {{ old('returstatut', $returpurchase->statut) == 'arrived' ? 'selected' : '' }}>Arrived</option>
                        <option value="completed" {{ old('returstatut', $returpurchase->statut) == 'completed' ? 'selected' : '' }}>Complete</option>
                    </select> 
                    </div>
                    @endif
                </div>
                <div class="card-footer d-flex" style="float: right;">
                    <button type="button" class="send-email" data-send="send_email" id="retursend-email" autofocus>
                        <div class="svg-wrapper-1">
                            <div class="svg-wrapper">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                width="24"
                                height="24"
                            >
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path
                                fill="currentColor"
                                d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"
                                ></path>
                            </svg>
                            </div>
                        </div>
                        <span>Save and Send Email</span>
                    </button>
                    <button type="submit" id="saveReturn" class="btn btn-primary ms-2">Save</button>
                </div>
                <input type="hidden" id="returproducts" name="returproducts">
                <input type="hidden" id="returproducts_with_variant" name="returproducts_with_variant">
            </form>
            </div>
            @endif