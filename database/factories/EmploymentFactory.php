<?php

namespace Database\Factories;

use App\Models\Employment;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmploymentFactory extends Factory
{
    protected $model = Employment::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'contact_id' => Contact::factory(),
            'position' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'start_date' => $this->faker->date(),
            'end_date' => null,
        ];
    }
}
