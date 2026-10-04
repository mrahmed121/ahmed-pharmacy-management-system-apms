<?php

namespace App\Http\Controllers\Api\V1;

use App\Domains\Shared\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UpdateSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(private SettingService $settings) {}

    public function index(Request $request): JsonResponse
    {
        $actor = $request->user();

        if (! $actor->company_id) {
            return response()->json([
                'message' => 'Settings are company-scoped. Super Admin has no company settings.',
            ], 422);
        }

        return response()->json([
            'data' => $this->settings->allForAgency($actor->company_id),
        ]);
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $actor = $request->user();

        if (! $actor->company_id) {
            return response()->json([
                'message' => 'Settings are company-scoped. Super Admin has no company settings.',
            ], 422);
        }

        $saved = $this->settings->updateMany($actor->company_id, $request->validated('settings'));

        return response()->json([
            'message' => 'Settings updated.',
            'data' => collect($saved)->map(fn ($s) => [
                'group' => $s->group,
                'key' => $s->key,
                'value' => $s->typedValue(),
                'type' => $s->type,
            ])->all(),
        ]);
    }
}
