<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'code'  => strtoupper($this->faker->bothify('PRJ-#####')),
            'funder' => $this->faker->randomElement(['MUR','EU','MIUR','Regione Puglia']),
            'start_date' => $this->faker->date(),
            'end_date' => null,
            // 'status' => 'ongoing', // usa solo se la tua migration lo consente
            'description' => $this->faker->paragraph(),
        ];
    }
}
