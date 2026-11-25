<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Facility> */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('FAC-##??'),
            'name' => fake()->company().' Health Centre',
            'type' => 'clinic',
            'district' => fake()->city(),
            'address' => fake()->address(),
            'phone' => fake()->numerify('01#########'),
            'status' => 'active',
        ];
    }
}
