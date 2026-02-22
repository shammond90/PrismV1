<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'industry' => $this->faker->companySuffix(),
            'website' => $this->faker->url(),
            'notes' => $this->faker->sentence(),
        ];
    }
}
