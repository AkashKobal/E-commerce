<?php       
			
//sensSMS function for OTP
if (!function_exists('order_create')) {
    function order_create($data)
    {       
		/*  
		exit(); 
		$rand = rand(000000,999999);
		*/  
		$requestdata = 'format=json&data={
		  "pickup_location": {
			"name": "'.$data['pickup_name'].'"
		  },
		  "shipments": [{
			"order": "'.$data['order_id'].'",
			"phone": "'.$data['phone'].'",
			"name": "'.$data['name'].'",
			"add": "'.$data['add'].'",
			"pin": "'.$data['pin'].'",
			"payment_mode": "'.$data['payment_mode'].'",
			"products_desc": "'.$data['products_desc'].'",
			"cod_amount": "'.$data['cod_amount'].'",
			"country": "'.$data['country'].'",
			"order_date": "'.$data['order_date'].'",
			"total_amount": "'.$data['total_amount'].'",
			"seller_add": "'.$data['seller_add'].'",
			"seller_name": "'.$data['seller_name'].'",
			"seller_inv": "'.$data['seller_inv'].'",
			"quantity": "'.$data['quantity'].'",
			"state":"'.$data['state'].'",
			"city": "'.$data['city'].'"
		  }]
		}'; 
	
	//$url = "https://staging-express.delhivery.com/api/cmu/create.json";	// Test URL
	$url = "https://track.delhivery.com/api/cmu/create.json";		// Live URL
	
    $ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestdata);
	//curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 5e03d0590c829a95875ea8c045f815ef3b0b78db','Content-Type:application/json'));	// Test Token
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 6a6edd9d0ca5c803e81cdd70dabff8c121c1cc67','Content-Type:application/json'));		// Live Token
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
	
    }
}

/* if (!function_exists('order_create')) {
    function order_create($data)
    {

	$url = "https://staging-express.delhivery.com/api/cmu/create.json";
    $ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestdata);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Token 5e03d0590c829a95875ea8c045f815ef3b0b78db','Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$result = curl_exec($ch);
	curl_close($ch);
	 $d = json_decode($result);
	echo "<pre>";
	print_r($d);
	echo "<pre>";
	
    }
} */

	function track_order_api($waybill) {
		//$auth_token = "5e03d0590c829a95875ea8c045f815ef3b0b78db";		// Test Token
		$auth_token = "6a6edd9d0ca5c803e81cdd70dabff8c121c1cc67";		// Live Token
		
		$ch = curl_init();
  
		//$url = "https://staging-express.delhivery.com/api/v1/packages/json/";		// Test URL
		$url = "https://track.delhivery.com/api/v1/packages/";		// Live URL
		
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
		
	}
	
	function check_pincode_avail($pincode) {
		//$auth_token = "5e03d0590c829a95875ea8c045f815ef3b0b78db";		// Test Token
		$auth_token = "6a6edd9d0ca5c803e81cdd70dabff8c121c1cc67";		// Live Token
		//$pincode = "400064";
		
		$ch = curl_init();
  
		//$url = "https://staging-express.delhivery.com/c/api/pin-codes/json/";		// test URL
		$url = "https://track.delhivery.com/c/api/pin-codes/json/";		// Live URL
		
		$dataArray = ['filter_codes' => $pincode, 'token' => $auth_token];
	  
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
		
	}
	
?>