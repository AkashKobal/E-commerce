<?php

namespace App\Http\Controllers\Api\V2;

use App\ClubPoint;
use App\Http\Resources\V2\ClubpointCollection;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class ClubpointController extends Controller
{

    public function get_list($id)
    {
        $club_points = ClubPoint::where('user_id', $id)->latest()->paginate(10);

        return new ClubpointCollection($club_points);
    }

    public function convert_into_wallet(Request $request)
    {
        $club_point_convert_rate = get_setting( 'club_point_convert_rate');
        $club_point = ClubPoint::find($request->id);
        $wallet = new Wallet;
        $wallet->user_id = $request->user_id;
        $wallet->amount = floatval($club_point->points / $club_point_convert_rate);
        $wallet->payment_method = 'Club Point Convert';
        $wallet->payment_details = 'Club Point Convert';
        $wallet->save();
        $user = User::find($request->user_id);
        $user->balance = $user->balance + floatval($club_point->points / $club_point_convert_rate);
        $user->save();
        $club_point->convert_status = 1;
        $club_point->save();

        return response()->json([
            'success' => true,
            'message' => translate('Successfully converted')
        ]);
    }

}
