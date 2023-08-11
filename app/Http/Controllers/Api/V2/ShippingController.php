<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\AddressCollection;
use App\Models\Cart;
use App\Models\City;
use App\Models\PickupPoint;
use App\Models\Product;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function pickup_list()
    {
        $pickup_point_list = PickupPoint::where('pick_up_status', '=', 1)->get();
        return response()->json(['result' => true, 'pickup_points' => $pickup_point_list], 200);
    }

    public function shipping_cost(Request $request)
    {
        $carts = Cart::where('user_id', $request->user_id)
            ->get();

        foreach ($carts as $key => $cartItem) {
            $cartItem['shipping_cost'] = 0;
            
            if($request->shipping_type == 'pickup_point') {
                $cartItem['shipping_type'] = 'pickup_point';
                $cartItem['pickup_point'] = $request->pickup_point_id;
                $cartItem['address_id'] = 0;
            }
            if ($request->shipping_type == 'home_delivery') {
                $cartItem['shipping_type'] = 'home_delivery';
                $cartItem['pickup_point'] = 0;
                $cartItem['address_id'] = $request->address_id;
                $cartItem['shipping_cost'] = getShippingCost($carts, $key);
            }

            $cartItem->save();
        }

        //Total shipping cost $calculate_shipping

        $total_shipping_cost = Cart::where('user_id', $request->user_id)->sum('shipping_cost');

        return response()->json(['result' => true, 'shipping_type' => get_setting('shipping_type'), 'value' => convert_price($total_shipping_cost), 'value_string' => format_price($total_shipping_cost)], 200);
    }
}
