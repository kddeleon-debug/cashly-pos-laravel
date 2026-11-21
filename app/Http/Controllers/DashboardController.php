<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'productCount' => Product::count(),
            'customerCount' => Customer::count(),
            'staffCount' => User::count(),
            'salesToday' => Sale::whereDate('created_at', today())->count(),
            'revenueToday' => Sale::whereDate('created_at', today())->sum('total_price'),
            'revenueTotal' => Sale::sum('total_price'),
            'lowStock' => Product::where('stock_quantity', '<=', Product::LOW_STOCK_THRESHOLD)->orderBy('stock_quantity')->limit(6)->get(),
            'recentSales' => Sale::with(['product', 'customer', 'seller'])->latest('created_at')->latest('id')->limit(6)->get(),
        ]);
    }
}
