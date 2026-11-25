<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Facility;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'facility_id' => Facility::factory(),
            'doctor_id' => User::factory(),
            'scheduled_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'type' => 'general consultation',
            'reason' => fake()->sentence(8),
            'status' => 'booked',
        ];
    }
}
