<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentTypeFilter;
use App\Http\Requests\EquipmentType\IndexRequest;
use App\Http\Resources\EquipmentTypeCollection;
use App\Models\EquipmentType;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param IndexRequest $request
     * @param EquipmentTypeFilter $filter
     * @return EquipmentTypeCollection
     */
    public function index(IndexRequest $request, EquipmentTypeFilter $filter): EquipmentTypeCollection
    {
        $limit = $request->get('limit', config('constants.equipment_type.pagination_limit'));
        $equipmentTypes = EquipmentType::filter($filter)->paginate($limit);

        return new EquipmentTypeCollection($equipmentTypes);
    }
}
