

    <form style="display: none" method="POST" action="{{ \App\Utility\PayfastUtility::get_action_url() }}" id="payfast-checkout-form">
        <input type="hidden" name="merchant_id" value="{{ $merchant_id }}">
        <input type="hidden" name="merchant_key" value="{{ $merchant_key }}">
        <!-- Replace your Merchant ID -->
        <input type="hidden" name="return_url" value="{{ $return_url }}">
        <input type="hidden" name="cancel_url" value="{{ $cancel_url }}">
        <input type="hidden" name="notify_url" value="{{ $notify_url }}">
        <br><br>Custom Params<br>
        <input type="text" name="custom_str1" value="{{ $custom_str1 }}">
        <input type="text" name="custom_str2" value="{{ $custom_str2 }}">
        <input type="text" name="custom_str3" value="{{ $custom_str3 }}">
        <input type="text" name="custom_str4" value="{{ $custom_str4 }}">
        <input type="text" name="custom_str5" value="{{ $custom_str5 }}">
        <br><br>Item Details<br>
        <input type="text" name="item_name" value="{{ $item_name }}"><br>
        <input type="text" name="amount" value="{{ $amount }}">
        <input type="text" name="payment_method" value="">
        <input type="text" name="signature" value="{{ $signature }}">
        <input type="submit" value="Pay Now">

    </form>


    <script type="text/javascript">
       var payfast_checkout_form =  document.getElementById('payfast-checkout-form');
       payfast_checkout_form.submit();
    </script>
