<?php

namespace App\Http\Filters;

use App\Http\Filters\Abstract\QueryFilter;

class EquipmentTypeFilter extends QueryFilter
{
    /**
     * @param string $value
     * @return void
     */
    public function q(string $value): void
    {
        $this->builder->where('name','like', "%$value%")
            ->orWhere('mask','like', "%$value%");
    }


    /**
     * @param string $name
     * @return void
     */
    public function name(string $name): void
    {
        $this->builder->where('name', 'like', "%$name%");
    }

    /**
     * @param string $mask
     * @return void
     */
    public function mask(string $mask): void
    {
        $this->builder->where('mask', 'like', "%$mask%");
    }
}
