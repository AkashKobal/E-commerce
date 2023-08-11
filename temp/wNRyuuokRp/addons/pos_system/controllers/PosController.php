<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OtpConfiguration;
use App\Models\BusinessSetting;
use App\Models\OrderDetail;
use App\Models\ProductStock;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\Color;
use App\Models\City;
use App\Models\User;
use App\Models\Address;
use App\Models\Addon;
use Session;
use Auth;
use DB;
use PDF;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Http\Resources\PosProductCollection;
use App\Utility\CategoryUtility;

class PosController extends Controller
{
    public function index()
    {
        if (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('pos.index');
        }
        else {
            $pos_activation = BusinessSetting::where('type', 'pos_activation_for_seller')->first();
            if ($pos_activation != null && $pos_activation->value == 1) {
                return view('pos.frontend.seller.pos.index');
            }
            else {
                flash(translate('POS is disable for Sellers!!!'))->error();
                return back();
            }
        }
    }

    public function search(Request $request)
    {
        if(Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff'){
            $products = ProductStock::join('products','product_stocks.product_id', '=', 'products.id')->where('products.added_by', 'admin')->select('products.*','product_stocks.id as stock_id','product_stocks.variant','product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image')->orderBy('products.created_at', 'desc');
            // $products = Product::where('added_by', 'admin')->where('published', '1');
        }
        else {
            $products = ProductStock::join('products','product_stocks.product_id', '=', 'products.id')->where('user_id', Auth::user()->id)->where('published', '1')->select('products.*','product_stocks.id as stock_id','product_stocks.variant','product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image')->orderBy('products.created_at', 'desc');
            // $products = Product::where('user_id', Auth::user()->id)->where('published', '1');
        }

        if($request->category != null){
            $arr = explode('-', $request->category);
            if($arr[0] == 'category'){
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $products = $products->whereIn('products.category_id', $category_ids);
            }
        }

        if($request->brand != null){
            $products = $products->where('products.brand_id', $request->brand);
        }

        if ($request->keyword != null) {
            $products = $products->where('products.name', 'like', '%'.$request->keyword.'%')->orWhere('products.barcode', $request->keyword);
        }

        /*$p = $products->get();

        dd($p);*/

        $stocks = new PosProductCollection($products->paginate(16));
        $stocks->appends(['keyword' =>  $request->keyword,'category' => $request->category, 'brand' => $request->brand]);
        return $stocks;
    }

    public function addToCart(Request $request)
    {
        $stock = ProductStock::find($request->stock_id);
        $product = $stock->product;

        $data = array();
        $data['stock_id'] = $request->stock_id;
        $data['id'] = $product->id;
        $data['variant'] = $stock->variant;
        $data['quantity'] = $product->min_qty;

        if($stock->qty < $product->min_qty){
            return array('success' => 0, 'message' => translate("This product doesn't have enough stock for minimum purchase quantity ").$product->min_qty, 'view' => view('pos.cart')->render());
        }

        $tax = 0;
        $price = $stock->price;

        // discount calculation
        $discount_applicable = false;
        if ($product->discount_start_date == null) {
            $discount_applicable = true;
        }
        elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
            strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
            $discount_applicable = true;
        }
        if ($discount_applicable) {
            if($product->discount_type == 'percent'){
                $price -= ($price*$product->discount)/100;
            }
            elseif($product->discount_type == 'amount'){
                $price -= $product->discount;
            }
        }

        //tax calculation
        foreach ($product->taxes as $product_tax) {
            if($product_tax->tax_type == 'percent'){
                $tax += ($price * $product_tax->tax) / 100;
            }
            elseif($product_tax->tax_type == 'amount'){
                $tax += $product_tax->tax;
            }
        }

        $data['price'] = $price;
        $data['tax'] = $tax;

