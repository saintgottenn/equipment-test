<?php

namespace Database\Seeders;

use App\Models\EquipmentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i <= 20; $i++) {
            EquipmentType::create([
                'name' => $this->generateRandomName(),
                'mask' => $this->generateRandomMask(),
            ]);
        }
    }

    /**
     * @return string
     */
    private function generateRandomName(): string
    {
        $brands = ['TP-Link', 'D-Link', 'Netgear', 'Asus', 'Linksys', 'Cisco'];
        $types = ['Router', 'Switch', 'Access Point', 'Modem', 'Repeater'];
        $numbers = range(100, 999);

        $brand = $brands[array_rand($brands)];
        $type = $types[array_rand($types)];
        $number = $numbers[array_rand($numbers)];

        return "{$brand} {$type} {$number}";
    }

    /**
     * @return string
     */
    private function generateRandomMask(): string
    {
        $maskLength = rand(8, 12);
        $mask = '';
        $possibleChars = ['N', 'A', 'a', 'X', 'Z'];

        for ($i = 0; $i < $maskLength; $i++) {
            $char = $possibleChars[array_rand($possibleChars)];
            $mask .= $char;
        }

        return $mask;
    }
}
