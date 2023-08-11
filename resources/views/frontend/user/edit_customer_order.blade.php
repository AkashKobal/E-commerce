<?php 
$shipment = $response->ShipmentData[0]->Shipment;
$shipmentStatus = $shipment->Status;
$shippingAddress = json_decode($order->shipping_address);
//print_r($order);
//echo $order->id;
//echo json_decode($order->shipping_address)->name;

//{"name":"Mr. Customer","email":"customer@example.com","address":"bangalore urban","country":"India","state":"Karnataka","city":"Bengaluru","postal_code":"460028","phone":"9399839600"}
//echo $shipmentStatus->Status;
?>

@if($shipmentStatus->Status == 'Manifested' || $shipmentStatus->Status == 'In Transit' || $shipmentStatus->Status == 'Pending' || $shipmentStatus->Status == 'Scheduled')
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">{{ translate('Edit Order') }}</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
	</div>

	<div class="modal-body gry-bg px-3 pt-3">
		<form class="" action="{{ route('purchase_history.update') }}" method="post">
			@csrf
			<input type="hidden" name="order_id" value="{{ $order->id }}" />
		  <div class="row">
			  <div class="col-md-2">
				  <label>{{ translate('Name')}}</label>
			  </div>
			  <div class="col-md-10">
				  <input type="text" class="form-control mb-3" placeholder="{{ translate('Name')}}" name="name" value="{{ $shippingAddress->name }}" required>
			  </div>
		  </div>

		  <div class="row">
			  <div class="col-md-2">
				  <label>{{ translate('Address')}}</label>
			  </div>
			  <div class="col-md-10">
				  <textarea type="text" class="form-control mb-3" rows="3" name="address" placeholder="{{ translate('Address')}}" data-buttons="bold,underline,italic,|,ul,ol,|,paragraph,|,undo,redo" required>{{ $shippingAddress->address }}</textarea>
			  </div>
		  </div>
		  
		  <div class="row">
			  <div class="col-md-2">
				  <label>{{ translate('Phone')}}</label>
			  </div>
			  <div class="col-md-10">
				  <input type="text" class="form-control mb-3" placeholder="{{ translate('Phone')}}" name="phone" value="{{ $shippingAddress->phone }}" required>
			  </div>
		  </div>
		  
		  <div class="text-right mt-4">
			  <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('cancel')}}</button>
			  <button type="submit" class="btn btn-primary">{{ translate('Save')}}</button>
		  </div>
	  </form>
	</div>
@else
	<div class="modal-header">
		<h5 class="modal-title" id="exampleModalLabel">You are not allowed to edit order.</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
	</div>
@endif

