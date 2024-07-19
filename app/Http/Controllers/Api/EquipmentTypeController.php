<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentTypeFilter;
use App\Http\Resources\EquipmentTypeCollection;
use App\Models\EquipmentType;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param EquipmentTypeFilter $filter
     * @return JsonResponse
     */
    public function index(Request $request, EquipmentTypeFilter $filter): JsonResponse
    {
        $limit = $request->get('limit', config('constants.equipment_type.pagination_limit'));
        $equipmentTypes = EquipmentType::filter($filter)->paginate($limit);

        return ResponseService::success(new EquipmentTypeCollection($equipmentTypes));
    }
}
