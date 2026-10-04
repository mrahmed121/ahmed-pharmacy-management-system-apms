<?php
namespace App\Domains\Purchases\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseItem extends Model
{
    protected $fillable = ['purchase_order_id', 'medicine_id', 'quantity', 'unit_price', 'batch_number', 'expiry_date'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'expiry_date' => 'date']; }
}
