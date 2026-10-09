<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $suppliers = $query->withCount('purchases')->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $supplier = Supplier::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_CREATE',
            'entity_type' => 'Supplier',
            'entity_id' => $supplier->id,
            'new_values' => $supplier->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier profile registered successfully.');
    }

    public function show(Supplier $supplier)
    {
        $purchases = $supplier->purchases()->with('items.product')->latest()->paginate(10);
        $payments = $supplier->payments()->with('purchase')->latest()->take(10)->get();

        return view('suppliers.show', compact('supplier', 'purchases', 'payments'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $oldValues = $supplier->toArray();
        $supplier->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_UPDATE',
            'entity_type' => 'Supplier',
            'entity_id' => $supplier->id,
            'old_values' => $oldValues,
            'new_values' => $supplier->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier)
    {
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'Cannot delete supplier with historical purchase records. Mark as inactive instead.');
        }

        $oldValues = $supplier->toArray();
        $supplier->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'SUPPLIER_DELETE',
            'entity_type' => 'Supplier',
            'entity_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
