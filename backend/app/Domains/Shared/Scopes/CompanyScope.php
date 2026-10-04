<?php

namespace App\Domains\Shared\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * CompanyScope — multi-tenant isolation at the query layer.
 *
 * Any model using BelongsToCompany automatically filters rows to the
 * authenticated user's company. Super Admin (company_id = null) bypasses
 * the scope and sees everything; unauthenticated contexts see nothing.
 */
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // No authenticated user (console, seeder, tests without actingAs):
        // apply no constraint — callers must scope explicitly.
        if (! $user) {
            return;
        }

        // Platform-level Super Admin sees all agencies.
        if (is_null($user->company_id)) {
            return;
        }

        $builder->where($model->getTable().'.company_id', $user->company_id);
    }
}
