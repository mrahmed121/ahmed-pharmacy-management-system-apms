<?php
namespace App\Domains\Inventory\Models;
use App\Domains\Shared\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Medicine extends Model
{
    use SoftDeletes, BelongsToCompany;
    protected $fillable = ['company_id', 'name', 'generic_name', 'barcode', 'unit', 'rack_location', 'reorder_level', 'sale_price', 'is_controlled', 'requires_prescription', 'is_active'];
    protected function casts(): array { return ['reorder_level' => 'decimal:2', 'sale_price' => 'decimal:2', 'is_controlled' => 'boolean', 'requires_prescription' => 'boolean', 'is_active' => 'boolean']; }
    public function batches() { return $this->hasMany(Batch::class); }
    public function availableStock(): float { return (float) $this->batches()->sum('quantity'); }
}
