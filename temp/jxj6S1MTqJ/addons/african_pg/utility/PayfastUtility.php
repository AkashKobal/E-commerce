<?php

namespace App\Utility;

class PayfastUtility
{
    public static function is_sandbox()
    {
        return \App\Models\BusinessSetting::where('type', 'payfast_sandbox')->first()->value == 1;
    }

    // 'sandbox' or 'live' | default live
    public static function action_url($mode = 'sandbox')
    {
        return $mode == 'sandbox' ? 'https://sandbox.payfast.co.za/eng/process' : 'https://www.payfast.co.za/eng/process';
    }

    // 'sandbox' or 'live' | default live
    public static function get_action_url()
    {
        return PayfastUtility::is_sandbox() ? PayfastUtility::action_url('sandbox') : PayfastUtility::action_url('live');
    }

    public static function create_checkout_form($order_id, $amount)
    {
        $data = array(
            // Merchant details
            'merchant_id' => env('PAYFAST_MERCHANT_ID'),
            'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
            'return_url' => route('payfast.checkout.return'),
            'cancel_url' => route('payfast.checkout.cancel'),
            'notify_url' => route('payfast.checkout.notify'),
            // Transaction details
            'amount' => number_format(sprintf('%.2f', $amount), 2, '.', ''),
            'item_name' => translate("Checkout Payment"),
            // Custom Parametera
            'custom_str1' => $order_id,
            'custom_str2' => "",
            'custom_str3' => "",
            'custom_str4' => "",
            'custom_str5' => "",
        );

        $signature = PayfastUtility::getSignature($data);
        $data['signature'] = $signature;

        return view('frontend.payfast.form', $data);
    }

    public static function create_wallet_form($user_id, $amount)
    {
        $data = array(
            // Merchant details
            'merchant_id' => env('PAYFAST_MERCHANT_ID'),
            'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
            'return_url' => route('payfast.wallet.return'),
            'cancel_url' => route('payfast.wallet.cancel'),
            'notify_url' => route('payfast.wallet.notify'),
            // Transaction details
            'amount' => number_format(sprintf('%.2f', $amount), 2, '.', ''),
            'item_name' => translate("Wallet Recharge Payment"),
            // Custom Parametera
            'custom_str1' => $user_id,
            'custom_str2' => "",
            'custom_str3' => "",
            'custom_str4' => "",
            'custom_str5' => "",
        );

        $signature = PayfastUtility::getSignature($data);
        $data['signature'] = $signature;

        return view('frontend.payfast.form', $data);
    }

    public static function create_customer_package_form($user_id, $package_id, $amount)
    {
        $data = array(
            // Merchant details
            'merchant_id' => env('PAYFAST_MERCHANT_ID'),
            'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
            'return_url' => route('payfast.customer_package_payment.return'),
            'cancel_url' => route('payfast.customer_package_payment.cancel'),
            'notify_url' => route('payfast.customer_package_payment.notify'),
            // Transaction details
            'amount' => number_format(sprintf('%.2f', $amount), 2, '.', ''),
            'item_name' => translate("Customer Package Payment"),
            // Custom Parametera
            'custom_str1' => $user_id,
            'custom_str2' => $package_id,
            'custom_str3' => "",
            'custom_str4' => "",
            'custom_str5' => "",
        );

        $signature = PayfastUtility::getSignature($data);
        $data['signature'] = $signature;


        return view('frontend.payfast.form', $data);
    }

    public static function create_seller_package_form($user_id, $package_id, $amount)
    {
        $data = array(
            // Merchant details
            'merchant_id' => env('PAYFAST_MERCHANT_ID'),
            'merchant_key' => env('PAYFAST_MERCHANT_KEY'),
            'return_url' => route('payfast.seller_package_payment.return'),
            'cancel_url' => route('payfast.seller_package_payment.cancel'),
            'notify_url' => route('payfast.seller_package_payment.notify'),
            // Transaction details
            'amount' => number_format(sprintf('%.2f', $amount), 2, '.', ''),
            'item_name' => translate("Seller Package Payment"),
            // Custom Parametera
            'custom_str1' => $user_id,
            'custom_str2' => $package_id,
            'custom_str3' => "",
            'custom_str4' => "",
            'custom_str5' => "",
        );

        $signature = PayfastUtility::getSignature($data);
        $data['signature'] = $signature;


        return view('frontend.payfast.form', $data);
    }


