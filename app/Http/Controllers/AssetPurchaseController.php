<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPurchase;
use App\Models\AssetPurchaseItem;
use App\Models\BankAccount;
use App\Models\MobileBankingAccount;
use App\Models\HandCash;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetPurchaseController extends Controller
{
    public function index()
    {
        $purchases = AssetPurchase::with(['items.asset', 'recorder'])->latest()->paginate(20);
        return view('pages.assets.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $assets      = Asset::where('status', 'active')->with('category')->get();
        $bankAccounts   = BankAccount::where('is_active', true)->get();
        $mobileAccounts = MobileBankingAccount::where('is_active', true)->get();
        $handCashes     = HandCash::where('is_active', true)->get();
        return view('pages.assets.purchases.create', compact('assets', 'bankAccounts', 'mobileAccounts', 'handCashes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_date'  => 'required|date',
            'payment_type'   => 'required|in:hand_cash,bank,mobile',
            'account_id'     => 'required|integer',
            'notes'          => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.asset_id'   => 'required|exists:assets,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $accountType = match($request->payment_type) {
                'bank'      => \App\Models\BankAccount::class,
                'mobile'    => \App\Models\MobileBankingAccount::class,
                'hand_cash' => \App\Models\HandCash::class,
            };

            $totalAmount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);

            $reference = 'PUR-' . now()->format('Ymd') . '-' . str_pad(AssetPurchase::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $purchase = AssetPurchase::create([
                'reference_no'  => $reference,
                'purchase_date' => $request->purchase_date,
                'total_amount'  => $totalAmount,
                'payment_type'  => $request->payment_type,
                'account_type'  => $accountType,
                'account_id'    => $request->account_id,
                'notes'         => $request->notes,
                'recorded_by'   => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                $totalPrice = $item['quantity'] * $item['unit_price'];

                AssetPurchaseItem::create([
                    'asset_purchase_id' => $purchase->id,
                    'asset_id'          => $item['asset_id'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'total_price'       => $totalPrice,
                ]);

                // Increment asset quantity
                Asset::where('id', $item['asset_id'])->increment('quantity', $item['quantity']);
            }

            // Record as expense transaction
            $expenseCategory = ExpenseCategory::where('slug', 'asset-purchase')->first()
                ?? ExpenseCategory::where('slug', 'maintenance')->first();

            $paymentMethodMap = [
                'hand_cash' => 'Cash',
                'bank'      => 'Bank Transfer',
                'mobile'    => 'Mobile Banking',
            ];

            $expensePaymentMethod = $paymentMethodMap[$request->payment_type] ?? 'Other';

            $purchase->recordExpense($expenseCategory->id, 'Asset Purchase - ' . $reference, [
                'amount'         => $totalAmount,
                'payment_method' => $expensePaymentMethod,
                'description'    => 'Asset purchase ref: ' . $reference,
                'account_type'   => $accountType,
                'account_id'     => $request->account_id,
            ]);

            DB::commit();

            return redirect()->route('asset-purchases.index')->with('success', 'Purchase recorded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(AssetPurchase $assetPurchase)
    {
        $assetPurchase->load(['items.asset.category', 'recorder']);
        return view('pages.assets.purchases.show', compact('assetPurchase'));
    }

    public function getAccounts(Request $request)
    {
        $accounts = match($request->type) {
            'bank'      => BankAccount::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->bank_name . ' - ' . $a->account_number]),
            'mobile'    => MobileBankingAccount::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->provider . ' - ' . $a->account_number]),
            'hand_cash' => HandCash::where('is_active', true)->get()->map(fn($a) => ['id' => $a->id, 'label' => $a->label]),
            default     => collect(),
        };

        return response()->json($accounts);
    }
}
