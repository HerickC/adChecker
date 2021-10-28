<?php

namespace Database\Factories;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $categories       = ['eletrodomésticos', 'veículos', 'móveis'];
        $category = $this->faker->randomElement($categories);

        $items = [
            'eletrodomésticos' => ['geladeira', 'televisão', 'fogão', 'computador'],
            'veículos' => ['bicicleta', 'motocicleta', 'carro'],
            'móveis' => ['sofá', 'cadeira', 'mesa', 'estante', 'cama'],
        ];

        $item = $this->faker->randomElement($items[$category]);

        $prices = [
            'geladeira'     => [1000, 3000],
            'televisão'     => [1000, 4000],
            'fogão'         => [500, 1500],
            'computador'    => [3000, 8000],
            'bicicleta'     => [950, 5000],
            'motocicleta'   => [8000, 15000],
            'carro'         => [15000, 50000],
            'sofá'          => [800, 2500],
            'cadeira'       => [100, 900],
            'mesa'          => [100, 700],
            'estante'       => [250, 800],
            'cama'          => [600, 2000]
        ];

        return [
            'title'         => $this->faker->sentence(),
            'description'   => $this->faker->paragraphs(3, true),
            'image'         => '/img/semimagem.png',
            'value'         => $this->faker->randomFloat(2, $prices[$item][0], $prices[$item][1]),
            'category'      => $category,
            'type'          => $item,
            'score'         => (rand(0, 25) / 100)
        ];
    }
}
