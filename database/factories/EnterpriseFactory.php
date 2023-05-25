<?php

namespace Database\Factories;

use App\Services\FileManagementService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enterprise>
 */
class EnterpriseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();
        $slug = Str::slug($name);

        return [
            'name'          => $name,
            'description'   => fake()->sentence(),
            'profile_pict'  => FileManagementService::uploadImage(file_get_contents("https://api.dicebear.com/6.x/fun-emoji/png?seed=$slug"), "store"),
        ];
    }
}
