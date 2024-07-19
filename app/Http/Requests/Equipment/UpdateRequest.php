<?php

namespace App\Http\Requests\Equipment;

use App\Models\Equipment;
use App\Models\EquipmentType;
use App\Services\EquipmentService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function __construct(
        private readonly EquipmentService $equipmentService,
    )
    {
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'serial_number' => [
                'string',
                function ($attribute, $value, $fail) {
                    $this->validateSerialNumberMask($value, $fail);
                },
            ],
            'equipment_type_id' => ['integer', 'exists:equipment_types,id'],
            'desc' => ['string'],
        ];
    }

    /**
     * @param $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateUniqueEquipment($validator);
        });
    }

    /**
     * @param $validator
     * @return void
     */
    private function validateUniqueEquipment($validator): void
    {
        $serialNumber = $this->input('serial_number');
        $equipmentTypeId = $this->getEquipmentTypeId();

        $query = Equipment::where('equipment_type_id', $equipmentTypeId)
            ->where('serial_number', $serialNumber)
            ->where('id', '!=', $this->equipment);

        if ($query->exists()) {
            $validator->errors()->add(
                'serial_number',
                'The combination of serial number and equipment type already exists.'
            );
        }
    }

    /**
     * @param $value
     * @param $fail
     * @return void
     */
    private function validateSerialNumberMask($value, $fail): void
    {
        $equipmentTypeId = $this->getEquipmentTypeId();
        $equipmentType = EquipmentType::find($equipmentTypeId);

        if (!$equipmentType) {
            $fail('The equipment type is invalid.');
            return;
        }

        if (!$this->equipmentService->validateSerialNumber($value, $equipmentType->mask)) {
            $fail('The serial number does not match the required format for this equipment type.');
        }
    }

    /**
     * @return int|null
     */
    private function getEquipmentTypeId(): ?int
    {
        if ($this->has('equipment_type_id')) {
            return $this->input('equipment_type_id');
        }

        $equipmentId = $this->route('equipment');
        if ($equipmentId) {
            $equipment = Equipment::find($equipmentId);
            return $equipment ? $equipment->equipment_type_id : null;
        }

        return null;
    }
}
