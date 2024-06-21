<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

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
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // Set semua password sama, Gunakan bcrypt atau Hash::make
            'pin' => $this->getPin(),
            'phone' => '089655443322',
            'gender' => 'Laki-laki',
            'status' => 1,
            'avatar' => 'no_avatar.png',
        ];
    }

    public function getPin()
    {
        $isUnique = false;
        $uniqueCode = '';

        while (! $isUnique) {
            // Generate a random number between 0 and 999999, then pad it with zeros to ensure it is 6 digits
            $randomCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Check if the code is unique (assuming 'code' is the column where the unique codes are stored)
            $codeExists = User::where('pin', $randomCode)->exists();

            if (! $codeExists) {
                $isUnique = true;
                $uniqueCode = $randomCode;
            }
        }

        // Here, you have a unique $uniqueCode which is a 6-digit number, including leading zeros
        return $uniqueCode;
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
