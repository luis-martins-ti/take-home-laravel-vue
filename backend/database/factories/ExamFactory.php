<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => 'Exame ' . $this->faker->word(),
            'laterality' => $this->faker->randomElement(['OD', 'OE', 'AO']),
        ];
    }
}
