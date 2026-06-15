<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Group;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderPayment;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    private function authorizePermission(string $permission): void
    {
        abort_if(!auth()->user()?->hasPermission($permission), 403);
    }

    public function index(Request $request)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $query = PurchaseOrder::with('supplier')->orderByDesc('purchase_date')->orderByDesc('id');
        if ($request->filled('q')) {
            $q = trim((string)$request->get('q'));
            $query->where('reference_no', 'like', "%{$q}%")
                ->orWhereHas('supplier', fn ($s) => $s->where('name', 'like', "%{$q}%"));
        }

        $purchases = $query->paginate(20)->withQueryString();
        return view('pages.inventory.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $this->authorizePermission('manage_inventory_purchases');

        $suppliers = Supplier::where('status', true)->orderBy('name')->get();
        $products = InventoryItem::with(['category', 'schoolClass', 'group'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $categories = $products->pluck('category')->filter()->unique('id')->sortBy('name')->values();
        $classes    = SchoolClass::where('status', true)->get();
        $groups     = Group::get();

        $reference = Transaction::generateReference();

        return view('pages.inventory.purchases.create', compact('suppliers', 'products', 'categories', 'classes', 'groups', 'reference'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $createdBy = auth()->id();
        $reference = $validated['reference_no'] ?: Transaction::generateReference();
        $paymentAmount = (float) ($validated['amount'] ?? 0);

        $items = collect($validated['items'])
            ->groupBy('inventory_item_id')
            ->map(function ($rows) {
                $qty = (int)$rows->sum(fn ($r) => (int)$r['quantity']);
                $unitPrice = (float)($rows->last()['unit_price'] ?? 0);
                return [
                    'inventory_item_id' => (int)$rows->first()['inventory_item_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $qty * $unitPrice,
                ];
            })
            ->values();

        $totalAmount = (float)$items->sum('line_total');

        if ($paymentAmount < 0) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Amount cannot be negative.']);
        }

        if ($paymentAmount > $totalAmount) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Amount cannot be greater than the purchase total.']);
        }

        $dueAmount = max(0, $totalAmount - $paymentAmount);
        $status = $paymentAmount <= 0 ? 'unpaid' : ($dueAmount <= 0 ? 'paid' : 'partial');

        $purchase = DB::transaction(function () use ($validated, $items, $totalAmount, $paymentAmount, $dueAmount, $status, $createdBy, $reference) {
            $purchase = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'reference_no' => $reference,
                'notes' => $validated['notes'] ?? null,
                'total_amount' => $totalAmount,
                'paid_amount' => $paymentAmount,
                'due_amount' => $dueAmount,
                'status' => $status,
                'created_by' => $createdBy,
            ]);

            foreach ($items as $row) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchase->id,
                    'inventory_item_id' => $row['inventory_item_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'line_total' => $row['line_total'],
                ]);

                $product = InventoryItem::lockForUpdate()->findOrFail($row['inventory_item_id']);
                $product->update([
                    'current_stock' => (int)$product->current_stock + (int)$row['quantity'],
                    'purchase_price' => $row['unit_price'],
                ]);

                StockMovement::create([
                    'inventory_item_id' => $product->id,
                    'type' => 'purchase',
                    'quantity_change' => (int)$row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'purchase_order_id' => $purchase->id,
                    'created_by' => $createdBy,
                    'note' => $validated['reference_no'] ? 'Ref: ' . $validated['reference_no'] : null,
                ]);
            }

            if ($paymentAmount > 0) {
                $this->recordPurchasePayment(
                    $purchase,
                    $paymentAmount,
                    $validated['purchase_date'],
                    $validated['reference_no'] ? $validated['reference_no'] . '-ADV-01' : $purchase->reference_no . '-ADV-01',
                    $validated['notes'] ?? null,
                    $createdBy
                );
            }

            return $purchase;
        });

        return redirect()->route('inventory.purchases.show', $purchase->id)->with('success', 'Purchase saved successfully');
    }

    // Debit petty cash after the transaction commits
    // (done outside DB::transaction to avoid nested issues)
    // Note: moved below so $purchase is available
    public function show($id)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $purchase = PurchaseOrder::with(['supplier', 'items.inventoryItem.category', 'payments.creator', 'createdBy'])
            ->findOrFail($id);

        return view('pages.inventory.purchases.show', compact('purchase'));
    }

    public function storePayment(Request $request, $id)
    {
        $this->authorizePermission('manage_inventory_purchases');

        $purchase = PurchaseOrder::with('payments')->findOrFail($id);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string'],
        ]);

        $remaining = (float) $purchase->balance;
        if ((float) $validated['amount'] > $remaining) {
            return back()
                ->withInput()
                ->withErrors(['amount' => 'Payment amount cannot be greater than the remaining due.']);
        }

        DB::transaction(function () use ($purchase, $validated) {
            $this->recordPurchasePayment(
                $purchase,
                (float) $validated['amount'],
                $validated['payment_date'],
                $validated['reference_no'] ?: ($purchase->reference_no . '-PAY-' . str_pad($purchase->payments()->count() + 1, 2, '0', STR_PAD_LEFT)),
                $validated['notes'] ?? null,
                auth()->id()
            );

            $this->syncPurchaseTotals($purchase->fresh('payments'));
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function voucher($id)
    {
        $purchase = PurchaseOrder::with(['supplier', 'items.inventoryItem.category', 'payments.creator', 'createdBy'])->findOrFail($id);
        $setting = SchoolSetting::current();

        $rows = $purchase->items->map(function ($item) {
            return [
                'description' => $item->inventoryItem?->name . ' (' . ($item->inventoryItem?->category?->name ?? 'Uncategorized') . ')',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount' => $item->line_total,
            ];
        })->toArray();

        return view('pages.vouchers.print', [
            'setting' => $setting,
            'voucherType' => 'Credit Purchase Voucher',
            'record' => $purchase,
            'fromAccountName' => $purchase->supplier?->name ?? 'Supplier',
            'rows' => $rows,
            'total' => $purchase->total_amount,
        ]);
    }

    private function recordPurchasePayment(PurchaseOrder $purchase, float $amount, string $paymentDate, ?string $referenceNo, ?string $notes, ?int $createdBy): void
    {
        if ($amount <= 0) {
            return;
        }

        $category = ExpenseCategory::firstOrCreate(
            ['slug' => 'inventory-purchase'],
            ['name' => 'Inventory Purchase', 'is_active' => true]
        );

        $payment = PurchaseOrderPayment::create([
            'purchase_order_id' => $purchase->id,
            'amount' => $amount,
            'payment_date' => $paymentDate,
            'payment_method' => 'Cash',
            'reference_no' => $referenceNo,
            'notes' => $notes,
            'created_by' => $createdBy,
        ]);

        Expense::create([
            'expense_category_id' => $category->id,
            'title'               => 'Inventory Purchase Payment — ' . ($referenceNo ?? $purchase->reference_no),
            'reference_no'        => $referenceNo,
            'amount'              => $amount,
            'expense_date'        => $paymentDate,
            'payment_method'      => 'Cash',
            'account_type'        => null,
            'account_id'          => null,
            'description'         => 'Supplier payment for purchase ref: ' . ($purchase->reference_no ?? '—'),
            'recorded_by'         => $createdBy,
        ]);

        Transaction::create([
            'reference_no'         => $referenceNo,
            'type'                 => 'expense',
            'expense_category_id'  => $category->id,
            'amount'               => $amount,
            'payment_method'       => 'Cash',
            'description'          => 'Inventory Purchase Payment — ' . ($purchase->reference_no ?? '—'),
            'transaction_date'     => $paymentDate,
            'transactionable_type' => PurchaseOrderPayment::class,
            'transactionable_id'   => $payment->id,
            'recorded_by'          => $createdBy,
        ]);
    }

    private function syncPurchaseTotals(PurchaseOrder $purchase): void
    {
        $paid = (float) $purchase->payments()->sum('amount');
        $due = max(0, (float) $purchase->total_amount - $paid);

        $purchase->update([
            'paid_amount' => $paid,
            'due_amount' => $due,
            'status' => $paid <= 0 ? 'unpaid' : ($due <= 0 ? 'paid' : 'partial'),
            'last_paid_at' => $purchase->payments()->latest('payment_date')->value('payment_date'),
        ]);
    }
}
