<?php
namespace App\Domains\Inventory\Models;
use App\Domains\Shared\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
class Batch extends Model
{
    use BelongsToCompany;
    protected $fillable = ['company_id', 'medicine_id', 'batch_number', 'expiry_date', 'quantity', 'purchase_price'];
    protected function casts(): array { return ['expiry_date' => 'date', 'quantity' => 'decimal:2', 'purchase_price' => 'decimal:2']; }
    public function medicine() { return $this->belongsTo(Medicine::class); }
    public function scopeFefo($query) { return $query->where('quantity', '>', 0)->orderBy('expiry_date'); }
}
