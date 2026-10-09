<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        $customers = $query->withCount('sales')->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $customer = Customer::create($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CUSTOMER_CREATE',
            'entity_type' => 'Customer',
            'entity_id' => $customer->id,
            'new_values' => $customer->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer profile registered successfully.');
    }

    public function show(Customer $customer)
    {
        $sales = $customer->sales()->with('items.product')->latest()->paginate(10);
        $payments = $customer->payments()->with('sale')->latest()->take(10)->get();

        return view('customers.show', compact('customer', 'sales', 'payments'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');
        $oldValues = $customer->toArray();
        $customer->update($validated);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CUSTOMER_UPDATE',
            'entity_type' => 'Customer',
            'entity_id' => $customer->id,
            'old_values' => $oldValues,
            'new_values' => $customer->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer profile updated successfully.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        if ($customer->sales()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with existing sales invoices. Mark as inactive instead.');
        }

        $oldValues = $customer->toArray();
        $customer->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'CUSTOMER_DELETE',
            'entity_type' => 'Customer',
            'entity_id' => $oldValues['id'],
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }
}
