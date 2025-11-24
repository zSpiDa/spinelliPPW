<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Milestone;

class MilestoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => 'Milestone: '.$this->faker->words(2, true),
            'due_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['planned','in_progress','completed']),
        ];
    }
}