        if($request->session()->has('pos.cart')){
            $foundInCart = false;
            $cart = collect();

            foreach ($request->session()->get('pos.cart') as $key => $cartItem){
                if($cartItem['id'] == $product->id && $cartItem['stock_id'] == $stock->id){
                    $foundInCart = true;
                    $loop_product = Product::find($cartItem['id']);
                    $product_stock = $loop_product->stocks->where('variant', $cartItem['variant'])->first();

                    if($product_stock->qty >= ($cartItem['quantity'] + 1)){
                        $cartItem['quantity'] += 1;
                    }else{
                        return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                    }
                }
                $cart->push($cartItem);
            }

            if (!$foundInCart) {
                $cart->push($data);
            }
            $request->session()->put('pos.cart', $cart);
        }
        else{
            $cart = collect([$data]);
            $request->session()->put('pos.cart', $cart);
        }

        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('pos.cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if($key == $request->key){
                $product = Product::find($object['id']);
                $product_stock = $product->stocks->where('id', $object['stock_id'])->first();

                if($product_stock->qty >= $request->quantity){
                    $object['quantity'] = $request->quantity;
                }else{
                    return array('success' => 0, 'message' => translate("This product doesn't have more stock."), 'view' => view('pos.cart')->render());
                }
            }
            return $object;
        });
        $request->session()->put('pos.cart', $cart);

        return array('success' => 1, 'message' => '', 'view' => view('pos.cart')->render());
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if(Session::has('pos.cart')){
            $cart = Session::get('pos.cart', collect([]));
            $cart->forget($request->key);
            Session::put('pos.cart', $cart);

            $request->session()->put('pos.cart', $cart);
        }

        return view('pos.cart');
    }

    //Shipping Address for admin
    public function getShippingAddress(Request $request){
        $user_id = $request->id;
        if($user_id == ''){
            return view('pos.guest_shipping_address');
        }
        else{
            return view('pos.shipping_address', compact('user_id'));
        }
    }

    //Shipping Address for seller
    public function getShippingAddressForSeller(Request $request){
        $user_id = $request->id;
        if($user_id == ''){
            return view('pos.frontend.seller.pos.guest_shipping_address');
        }
        else{
            return view('pos.frontend.seller.pos.shipping_address', compact('user_id'));
        }
    }

    public function set_shipping_address(Request $request) {
        if ($request->address_id != null) {
            $address = Address::findOrFail($request->address_id);
            $data['name'] = $address->user->name;
            $data['email'] = $address->user->email;
            $data['address'] = $address->address;
            $data['country'] = $address->country;
            $data['city'] = $address->city;
            $data['postal_code'] = $address->postal_code;
            $data['phone'] = $address->phone;
        } else {
            $data['name'] = $request->name;
            $data['email'] = $request->email;
            $data['address'] = $request->address;
            $data['country'] = $request->country;
            $data['city'] = $request->city;
            $data['postal_code'] = $request->postal_code;
            $data['phone'] = $request->phone;
        }

        $shipping_info = $data;
        $request->session()->put('pos.shipping_info', $shipping_info);
    }

    //set Discount
    public function setDiscount(Request $request){
        if($request->discount >= 0){
            Session::put('pos.discount', $request->discount);
        }
        return view('pos.cart');
    }

    //set Shipping Cost
    public function setShipping(Request $request){
        if($request->shipping != null){
            Session::put('pos.shipping', $request->shipping);
        }
        return view('pos.cart');
    }

    //order summary
    public function get_order_summary(Request $request){
        return view('pos.order_summary');
    }

    //order place
    public function order_store(Request $request){
        if(Session::has('pos.cart') && count(Session::get('pos.cart')) > 0){
            $order = new Order;
            $name = '';
            $email = '';
            $address = '';
            $country = '';
            $city = '';
            $postal_code = '';
            $phone = '';

            if ($request->user_id == null) {
                $order->guest_id    = mt_rand(100000, 999999);
                $name               = $request->name;
                $email              = $request->email;
                $address            = $request->address;
                $country            = $request->country;
                $city               = $request->city;
                $postal_code        = $request->postal_code;
                $phone              = $request->phone;
            }
            else {
                $order->user_id = $request->user_id;
                $user           = User::findOrFail($request->user_id);
                $name   = $user->name;
                $email  = $user->email;

                if($request->shipping_address != null){
                    $address_data   = Address::findOrFail($request->shipping_address);
                    $address        = $address_data->address;
                    $country        = $address_data->country;
                    $city           = $address_data->city;
                    $postal_code    = $address_data->postal_code;
                    $phone          = $address_data->phone;
                }
            }

            $data['name']           = $name;
            $data['email']          = $email;
            $data['address']        = $address;
            $data['country']        = $country;
            $data['city']           = $city;
            $data['postal_code']    = $postal_code;
            $data['phone']          = $phone;

            $order->shipping_address = json_encode($data);

            $order->payment_type = $request->payment_type;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His').rand(10,99);
            $order->date = strtotime('now');
            $order->payment_status = 'paid';
            $order->payment_details = $request->payment_type;

            $shipping_info = Session::get('pos.shipping_info');

            if($order->save()){
                $subtotal = 0;
                $tax = 0;
                foreach (Session::get('pos.cart') as $key => $cartItem){
                    $product_stock = ProductStock::find($cartItem['stock_id']);
                    $product = $product_stock->product;
                    $product_variation = $product_stock->variant;

                    $subtotal += $cartItem['price']*$cartItem['quantity'];
                    $tax += $cartItem['tax']*$cartItem['quantity'];


                    if($cartItem['quantity'] > $product_stock->qty){
                        $order->delete();
                        return array('success' => 0, 'message' => $product->name.' ('.$product_variation.') '.translate(" just stock outs."));
                    }
                    else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }

                    $order_detail = new OrderDetail;
                    $order_detail->order_id  =$order->id;
                    $order_detail->seller_id = $product->user_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->payment_status = 'paid';
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->shipping_type = null;

                    if (Session::get('pos.shipping', 0) >= 0){
                        $order_detail->shipping_cost = Session::get('pos.shipping', 0)/count(Session::get('pos.cart'));
                    }
                    else {
                        $order_detail->shipping_cost = 0;
                    }

                    $order_detail->save();

                    $product->num_of_sale++;
                    $product->save();
                }

                $order->grand_total = $subtotal + $tax + Session::get('pos.shipping', 0);

                if(Session::has('pos.discount')){
                    $order->grand_total -= Session::get('pos.discount');
                    $order->coupon_discount = Session::get('pos.discount');
                }

                $order->seller_id = $product->user_id;
                $order->save();

                $array['view'] = 'emails.invoice';
                $array['subject'] = 'Your order has been placed - '.$order->code;
                $array['from'] = env('MAIL_USERNAME');
                $array['order'] = $order;

                $admin_products = array();
                $seller_products = array();

                foreach ($order->orderDetails as $key => $orderDetail){
                    if($orderDetail->product->added_by == 'admin'){
                        array_push($admin_products, $orderDetail->product->id);
                    }
                    else{
                        $product_ids = array();
                        if(array_key_exists($orderDetail->product->user_id, $seller_products)){
                            $product_ids = $seller_products[$orderDetail->product->user_id];
                        }
                        array_push($product_ids, $orderDetail->product->id);
                        $seller_products[$orderDetail->product->user_id] = $product_ids;
                    }
                }

                foreach($seller_products as $key => $seller_product){
                    try {
                        Mail::to(User::find($key)->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {

                    }
                }

                //sends email to customer with the invoice pdf attached
                if(env('MAIL_USERNAME') != null){
                    try {
                        Mail::to($request->session()->get('pos.shipping_info')['email'])->queue(new InvoiceEmailManager($array));
                        Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new InvoiceEmailManager($array));
                    } catch (\Exception $e) {

                    }
                }

                if($request->user_id != NULL){
                    if (Addon::where('unique_identifier', 'club_point')->first() != null && Addon::where('unique_identifier', 'club_point')->first()->activated) {
                        $clubpointController = new ClubPointController;
                        $clubpointController->processClubPoints($order);
                    }
                }

                calculateCommissionAffilationClubPoint($order);

                Session::forget('pos.shipping_info');
                Session::forget('pos.shipping');
                Session::forget('pos.discount');
                Session::forget('pos.cart');
               return array('success' => 1, 'message' => translate('Order Completed Successfully.'));
            }
            else {
                return array('success' => 0, 'message' => translate('Please input customer information.'));
            }
        }
        return array('success' => 0, 'message' => translate("Please select a product."));
    }

    public function pos_activation()
    {
        $pos_activation = BusinessSetting::where('type', 'pos_activation_for_seller')->first();
        return view('pos.pos_activation', compact('pos_activation'));
    }
}
