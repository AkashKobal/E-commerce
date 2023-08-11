@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Purchase History') }}</h5>
        </div>
        @if (count($orders) > 0)
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Code')}}</th>
                            <th data-breakpoints="md">{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th data-breakpoints="md">{{ translate('Delivery Status')}}</th>
                            <th data-breakpoints="md">{{ translate('Payment Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $key => $order)
                            @if (count($order->orderDetails) > 0)
								<?php 
									$delihveryData = DB::table('delhivery_response')->where('order_id', $order->id)->select('*')->first();
									//print_r($delihveryData);
									$waybill = "";
									$orderStatus = "";
									if($delihveryData){
										$waybill = $delihveryData->waybill;
										$orderStatus = $delihveryData->order_status;
									}
								?>
                                <tr>
                                    <td>
                                        <a href="#{{ $order->code }}" onclick="show_purchase_history_details({{ $order->id }})">{{ $order->code }}</a>
                                    </td>
                                    <td>{{ date('d-m-Y', $order->date) }}</td>
                                    <td>
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                    <td>
                                        {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                                        @if($order->delivery_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-danger">{{translate('Unpaid')}}</span>
                                        @endif
                                        @if($order->payment_status_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                    <td class="text-right">
									{{--@if ($order->orderDetails->first()->delivery_status == 'pending' && $order->payment_status == 'unpaid')
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                               <i class="las la-trash"></i>
                                           </a>
									@endif--}}
										
										@if($orderStatus == "cancelled" || $waybill == "")
											<a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('orders.destroy', $order->id)}}" title="{{ translate('Delete') }}">
                                               <i class="las la-trash"></i>
                                           </a>
										@else
											<a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm" onclick="show_cancel_order({{ $order->id }})" title="{{ translate('Cancel Order') }}">
											<span>X</span>
											</a>
										@endif
										
                                        <a href="javascript:void(0)" class="btn btn-soft-info btn-icon btn-circle btn-sm" onclick="show_purchase_history_details({{ $order->id }})" title="{{ translate('Order Details') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                            <i class="las la-download"></i>
                                        </a>
										
										@if($orderStatus != 'cancelled')
										<a href="javascript:void(0)" class="btn btn-soft-success btn-icon btn-circle btn-sm" onclick="show_edit_order({{ $order->id }}, {{ $waybill }})" title="{{ translate('Edit Order') }}">
											<i class="las la-edit"></i>
                                        </a>
										@endif
										
										<a href="javascript:void(0)" style="margin-top: 10px;" class="btn btn-soft-info btn-sm" onclick="show_order_track({{ $order->id }}, {{ $waybill }})" title="{{ translate('Track Order') }}">
                                            Track Order
                                        </a>
										
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    	{{ $orders->links() }}
              	</div>
            </div>
        @endif
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>
	
	<div class="modal fade" id="trackOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="trackOrder-modal-body">

                </div>
            </div>
        </div>
    </div>
	
	<div class="modal fade" id="editOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="editOrder-modal-body">

                </div>
            </div>
        </div>
    </div>
	
	<div id="cancel-order-modal" class="modal fade">
		<div class="modal-dialog modal-sm modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title h6">{{translate('Cancel Order')}}</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
				</div>
				<div class="modal-body text-center">
					<form class="" action="{{ route('purchase_history.cancel') }}" method="post">
						@csrf
						<input type="hidden" id="order_id" name="order_id" value="" />
						<p class="mt-1">{{ translate('Are you sure to cancel this order ?') }}</p>
						<button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('No')}}</button>
						<button type="submit" class="btn btn-primary mt-2">{{ translate('Yes') }}</button>
						
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection 

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
		
		function show_cancel_order(order_id)
        {
			//console.log(order_id);
			$('#order_id').val(order_id);
            $('#cancel-order-modal').modal();
        }
    </script>

@endsection
