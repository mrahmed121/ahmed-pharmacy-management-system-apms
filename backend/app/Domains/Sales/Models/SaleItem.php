<?php
namespace App\Domains\Sales\Models;
use Illuminate\Database\Eloquent\Model;
class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'medicine_id', 'batch_id', 'quantity', 'unit_price', 'line_total'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'line_total' => 'decimal:2']; }
    public function batch() { return $this->belongsTo(\App\Domains\Inventory\Models\Batch::class); }
    public function medicine() { return $this->belongsTo(\App\Domains\Inventory\Models\Medicine::class); }
}
