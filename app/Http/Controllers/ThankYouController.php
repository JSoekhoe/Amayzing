<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Mollie\Api\MollieApiClient;

class ThankYouController extends Controller
{
    public function index()
    {
        return view('checkout.thankyou');
    }
}

