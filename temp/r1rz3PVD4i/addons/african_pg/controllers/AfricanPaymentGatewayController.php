<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AfricanPaymentGatewayController extends Controller
{
    public function configuration()
    {
        return view('african_pg.configurations.activation');
    }

    public function credentials_index()
    {
        return view('african_pg.configurations.index');
    }
}