    public static function getSignature($data, $passPhrase = null)
    {
        // Create parameter string
        $pfOutput = '';
        foreach( $data as $key => $val ) {
            if(!empty($val)) {
                $pfOutput .= $key .'='. urlencode( trim( $val ) ) .'&';
            }
        }
        // Remove last ampersand
        $getString = substr( $pfOutput, 0, -1 );
        if( $passPhrase !== null ) {
            $getString .= '&passphrase='. urlencode( trim( $passPhrase ) );
        }
        return md5( $getString );
    }

    public static function pfValidSignature($pfData, $pfParamString, $pfPassphrase = null)
    {
        // Calculate security signature
        if ($pfPassphrase === null) {
            $tempParamString = $pfParamString;
        } else {
            $tempParamString = $pfParamString . '&passphrase=' . urlencode($pfPassphrase);
        }

        $signature = md5($tempParamString);
        return ($pfData['signature'] === $signature);
    }

    public static function pfValidIP()
    {
        // Variable initialization
        $validHosts = array(
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        );

        $validIps = [];

        foreach ($validHosts as $pfHostname) {
            $ips = gethostbynamel($pfHostname);

            if ($ips !== false)
                $validIps = array_merge($validIps, $ips);
        }

        // Remove duplicates
        $validIps = array_unique($validIps);
        $referrerIp = gethostbyname(parse_url($_SERVER['HTTP_REFERER'])['host']);
        if (in_array($referrerIp, $validIps, true)) {
            return true;
        }
        return false;
    }

    public static function pfValidPaymentData($cartTotal, $pfData)
    {
        return !(abs((float)$cartTotal - (float)$pfData['amount_gross']) > 0.01);
    }

    function pfValidServerConfirmation($pfParamString, $pfHost = 'sandbox.payfast.co.za', $pfProxy = null)
    {
        // Use cURL (if available)
        if (in_array('curl', get_loaded_extensions(), true)) {
            // Variable initialization
            $pfHost = PayfastUtility::is_sandbox() ? 'sandbox.payfast.co.za' : 'payfast.co.za';
            $url = 'https://' . $pfHost . '/eng/query/validate';

            // Create default cURL object
            $ch = curl_init();

            // Set cURL options - Use curl_setopt for greater PHP compatibility
            // Base settings
            curl_setopt($ch, CURLOPT_USERAGENT, NULL);  // Set user agent
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // Return output as string rather than outputting it
            curl_setopt($ch, CURLOPT_HEADER, false);             // Don't include header in output
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            // Standard settings
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pfParamString);
            if (!empty($pfProxy))
                curl_setopt($ch, CURLOPT_PROXY, $pfProxy);

            // Execute cURL
            $response = curl_exec($ch);
            curl_close($ch);
            if ($response === 'VALID') {
                return true;
            }
        }
        return false;
    }

    public static function check($pfData)
    {
        // Strip any slashes in data
        foreach ($pfData as $key => $val) {
            $pfData[$key] = stripslashes($val);
        }

        // Convert posted variables to a string
        $pfParamString = "";
        foreach ($pfData as $key => $val) {
            if ($key !== 'signature') {
                $pfParamString .= $key . '=' . urlencode($val) . '&';
            } else {
                break;
            }
        }

        $pfParamString = substr($pfParamString, 0, -1);

        $pfHost = PayfastUtility::is_sandbox() ? 'sandbox.payfast.co.za' : 'payfast.co.za';
        $check1 = PayfastUtility::pfValidSignature($pfData, $pfParamString);
        $check2 = PayfastUtility::pfValidIP();
        //$check3 = PayfastUtility::pfValidPaymentData($cartTotal, $pfData); //ignore check 3
        $check4 = PayfastUtility::pfValidServerConfirmation($pfParamString, $pfHost);

        return $check1 && $check2 && $check4;

    }


}
