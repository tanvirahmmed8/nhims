<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Patient> */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'registered_at_facility_id' => Facility::factory(),
            'patient_number' => fake()->unique()->numerify('NHIMS-######'),
            'demo_national_id' => fake()->unique()->numerify('NID-DEMO-########'),
            'name' => fake()->name(),
            'date_of_birth' => fake()->dateTimeBetween('-75 years', '-1 year')->format('Y-m-d'),
            'sex' => fake()->randomElement(['female', 'male', 'other']),
            'phone' => fake()->numerify('01#########'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'allergies' => null,
            'conditions' => null,
            'status' => 'active',
        ];
    }
}
