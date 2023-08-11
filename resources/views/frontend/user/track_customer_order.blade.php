<?php 
$shipment = $response->ShipmentData[0]->Shipment;
$shipmentScans = $shipment->Scans;
$shipmentStatus = $shipment->Status;
?>

<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel">{{ translate('Delivery by')}}: {{ $shipment->SenderName }}</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $status = $order->orderDetails->first()->delivery_status;
@endphp

<div class="modal-body gry-bg px-3 pt-3">
	<b class="fs-15 px-3" >{{ translate('Tracking ID') }} : {{ $shipment->AWB }}</b>
    <div class="card mt-4">
		<div class="card-header">
			<b class="fs-15">{{ translate('Order Current Status') }}</b>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-20 fw-600">{{ translate('Status')}}:</td>
                            <td>{{ $shipmentStatus->Status }}</td>
                        </tr>
                        <tr>
                            <td class="w-20 fw-600">{{ translate('Location')}}:</td>
                            <td>{{ $shipmentStatus->StatusLocation }}</td>
                        </tr>
						{{--@if ($shipmentStatus->RecievedBy != "")--}}
                        <tr>
                            <td class="w-20 fw-600">{{ translate('Recieved By')}}:</td>
                            <td>{{ $shipmentStatus->RecievedBy }}</td>
                        </tr>
						{{--@endif--}}
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-20 fw-600">{{ translate('Details')}}:</td>
							<td>{{ $shipmentStatus->Instructions }}</td>
                        </tr>
						<tr>
                            <td class="w-20 fw-600">{{ translate('Date')}}:</td>
							<td>{{ date('d-m-Y h:i a',strtotime($shipmentStatus->StatusDateTime)) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-header">
                  <b class="fs-15">{{ translate('Shipment Details') }}</b>
                </div>
				
				
				<div class="card-body pb-0">
					@php 
						foreach($shipmentScans as $scan){
					@endphp
						<div class="row" style="border-bottom: 1px solid lightgray;">
							<div class="col-sm-6">
								<table class="table table-borderless">
									<tr>
										<td class="w-20 fw-600">{{ translate('Status')}}:</td>
										<td>{{ $scan->ScanDetail->Scan }}</td>
									</tr>
									<tr>
										<td class="w-20 fw-600">{{ translate('Location')}}:</td>
										<td>{{ $scan->ScanDetail->ScannedLocation }}</td>
									</tr>
								</table>
							</div>
							<div class="col-sm-6">
								<table class="table table-borderless">
									<tr>
										<td class="w-20 fw-600">{{ translate('Date')}}:</td>
										<td>{{ date('d-m-Y h:i a',strtotime($scan->ScanDetail->StatusDateTime)) }}</td>
									</tr>
									<tr>
										<td class="w-20 fw-600">{{ translate('Details')}}:</td>
										<td>{{ $scan->ScanDetail->Instructions }}</td>
									</tr>
								</table>
							</div>
						</div>
					@php
						}
					@endphp
					
				</div>
            </div>
        </div>
    </div>
</div>

