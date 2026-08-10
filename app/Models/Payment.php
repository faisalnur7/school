<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTransactions;
class Payment extends Model
{
    use HasTransactions;
    protected $fillable = [
        'student_id',
        'amount',
        'gross_amount',
        'scholarship_amount',
        'discount_type',
        'discount_amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'remarks',
        'description',
        'receipt_no',
        'collected_by',
        'account_type',
        'account_id',
        'inventory_sale_id',
    ];

    public function items()
    {
        return $this->hasMany(PaymentItem::class);
    }

    public function inventorySale()
    {
        return $this->belongsTo(InventorySale::class);
    }

    public function inventoryDueItems()
    {
        return $this->hasMany(PaymentInventoryItem::class);
    }

    public function validInventoryDueItems()
    {
        $items = $this->relationLoaded('inventoryDueItems')
            ? $this->inventoryDueItems
            : $this->inventoryDueItems()->with('inventorySaleItem.inventoryItem.category')->get();

        return $items->filter(function ($item) {
            return $item->inventorySaleItem
                && $item->inventorySaleItem->inventoryItem
                && $item->inventorySaleItem->inventoryItem->category;
        })->values();
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class,'collected_by');
    }

    public function getFeeReceivedAmountAttribute(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return (float) $items->sum(fn ($item) => (float) $item->amount);
    }

    public function getScholarshipReceivedAmountAttribute(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        $amount = round((float) $items->sum(fn ($item) => (float) ($item->scholarship_amount ?? 0)), 2);

        if ($amount > 0) {
            return $amount;
        }

        return round((float) ($this->scholarship_amount ?? 0), 2);
    }

    public function getFreeStudentshipReceivedAmountAttribute(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return round((float) $items->sum(fn ($item) => (float) ($item->free_studentship_amount ?? 0)), 2);
    }

    public function getInventoryReceivedAmountAttribute(): float
    {
        $sale = $this->relationLoaded('inventorySale') ? $this->inventorySale : $this->inventorySale()->first();
        $dueItems = $this->validInventoryDueItems();

        return (float) ($sale?->paid_amount ?? 0) + (float) $dueItems->sum('amount');
    }

    public function getCalculatedAmountAttribute(): float
    {
        return round($this->fee_received_amount + $this->inventory_received_amount, 2);
    }

    public function getCalculatedGrossAmountAttribute(): float
    {
        $feeItems = $this->relationLoaded('items') ? $this->items : $this->items()->get();
        $feeGross = (float) $feeItems->sum(function ($item) {
            return (float) ($item->fee?->amount ?? $item->amount);
        });

        $sale = $this->relationLoaded('inventorySale') ? $this->inventorySale : $this->inventorySale()->first();
        $inventoryGross = (float) ($sale?->total_amount ?? 0);
        $dueItems = $this->validInventoryDueItems();
        $inventoryGross += (float) $dueItems->sum(fn ($item) => (float) ($item->inventorySaleItem?->subtotal ?? 0));

        return round($feeGross + $inventoryGross, 2);
    }

    public function getAccountModelAttribute()
    {
        return match($this->account_type) {
            'App\Models\BankAccount'          => \App\Models\BankAccount::find($this->account_id),
            'App\Models\MobileBankingAccount' => \App\Models\MobileBankingAccount::find($this->account_id),
            'App\Models\HandCash'             => \App\Models\HandCash::find($this->account_id),
            default                           => null,
        };
    }
}
