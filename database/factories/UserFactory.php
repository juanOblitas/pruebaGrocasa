<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $password = Hash::make('oblitas');
        return [
            //'name' => $this->faker->name,
            //'email' => $this->faker->unique()->safeEmail,
            //'email_verified_at' => now(),
            //'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'username' => $this->faker->username,
            'password' => $password,
            'remember_token' => Str::random(10),
            'is_active' => $this->faker->randomElement([0, 1]), //Debido a que tiene un valor por defecto en db no es necesario en los faker
            'role' => $this->faker->randomElement(['manager', 'agent']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                //'email_verified_at' => null, //Comentamos ya que no tenemos esa variable
            ];
        });
    }
}
