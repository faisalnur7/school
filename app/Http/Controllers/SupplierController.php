<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePermission('manage_inventory_suppliers');

        $query = Supplier::query()->orderBy('name');
        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('company_name', 'like', "%{$q}%");
            });
        }

        $suppliers = $query->paginate(20)->withQueryString();
        return view('pages.inventory.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $this->authorizePermission('manage_inventory_suppliers');
        return view('pages.inventory.suppliers.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_suppliers');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['status'] = (bool)($validated['status'] ?? true);
        Supplier::create($validated);

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier created successfully');
    }

    public function edit($id)
    {
        $this->authorizePermission('manage_inventory_suppliers');

        $supplier = Supplier::findOrFail($id);
        return view('pages.inventory.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $this->authorizePermission('manage_inventory_suppliers');

        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'boolean'],
        ]);

        $validated['status'] = (bool)($validated['status'] ?? false);
        $supplier->update($validated);

        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier updated successfully');
    }

    public function destroy($id)
    {
        $this->authorizePermission('manage_inventory_suppliers');

        $supplier = Supplier::withCount('purchaseOrders')->findOrFail($id);
        if ($supplier->purchase_orders_count > 0) {
            return redirect()->route('inventory.suppliers.index')->with('error', 'Cannot delete a supplier with purchases');
        }

        $supplier->delete();
        return redirect()->route('inventory.suppliers.index')->with('success', 'Supplier deleted successfully');
    }
}
