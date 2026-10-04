<?php
namespace App\Domains\Sales\Models;
use App\Domains\Shared\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
class Sale extends Model
{
    use BelongsToCompany;
    protected $fillable = ['company_id', 'invoice_number', 'customer_name', 'customer_phone', 'subtotal', 'discount', 'total', 'paid', 'payment_method', 'idempotency_key', 'created_by'];
    protected function casts(): array { return ['subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2']; }
    public function items() { return $this->hasMany(SaleItem::class); }
}
