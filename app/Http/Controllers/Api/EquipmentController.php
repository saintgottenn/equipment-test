<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentFilter;
use App\Http\Requests\Equipment\IndexRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EquipmentController extends Controller
{
    public function __construct(
        private readonly EquipmentService $equipmentService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     * @param IndexRequest $request
     * @param EquipmentFilter $filter
     * @return EquipmentCollection
     */
    public function index(IndexRequest $request, EquipmentFilter $filter): EquipmentCollection
    {
        $limit = $request->get('limit', config('constants.equipment.pagination_limit'));
        $equipments = Equipment::filter($filter)->latest()->paginate($limit);

        return new EquipmentCollection($equipments);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return array|array[]
     */
    public function store(Request $request): array
    {
        $queryParams = $request->all();

        return $this->equipmentService->createMultiple($queryParams);
    }

    /**
     * Display the specified resource.
     * @param Equipment $equipment
     * @return EquipmentResource
     */
    public function show(Equipment $equipment): EquipmentResource
    {
        return EquipmentResource::make($equipment);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateRequest $request
     * @param Equipment $equipment
     * @return EquipmentResource
     */
    public function update(UpdateRequest $request, Equipment $equipment): EquipmentResource
    {
        $validated = $request->validated();

        $equipment->update($validated);

        return EquipmentResource::make($equipment);
    }

    /**
     * Remove the specified resource from storage.
     * @param Equipment $equipment
     * @return Response
     */
    public function destroy(Equipment $equipment): Response
    {
        $equipment->delete();

        return response()->noContent();
    }
}
