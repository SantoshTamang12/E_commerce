<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Arr;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Field>
 */
class FieldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category = Arr::random(Category::inRandomOrder()->pluck('id')->toArray());
        $subcategory = Arr::random(SubCategory::inRandomOrder()->pluck('id')->toArray());

        return [
            'category_id' => $category,
            'sub_category_id' => $subcategory,
            'label'    => $this->faker->unique()->name,
            'is_price' => function(){
                return Arr::random([true, false]);
            },
            'required'  => function(){
                return Arr::random([true, false]);                
            },
            'type' => function(){
                return Arr::random(["SELECT", 'TEXT_NUMBER', 'TEXT']);                
            },
        ];
    }
}
