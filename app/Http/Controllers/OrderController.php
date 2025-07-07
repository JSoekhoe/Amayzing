<?php

namespace App\Http\Controllers;

class OrderController extends Controller
{
    // Optioneel: alleen om een checkout pagina te tonen als je wilt
    public function checkout()
    {
        return view('checkout');
    }
}
