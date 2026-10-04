<?php
namespace App\Domains\Purchases\Models;
use App\Domains\Shared\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Supplier extends Model
{
    use SoftDeletes, BelongsToCompany;
    protected $fillable = ['company_id', 'name', 'phone', 'email', 'address', 'balance'];
    protected function casts(): array { return ['balance' => 'decimal:2']; }
}
