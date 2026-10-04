<?php
namespace App\Domains\Purchases\Models;
use App\Domains\Shared\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrder extends Model
{
    use BelongsToCompany;
    protected $fillable = ['company_id', 'po_number', 'supplier_id', 'status', 'total_amount', 'created_by'];
    protected function casts(): array { return ['total_amount' => 'decimal:2']; }
    public function items() { return $this->hasMany(PurchaseItem::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
}
