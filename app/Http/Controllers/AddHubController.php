<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AddHubController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'product');
        if (!in_array($tab, ['product', 'supplier', 'customer'])) {
            $tab = 'product';
        }

        $suppliers = Supplier::where('is_active', true)->orderBy('name', 'asc')->get();

        // Recent items for quick preview & confirmation
        $recentProducts = Product::with('supplier')->latest()->take(5)->get();
        $recentSuppliers = Supplier::latest()->take(5)->get();
        $recentCustomers = Customer::latest()->take(5)->get();

        return view('add.index', compact(
            'tab',
            'suppliers',
            'recentProducts',
            'recentSuppliers',
            'recentCustomers'
        ));
    }
}
