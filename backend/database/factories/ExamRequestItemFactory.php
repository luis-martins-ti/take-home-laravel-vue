<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamRequestItemFactory extends Factory
{
    public function definition()
    {
        return [
            'exam_id' => Exam::factory(),
            'laterality' => $this->faker->randomElement(['OD', 'OE', 'AO']),
            'comment' => $this->faker->sentence(),
            'group' => $this->faker->randomElement(['Individual', 'Grupo 1', 'Grupo 2']),
            'package_id' => null,
        ];
    }
}
