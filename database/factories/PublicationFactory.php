<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Publication;

class PublicationFactory extends Factory
{
    protected $model = Publication::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6),
            'type'  => $this->faker->randomElement(['journal','conference','workshop']),
            'venue' => $this->faker->randomElement(['TOCHI','IJHCS','CHI','CSCW','SOUPS']),
            'doi'   => null,
            // 'status' => 'drafting', // usa solo se la tua migration lo consente
            'target_deadline' => null,
        ];
    }
}