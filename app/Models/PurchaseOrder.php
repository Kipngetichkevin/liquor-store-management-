<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'user_id',
        'order_date',
        'expected_date',
        'received_date',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generatePONumber(): string
    {
        $prefix = 'PO';
        $year = now()->format('y');
        $month = now()->format('m');
        $last = self::where('po_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('po_number', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->po_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}-{$year}{$month}-{$newNumber}";
    }

    public function receive(array $receivedQuantities): bool
    {
        $allReceived = true;
        $anyReceived = false;

        foreach ($this->items as $item) {
            $received = $receivedQuantities[$item->id] ?? 0;
            
            if ($received > 0) {
                $item->quantity_received += $received;
                $item->save();

                // Add stock to product
                $item->product->updateStock(
                    $received,
                    'purchase',
                    $this->po_number,
                    "Received from PO #{$this->po_number}"
                );

                $anyReceived = true;
            }

            if ($item->quantity_received < $item->quantity_ordered) {
                $allReceived = false;
            }
        }

        if ($allReceived) {
            $this->status = 'received';
            $this->received_date = now();
        } elseif ($anyReceived) {
            $this->status = 'partial';
        }

        $this->save();
        return true;
    }
}