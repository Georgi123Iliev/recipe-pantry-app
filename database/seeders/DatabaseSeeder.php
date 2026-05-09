<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\PantryItem;
use App\Models\Recipe;
use App\Models\User;
use App\Services\UnitConverter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // --- Users ---
        $admin = User::factory()->create([
            'name' => 'Администратор Иванов',
            'email' => 'admin@receptite.bg',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Мария Петрова',
            'email' => 'maria@receptite.bg',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        // --- Ingredient Categories ---
        $dairy    = IngredientCategory::create(['name' => 'Млечни']);
        $meat     = IngredientCategory::create(['name' => 'Месо']);
        $produce  = IngredientCategory::create(['name' => 'Плодове и зеленчуци']);
        $grains   = IngredientCategory::create(['name' => 'Зърнени и тестени']);
        $pantry   = IngredientCategory::create(['name' => 'Подправки и други']);

        // --- Ingredients ---
        $flour      = Ingredient::create(['name' => 'Брашно',              'base_unit' => 'g',    'ingredient_category_id' => $grains->id]);
        $eggs       = Ingredient::create(['name' => 'Яйца',               'base_unit' => 'бр.',  'ingredient_category_id' => $dairy->id]);
        $milk       = Ingredient::create(['name' => 'Мляко',              'base_unit' => 'ml',   'ingredient_category_id' => $dairy->id]);
        $cheese     = Ingredient::create(['name' => 'Сирене',             'base_unit' => 'g',    'ingredient_category_id' => $dairy->id]);
        $butter     = Ingredient::create(['name' => 'Масло',              'base_unit' => 'g',    'ingredient_category_id' => $dairy->id]);
        $yogurt     = Ingredient::create(['name' => 'Кисело мляко',       'base_unit' => 'g',    'ingredient_category_id' => $dairy->id]);
        $chicken    = Ingredient::create(['name' => 'Пилешко месо',       'base_unit' => 'g',    'ingredient_category_id' => $meat->id]);
        $mince      = Ingredient::create(['name' => 'Кайма',              'base_unit' => 'g',    'ingredient_category_id' => $meat->id]);
        $onion      = Ingredient::create(['name' => 'Лук',                'base_unit' => 'g',    'ingredient_category_id' => $produce->id]);
        $tomato     = Ingredient::create(['name' => 'Домати',             'base_unit' => 'g',    'ingredient_category_id' => $produce->id]);
        $pepper     = Ingredient::create(['name' => 'Чушки',              'base_unit' => 'g',    'ingredient_category_id' => $produce->id]);
        $potato     = Ingredient::create(['name' => 'Картофи',            'base_unit' => 'g',    'ingredient_category_id' => $produce->id]);
        $rice       = Ingredient::create(['name' => 'Ориз',               'base_unit' => 'g',    'ingredient_category_id' => $grains->id]);
        $oil        = Ingredient::create(['name' => 'Олио',               'base_unit' => 'ml',   'ingredient_category_id' => $pantry->id]);
        $salt       = Ingredient::create(['name' => 'Сол',                'base_unit' => 'g',    'ingredient_category_id' => $pantry->id]);
        $sugar      = Ingredient::create(['name' => 'Захар',              'base_unit' => 'g',    'ingredient_category_id' => $pantry->id]);

        // --- Pantry Items for test user (Мария) ---
        $pantryData = [
            ['ingredient_id' => $flour->id,    'quantity' => 1500],
            ['ingredient_id' => $eggs->id,     'quantity' => 10],
            ['ingredient_id' => $milk->id,     'quantity' => 1000],
            ['ingredient_id' => $cheese->id,   'quantity' => 400],
            ['ingredient_id' => $butter->id,   'quantity' => 200],
            ['ingredient_id' => $onion->id,    'quantity' => 500],
            ['ingredient_id' => $potato->id,   'quantity' => 2000],
            ['ingredient_id' => $rice->id,     'quantity' => 800],
            ['ingredient_id' => $oil->id,      'quantity' => 750],
            ['ingredient_id' => $salt->id,     'quantity' => 500],
        ];

        foreach ($pantryData as $item) {
            PantryItem::create([
                'user_id' => $user->id,
                'ingredient_id' => $item['ingredient_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // --- Recipes ---
        $musaka = Recipe::create([
            'user_id'      => $user->id,
            'title'        => 'Домашна мусака',
            'description'  => 'Класическа българска мусака с кайма и картофи, запечена с кисело мляко и яйца.',
            'instructions' => "1. Нарежете картофите на кубчета и ги запържете леко.\n2. Запържете каймата с нарязания лук и подправките.\n3. Наредете в тава слой картофи, слой кайма.\n4. Разбийте яйцата с киселото мляко и излейте отгоре.\n5. Печете на 180 градуса около 40 минути.",
            'prep_time'    => 30,
            'cook_time'    => 40,
            'servings'     => 6,
        ]);

        $musaka->ingredients()->attach([
            $mince->id  => ['display_quantity' => 0.5, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $potato->id => ['display_quantity' => 1,   'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'kg')],
            $onion->id  => ['display_quantity' => 150, 'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(150, 'g')],
            $eggs->id   => ['display_quantity' => 3,   'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'бр.')],
            $yogurt->id => ['display_quantity' => 400, 'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(400, 'g')],
            $oil->id    => ['display_quantity' => 100, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'ml')],
            $salt->id   => ['display_quantity' => 1,   'display_unit' => 'чаена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'чаена лъжица')],
        ]);

        $banica = Recipe::create([
            'user_id'      => $user->id,
            'title'        => 'Баница със сирене',
            'description'  => 'Традиционна баница с хрупкави кори, пълнени със сирене и яйца.',
            'instructions' => "1. Разбъркайте сиренето с яйцата и киселото мляко.\n2. Намажете всяка кора с олио и слой от плънката.\n3. Навийте на рулца и наредете в тава.\n4. Печете на 200 градуса около 30 минути до златисто.",
            'prep_time'    => 20,
            'cook_time'    => 30,
            'servings'     => 8,
        ]);

        $banica->ingredients()->attach([
            $cheese->id => ['display_quantity' => 400, 'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(400, 'g')],
            $eggs->id   => ['display_quantity' => 4,   'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(4, 'бр.')],
            $yogurt->id => ['display_quantity' => 200, 'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $butter->id => ['display_quantity' => 100, 'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $oil->id    => ['display_quantity' => 50,  'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(50, 'ml')],
        ]);

        $pileshko = Recipe::create([
            'user_id'      => $admin->id,
            'title'        => 'Пилешко с ориз',
            'description'  => 'Лесно и бързо пилешко с ориз на фурна.',
            'instructions' => "1. Нарежете пилешкото месо на хапки.\n2. Запържете с лука и доматите.\n3. Добавете ориза и водата.\n4. Печете на 180 градуса около 45 минути.",
            'prep_time'    => 15,
            'cook_time'    => 45,
            'servings'     => 4,
        ]);

        $pileshko->ingredients()->attach([
            $chicken->id => ['display_quantity' => 0.5,  'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $rice->id    => ['display_quantity' => 200,  'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $onion->id   => ['display_quantity' => 100,  'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $tomato->id  => ['display_quantity' => 200,  'display_unit' => 'g',  'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $oil->id     => ['display_quantity' => 3,    'display_unit' => 'супена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'супена лъжица')],
            $salt->id    => ['display_quantity' => 1,    'display_unit' => 'чаена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'чаена лъжица')],
        ]);
    }
}
