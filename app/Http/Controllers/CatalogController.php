<?php

namespace App\Http\Controllers;

use App\Models\GreetingCardTemplate;
use App\Models\Product;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the catalog and bakery landing page.
     */
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->active()
            ->where('is_featured', true)
            ->get();

        $readyStockProducts = Product::query()
            ->active()
            ->readyStock()
            ->get();

        $preOrderProducts = Product::query()
            ->active()
            ->preOrder()
            ->get();

        $allProducts = Product::query()
            ->active()
            ->orderByDesc('is_featured')
            ->get();

        $greetingCards = GreetingCardTemplate::query()
            ->active()
            ->get();

        return view('shop.index', [
            'featuredProducts' => $featuredProducts,
            'readyStockProducts' => $readyStockProducts,
            'preOrderProducts' => $preOrderProducts,
            'allProducts' => $allProducts,
            'greetingCards' => $greetingCards,
        ]);
    }
}
