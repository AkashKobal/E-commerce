<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $guarded = [];
	 protected $table = 'delhivery_response';
    protected $fillable = ['order_id','user_id','upload_wbn','response_status','client','sort_code','waybill','cod_amount','payment','serviceable','refnum','owner_id','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
