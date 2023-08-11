<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\BusinessSetting;
use App\Seller;
use Session;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\WalletController;
use PaytmWallet;
use Auth;
use Redirect;

class PaytmController extends Controller
{
    public function index(){
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $order = Order::findOrFail(Session::get('order_id'));
                $amount = $order->grand_total;

                $payment = PaytmWallet::with('receive');
                $payment->prepare([
					'order' => $order->id,
					'user' => $order->code,
					'mobile_number' => Auth::user()->phone,
					'email' => Auth::user()->email,
					'amount' => $amount,
					'callback_url' => route('paytm.callback')
                ]);
                return $payment->receive();
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                if(Auth::user()->phone != null){
                    $amount= Session::get('payment_data')['amount'];
                    $payment = PaytmWallet::with('receive');
                    $payment->prepare([
                      'order' => rand(10000,99999),
                      'user' => Auth::user()->id,
                      'mobile_number' => Auth::user()->phone,
                      'email' => Auth::user()->email,
                      'amount' => $amount,
                      'callback_url' => route('paytm.callback')
                    ]);
                    return $payment->receive();
                }
                else {
                    flash('Please add phone number to your profile')->warning();
                    return back();
                }
            }
        }
    }

    public function callback(Request $request){
        $transaction = PaytmWallet::with('receive');

        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm

        if($transaction->isSuccessful()){
            if($request->session()->has('payment_type')){
                if($request->session()->get('payment_type') == 'cart_payment'){
                    $checkoutController = new CheckoutController;
                    return $checkoutController->checkout_done(Session::get('order_id'), json_encode($response));
                }
                elseif ($request->session()->get('payment_type') == 'wallet_payment') {
                    $walletController = new WalletController;
                    return $walletController->wallet_payment_done(Session::get('payment_data'), json_encode($response));
                }
            }
        }else if($transaction->isFailed()){
            $request->session()->forget('order_id');
            $request->session()->forget('payment_data');
            flash(translate('Payment cancelled'))->error();
        	return back();
        }else if($transaction->isOpen()){
          //Transaction Open/Processing
        }
        $transaction->getResponseMessage(); //Get Response Message If Available
        //get important parameters via public methods
        $transaction->getOrderId(); // Get order id
        $transaction->getTransactionId(); // Get transaction id
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function credentials_index()
    {
        return view('paytm.index');
    }

    /**
     * Update the specified resource in .env
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_credentials(Request $request)
    {
        foreach ($request->types as $key => $type) {
                $this->overWriteEnvFile($type, $request[$type]);
        }

        flash("Settings updated successfully")->success();
        return back();
    }

    /**
    *.env file overwrite
    */
    public function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"'.trim($val).'"';
            if(is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0){
                file_put_contents($path, str_replace(
                    $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
                ));
            }
            else{
                file_put_contents($path, file_get_contents($path)."\r\n".$type.'='.$val);
            }
        }
    }
}
