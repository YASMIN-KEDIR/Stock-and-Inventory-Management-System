<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Low Stock / Out of Stock Filter
        if ($request->input('filter') === 'low_stock') {
            $query->where('current_stock', '<=', DB::raw('minimum_stock_level'));
        } elseif ($request->input('filter') === 'out_of_stock') {
            $query->where('current_stock', '<=', 0);
        }

        $products = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name', 'asc')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode'],
            'unit' => ['nullable', 'string', 'max:50'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'current_stock' => ['nullable', 'integer'],
            'minimum_stock_level' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Auto-fill friendly defaults if empty
        if (empty($validated['sku'])) {
            $validated['sku'] = 'SKU-' . strtoupper(Str::random(6));
            // Ensure uniqueness
            while (Product::where('sku', $validated['sku'])->exists()) {
                $validated['sku'] = 'SKU-' . strtoupper(Str::random(6));
            }
        }

        $validated['unit'] = $validated['unit'] ?? 'Pcs';
        $validated['cost_price'] = $validated['cost_price'] ?? 0.00;
        $validated['selling_price'] = $validated['selling_price'] ?? 0.00;
        $validated['current_stock'] = $validated['current_stock'] ?? 0;
        $validated['minimum_stock_level'] = $validated['minimum_stock_level'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            // Record initial stock movement if starting stock != 0
            if ($product->current_stock != 0) {
                StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'ADJUSTMENT_ADD',
                    'quantity' => $product->current_stock,
                    'balance_before' => 0,
                    'balance_after' => $product->current_stock,
                    'reference_type' => 'InitialStockAudit',
                    'reference_id' => $product->id,
                    'reason' => 'Product initial opening stock.',
                ]);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'PRODUCT_CREATE',
                'entity_type' => 'Product',
                'entity_id' => $product->id,
                'new_values' => $product->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return $product;
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'supplier']);
        $recentTransactions = $product->stockTransactions()->with('user')->latest()->take(10)->get();

        return view('products.show', compact('product', 'recentTransactions'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name', 'asc')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name', 'asc')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,'.$product->id],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,'.$product->id],
            'unit' => ['nullable', 'string', 'max:50'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'minimum_stock_level' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['unit'] = $validated['unit'] ?? $product->unit ?? 'Pcs';
        $validated['cost_price'] = $validated['cost_price'] ?? $product->cost_price ?? 0.00;
        $validated['selling_price'] = $validated['selling_price'] ?? $product->selling_price ?? 0.00;
        $validated['minimum_stock_level'] = $validated['minimum_stock_level'] ?? $product->minimum_stock_level ?? 0;
        $validated['is_active'] = $request->has('is_active') ? (bool)$request->input('is_active') : true;

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $oldValues = $product->toArray();
        $product->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_UPDATE',
            'entity_type' => 'Product',
            'entity_id' => $product->id,
            'old_values' => $oldValues,
            'new_values' => $product->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        if ($product->saleItems()->count() > 0 || $product->purchaseItems()->count() > 0) {
            return back()->with('error', 'Cannot delete product with existing commercial transactions. Set product as Inactive instead.');
        }

        $oldValues = $product->toArray();
        $product->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'PRODUCT_DELETE',
            'entity_type' => 'Product',
            'entity_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
