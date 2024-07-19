<?php

namespace App\Services;


use App\Http\Resources\EquipmentResource;
use App\Models\Equipment;
use App\Models\EquipmentType;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class EquipmentService
{
    /**
     * @param array $data
     * @return array
     */
    public function createMultiple(array $data): array
    {
        $result = ['errors' => [], 'success' => []];

        foreach ($data as $index => $item) {
            $validator = Validator::make($item, [
                'equipment_type_id' => ['required', 'integer', 'exists:equipment_types,id'],
                'serial_number' => ['required', 'string', 'unique:equipment,serial_number'],
                'desc' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                $result['errors'][$index] = $validator->errors()->all();
                continue;
            }

            $equipmentType = EquipmentType::find($item['equipment_type_id']);

            if (!$equipmentType) {
                $result['errors'][$index] = ['Equipment type not found'];
                continue;
            }

            if (!$this->validateSerialNumber($item['serial_number'], $equipmentType->mask)) {
                $result['errors'][$index] = ['Invalid serial number format'];
                continue;
            }

            $existingEquipment = Equipment::where('equipment_type_id', $item['equipment_type_id'])
                ->where('serial_number', $item['serial_number'])
                ->first();

            if ($existingEquipment) {
                $result['errors'][$index] = ['Serial number already exists for this equipment type'];
                continue;
            }

            $equipment = Equipment::create($item);
            $result['success'][$index] = EquipmentResource::make($equipment);
        }

        return $result;
    }

    /**
     * @param $serialNumber
     * @param $mask
     * @return bool
     */
    public function validateSerialNumber($serialNumber, $mask): bool
    {
        $pattern = '/^' . strtr($mask, [
                'N' => '[0-9]',
                'A' => '[A-Z]',
                'a' => '[a-z]',
                'X' => '[A-Z0-9]',
                'Z' => '[-_@]'
            ]) . '$/';

        return preg_match($pattern, $serialNumber) === 1;
    }
}
