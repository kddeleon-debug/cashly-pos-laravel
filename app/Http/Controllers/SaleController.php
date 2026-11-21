<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Http\Requests\SaleRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private SaleService $sales) {}

    /** Sales History */
    public function index(Request $request): View
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $term = $request->string('q')->trim();

        $query = Sale::query()
            ->with(['product', 'customer', 'seller'])
            ->when($term->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w
                ->whereHas('product', fn ($p) => $p->where('name', 'like', "%{$term}%"))
                ->orWhereHas('customer', fn ($c) => $c->where('full_name', 'like', "%{$term}%"))
                ->orWhereHas('seller', fn ($s) => $s->where('full_name', 'like', "%{$term}%"))))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));

        $totalRevenue = (clone $query)->sum('total_price');
        $totalUnits = (clone $query)->sum('quantity');

        $sales = $query->latest('created_at')->latest('id')->paginate(15)->withQueryString();

        return view('sales.index', compact('sales', 'totalRevenue', 'totalUnits'));
    }

    /** Record Sale page */
    public function create(): View
    {
        return view('sales.create', [
            'products' => Product::orderBy('name')->get(),
            'customers' => Customer::orderBy('full_name')->get(),
        ]);
    }

    public function store(SaleRequest $request): RedirectResponse
    {
        try {
            $sale = $this->sales->record(
                (int) $request->integer('product_id'),
                $request->filled('customer_id') ? (int) $request->integer('customer_id') : null,
                (int) Auth::id(),
                (int) $request->integer('quantity'),
            );
        } catch (InsufficientStockException $e) {
            throw ValidationException::withMessages(['quantity' => $e->getMessage()]);
        }

        $sale->load('product');

        return redirect()->route('sales.create')->with(
            'success',
            "Sale recorded: {$sale->quantity} × {$sale->product->name} for ".peso($sale->total_price).'.'
        );
    }
}
