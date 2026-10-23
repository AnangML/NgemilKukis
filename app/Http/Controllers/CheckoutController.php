<?php

namespace App\Http\Controllers;

use App\Models\GreetingCardTemplate;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display the checkout initialization view.
     */
    public function index(): View
    {
        $greetingCards = GreetingCardTemplate::query()
            ->active()
            ->get();

        return view('shop.checkout', [
            'greetingCards' => $greetingCards,
        ]);
    }
}
