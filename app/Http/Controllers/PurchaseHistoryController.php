<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Auth;
use DB;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(9);
        return view('frontend.user.purchase_history', compact('orders'));
    }

    public function digital_index()
    {
        $orders = DB::table('orders')
                        ->orderBy('code', 'desc')
                        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->join('products', 'order_details.product_id', '=', 'products.id')
                        ->where('orders.user_id', Auth::user()->id)
                        ->where('products.digital', '1')
                        ->where('order_details.payment_status', 'paid')
                        ->select('order_details.id')
                        ->paginate(15);
        return view('frontend.user.digital_purchase_history', compact('orders'));
    }

    public function purchase_history_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.user.order_details_customer', compact('order'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	
	//Track Order
	public function track_order(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
		
		if($order){
			//echo $order->id;
			$delihveryData = DB::table('delhivery_response')->where('order_id', $order->id)->select('*')->first();
			
			$waybill = $delihveryData->waybill;
			//$auth_token = "5e03d0590c829a95875ea8c045f815ef3b0b78db";
			
			//$response = $this->track_order_api($waybill,$auth_token);
			$response = track_order_api($waybill);
			$response = json_decode($response);
				
			return view('frontend.user.track_customer_order', compact('response','order'));
			
		}
    }
	
	/* public function track_order_api($waybill,$auth_token) {
		$ch = curl_init();
  
		$url = "https://staging-express.delhivery.com/api/v1/packages/json/";
		$dataArray = ['waybill' => $waybill, 'token' => $auth_token];
	  
		$data = http_build_query($dataArray);
	  
		$getUrl = $url."?".$data;
	  
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $getUrl);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 80);
		   
		$response = curl_exec($ch);
		if(curl_error($ch)){
			//echo 'Request Error:' . curl_error($ch);
			return false;
		} else {
			//echo $response;
			return $response;
		}
		   
		curl_close($ch);
		
	} */
	
	public function edit_order(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
		
		if($order){
			//echo $order->id;
			$delihveryData = DB::table('delhivery_response')->where('order_id', $order->id)->select('*')->first();
			
			$waybill = $delihveryData->waybill;
			//$auth_token = "5e03d0590c829a95875ea8c045f815ef3b0b78db";
			
			//$response = $this->track_order_api($waybill,$auth_token);
			$response = track_order_api($waybill);
			$response = json_decode($response);
				
			return view('frontend.user.edit_customer_order', compact('response','order'));
			
		}
    }
	
	public function update_order(Request $request)
    {
		$order_id = $request->order_id;
		$orderData = DB::table('orders')->where('id', $order_id)->select('*')->first();
		$delihveryData = DB::table('delhivery_response')->where('order_id', $order_id)->select('*')->first();
		
		$waybill = $delihveryData->waybill;
		$name = $request->name;
		$phone = $request->phone;
		$address = $request->address;
		
		$postData = '{
		  "waybill": "'.$waybill.'",
		  "phone": "'.$phone.'",
		  "name": "'.$name.'",
		  "add": "'.$address.'"
		}'; 
		//echo $postData;
		
		//$url = "https://staging-express.delhivery.com/api/p/edit";		// Test URL
		$url = "https://track.delhivery.com/api/p/edit";		// Live URL
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData);
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 5e03d0590c829a95875ea8c045f815ef3b0b78db','Content-Type:application/json'));		// Test Token
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 6a6edd9d0ca5c803e81cdd70dabff8c121c1cc67','Content-Type:application/json'));		// Live Token
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec($ch);
		//print_r($result);
		//echo $result
		if(curl_error($ch)){
			//echo 'Request Error:' . curl_error($ch);
			flash(translate('Request Error'))->error();
		} else {
			$shippingAddress = json_decode($orderData->shipping_address);
			
			$shipData = array(
				'name' => $name,
				'email' => $shippingAddress->email,
				'address' => $address,
				'country' => $shippingAddress->country,
				'state' => $shippingAddress->state,
				'city' => $shippingAddress->city,
				'postal_code' => $shippingAddress->postal_code,
				'phone' => $phone
			);
			//print_r($shipData);
			
			$update['shipping_address'] = json_encode($shipData);
			//print_r($update);
			$updateid = DB::table('orders')->where('id', $order_id)->update($update);

			if($updateid > 0){
				flash(translate('Your order has been updated'))->success();
				return redirect()->route('purchase_history.index');
			}
			else{
				flash(translate('Something went wrong'))->error();
			}
		}
		curl_close($ch);
    }
	
	public function cancel_order(Request $request){
		$order_id = $request->order_id;
		$delihveryData = DB::table('delhivery_response')->where('order_id', $order_id)->select('*')->first();
		$waybill = $delihveryData->waybill;
		//echo $waybill;
		
		$postData = '{
			"waybill": "'.$waybill.'",
			"cancellation": "true"
		}'; 
		//echo $postData;
		
		//$url = "https://staging-express.delhivery.com/api/p/edit";		// Test URL
		$url = "https://track.delhivery.com/api/p/edit";		// Live URL
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData);
		//curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 5e03d0590c829a95875ea8c045f815ef3b0b78db','Content-Type:application/json'));		//Test Token
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 6a6edd9d0ca5c803e81cdd70dabff8c121c1cc67','Content-Type:application/json'));		//Live Token
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec($ch);
		//print_r($result);
		//echo $result;
		if(curl_error($ch)){
			//echo 'Request Error:' . curl_error($ch);
			flash(translate('Request Error'))->error();
		} else {
			$updateDelhivery['order_status'] = "cancelled";
			$updateDelhivery['updated_at'] = date('Y-m-d H:i:s');
			$resDelhivery = DB::table('delhivery_response')->where('order_id', $order_id)->update($updateDelhivery);
			
			$updateOrder['delivery_status'] = "cancelled";
			$updateOrder['updated_at'] = date('Y-m-d H:i:s');
			$resOrder = DB::table('orders')->where('id', $order_id)->update($updateOrder);
			
			$updateOrderDetails['delivery_status'] = "cancelled";
			$updateOrderDetails['updated_at'] = date('Y-m-d H:i:s');
			$resOrderDetails = DB::table('order_details')->where('id', $order_id)->update($updateOrderDetails);
			
			if($resDelhivery > 0 && $resOrder > 0 && $resOrderDetails > 0){
				
				flash(translate('Your order has been cancelled !!'))->success();
				return redirect()->route('purchase_history.index');
			}
			else{
				flash(translate('Something went wrong !!'))->error();
			} 
		}
		curl_close($ch);
	}
	
}
