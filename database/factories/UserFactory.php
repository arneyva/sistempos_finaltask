<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    // protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->name,
            'lastname' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'username' =>  $this->faker->unique()->name,
            // Set semua password sama
            'password' => bcrypt('password'), // Gunakan bcrypt atau Hash::make
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    // public function unverified(): static
    // {
    //     return $this->state(fn (array $attributes) => [
    //         'email_verified_at' => null,
    //     ]);
    // }
}
