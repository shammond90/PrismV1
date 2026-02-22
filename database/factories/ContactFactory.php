<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement(['Mr','Mrs','Dr','Ms','Mx','']),'            
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'given_name' => $this->faker->firstName(),
            'pronouns' => $this->faker->randomElement(['he/him','she/her','they/them','']),
            'locations' => [$this->faker->city()],
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
