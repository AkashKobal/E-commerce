<?php

namespace App\Http\Controllers;

use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use Session;
use App\Models\CombinedOrder;
use Auth;
use Exception;
use Rave as Flutterwave;

class FlutterwaveController extends Controller
{
    public function pay()
    {
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                return $this->initialize($combined_order->grand_total);
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                return $this->initialize(Session::get('payment_data')['amount']);
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package_id = Session::get('payment_data')['customer_package_id'];
                $package_details = CustomerPackage::findOrFail($customer_package_id);
                return $this->initialize($package_details->amount);
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package_id = Session::get('payment_data')['seller_package_id'];
                $package_details = SellerPackage::findOrFail($seller_package_id);
                return $this->initialize($package_details->amount);
            }
        }
    }

    public function initialize($amount)
    {
        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $amount,
            'email' => Auth::user()->email,
            'tx_ref' => $reference,
            'currency' => env('FLW_PAYMENT_CURRENCY_CODE'),
            'redirect_url' => route('flutterwave.callback'),
            'customer' => [
                'email' => Auth::user()->email,
                "phone_number" => Auth::user()->phone,
                "name" => Auth::user()->name
            ],

            "customizations" => [
                "title" => 'Payment',
                "description" => ""
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return;
        }

        return redirect($payment['data']['link']);
    }

    /**
     * Obtain Rave callback information
     * @return void
     */
    public function callback()
    {
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            try{
                $payment = $data['data'];
                $payment_type = Session::get('payment_type');

                if($payment['status'] == "successful"){
                    if ($payment_type == 'cart_payment') {
                        $checkoutController = new CheckoutController;
                        return $checkoutController->checkout_done(session()->get('combined_order_id'), json_encode($payment));
                    }

                    if ($payment_type == 'wallet_payment') {
                        $walletController = new WalletController;
                        return $walletController->wallet_payment_done(session()->get('payment_data'), json_encode($payment));
                    }

                    if ($payment_type == 'customer_package_payment') {
                        $customer_package_controller = new CustomerPackageController;
                        return $customer_package_controller->purchase_payment_done(session()->get('payment_data'), json_encode($payment));
                    }

                    if ($payment_type == 'seller_package_payment') {
                        $seller_package_controller = new SellerPackageController;
                        return $seller_package_controller->purchase_payment_done(session()->get('payment_data'), json_encode($payment));
                    }
                }
            }
            catch(Exception $e){
                //dd($e);
            }
        }
        elseif ($status ==  'cancelled'){
            //Put desired action/code after transaction has been cancelled here
            flash(translate('Payment cancelled'))->error();
            return redirect()->route('home');
        }
        //Put desired action/code after transaction has failed here
        flash(translate('Payment failed'))->error();
        return redirect()->route('home');
    }
}
