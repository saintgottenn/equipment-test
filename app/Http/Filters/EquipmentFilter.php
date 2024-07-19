<?php

namespace App\Http\Filters;

use App\Http\Filters\Abstract\QueryFilter;

class EquipmentFilter extends QueryFilter
{
    /**
     * @param string $value
     * @return void
     */
    public function q(string $value): void
    {
        $this->builder->where('equipment_type_id', $value)
            ->orWhere('serial_number','like', "%$value%")
            ->orWhere('desc','like', "%$value%");
    }

    /**
     * @param string $id
     * @return void
     */
    public function equipment_type_id(string $id): void
    {
        $this->builder->where('equipment_type_id', $id);
    }

    /**
     * @param string $number
     * @return void
     */
    public function serial_number(string $number): void
    {
        $this->builder->where('serial_number', 'like', "%$number%");
    }

    /**
     * @param string $desc
     * @return void
     */
    public function desc(string $desc): void
    {
        $this->builder->where('desc', 'like', "%$desc%");
    }
}
