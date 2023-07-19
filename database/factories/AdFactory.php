<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Field;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $seller    = Arr::random(User::inRandomOrder()->pluck('id')->toArray());
        $category  = Arr::random(Category::inRandomOrder()->pluck('id')->toArray());
        $subcategory  = Arr::random(SubCategory::inRandomOrder()->pluck('id')->toArray());
        $fields      = json_encode(Field::where('category_id', $category)
            ->where('sub_category_id', $subcategory)
            ->get(), true);

        $featured = Arr::random([true, false]);

        if($featured){
            $price = 100;
        } else {
            $price = 0;
        }

        $sold = Arr::random([true, false]);

        if($sold){
            $sold_to = Arr::random(User::inRandomOrder()->where('id', '!=', $seller)->pluck('id')->toArray());
            $status = 'active';
        } else {
            $sold_to = null;
            $status  = Arr::random(['active', 'expired', 'inactive']);
        }

        // dd($fields);
        return [
            'user_id'           => $seller,
            'category_id'       => $category,
            'sub_category_id'   => $subcategory,
            'adId'              => Str::random(12),
            'title'             => $this->faker->unique()->name,
            'description'       => $this->faker->sentence(100),
            'fields'            => $fields,
            'is_featured'       => $featured,
            'price'             => $price,
            'position'          => $this->faker->address,
            'latitude'          => $this->faker->latitude,
            'longitude'         => $this->faker->longitude,
            'sold'              => $sold,
            'sold_to'           => $sold_to,
            'status'            => $status
        ];
    }
}
