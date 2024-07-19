<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Filters\EquipmentFilter;
use App\Http\Requests\Equipment\StoreRequest;
use App\Http\Requests\Equipment\UpdateRequest;
use App\Http\Resources\EquipmentCollection;
use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Services\EquipmentService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function __construct(
        private readonly EquipmentService $equipmentService,
    )
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @param EquipmentFilter $filter
     * @return JsonResponse
     */
    public function index(Request $request, EquipmentFilter $filter): JsonResponse
    {
        $limit = $request->get('limit', config('constants.equipment.pagination_limit'));
        $equipments = Equipment::filter($filter)->latest()->paginate($limit);

        return ResponseService::success(new EquipmentCollection($equipments));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->all();

        $result = $this->equipmentService->createMultiple($validated);

        return ResponseService::success($result);
    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $equipment = Equipment::find($id);

        if(!$equipment) {
            return ResponseService::notFound(message: 'Оборудование по такому ID не найдено');
        }

        return ResponseService::success((EquipmentResource::make($equipment)));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $equipment = Equipment::find($id);

        if(!$equipment) {
            return ResponseService::notFound(message: 'Оборудование по такому ID не найдено');
        }

        $validated = $request->validated();

        $equipment->update($validated);

        return ResponseService::success(EquipmentResource::make($equipment));
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $equipment = Equipment::find($id);

        if(!$equipment) {
            return ResponseService::notFound(message: 'Оборудование по такому ID не найдено');
        }

        $equipment->delete();

        return ResponseService::success();
    }
}
