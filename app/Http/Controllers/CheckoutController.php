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

        $user = auth()->user();
        $savedAddresses = $user ? $user->addresses()->latest()->get() : collect();

        return view('shop.checkout', [
            'greetingCards' => $greetingCards,
            'customer' => $user,
            'savedAddresses' => $savedAddresses,
        ]);
    }
}
