<?php

namespace Database\Factories;

use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->text(20);
        $slug = Str::slug($title);
        Storage::makeDirectory('project_images');
        $image = fake()->image(null, 250, 250, 'motorcycles');
        $image_url = Storage::putFileAs('project_images', $image, "$slug.png");
        $type_ids = Type::pluck('id')->toArray();
        $type_ids[] = null;
        return [
            'type_id' => Arr::random($type_ids),
            'title' => $title,
            'slug' => $slug,
            'description' => fake()->paragraphs(15, true),
            'image' => $image_url,
            'is_completed' => fake()->boolean()
        ];
    }
}
