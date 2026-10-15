<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class UsersHubController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'customers');
        if (!in_array($tab, ['customers', 'suppliers'])) {
            $tab = 'customers';
        }

        // Customer query
        $customerSearch = $request->input('customer_search', $request->input('search'));
        $customersQuery = Customer::query();
        if ($customerSearch) {
            $customersQuery->where(function ($q) use ($customerSearch) {
                $q->where('name', 'like', "%{$customerSearch}%")
                  ->orWhere('phone', 'like', "%{$customerSearch}%")
                  ->orWhere('email', 'like', "%{$customerSearch}%");
            });
        }
        $customers = $customersQuery->withCount('sales')->orderBy('name', 'asc')->paginate(12, ['*'], 'customers_page')->withQueryString();

        // Supplier query
        $supplierSearch = $request->input('supplier_search', $request->input('search'));
        $suppliersQuery = Supplier::query();
        if ($supplierSearch) {
            $suppliersQuery->where(function ($q) use ($supplierSearch) {
                $q->where('name', 'like', "%{$supplierSearch}%")
                  ->orWhere('company_name', 'like', "%{$supplierSearch}%")
                  ->orWhere('phone', 'like', "%{$supplierSearch}%")
                  ->orWhere('email', 'like', "%{$supplierSearch}%");
            });
        }
        $suppliers = $suppliersQuery->withCount('purchases')->orderBy('name', 'asc')->paginate(12, ['*'], 'suppliers_page')->withQueryString();

        $totalCustomersCount = Customer::count();
        $customersWithDebtCount = Customer::where('outstanding_balance', '>', 0)->count();
        $totalCustomerDebt = (float) Customer::sum('outstanding_balance');

        $totalSuppliersCount = Supplier::count();
        $suppliersWithPayablesCount = Supplier::where('outstanding_balance', '>', 0)->count();
        $totalSupplierDebt = (float) Supplier::sum('outstanding_balance');

        return view('users.index', compact(
            'tab',
            'customers',
            'suppliers',
            'totalCustomersCount',
            'customersWithDebtCount',
            'totalCustomerDebt',
            'totalSuppliersCount',
            'suppliersWithPayablesCount',
            'totalSupplierDebt'
        ));
    }
}
