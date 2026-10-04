<?php

namespace App\Domains\Shared\Traits;

use App\Domains\Shared\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;

/**
 * BelongsToCompany — attach to every company-owned model.
 * Adds the CompanyScope global scope + the company() relation + a helper
 * that auto-fills company_id from the authenticated user on creation.
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (empty($model->company_id) && Auth::check() && Auth::user()->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    public function agency()
    {
        return $this->belongsTo(\App\Domains\Shared\Models\Company::class);
    }

    /** Query without the agency constraint (Super Admin / system use). */
    public static function withoutCompanyScope()
    {
        return (new static)->newQueryWithoutScope(CompanyScope::class);
    }
}
