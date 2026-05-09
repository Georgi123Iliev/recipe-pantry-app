<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\PantryItem;
use App\Models\User;
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
            ['ingredient_id' => $flour->id,    'quantity' => 1500],  // 1.5 kg
            ['ingredient_id' => $eggs->id,     'quantity' => 10],    // 10 бр.
            ['ingredient_id' => $milk->id,     'quantity' => 1000],  // 1 l
            ['ingredient_id' => $cheese->id,   'quantity' => 400],   // 400 g
            ['ingredient_id' => $butter->id,   'quantity' => 200],   // 200 g
            ['ingredient_id' => $onion->id,    'quantity' => 500],   // 500 g
            ['ingredient_id' => $potato->id,   'quantity' => 2000],  // 2 kg
            ['ingredient_id' => $rice->id,     'quantity' => 800],   // 800 g
            ['ingredient_id' => $oil->id,      'quantity' => 750],   // 750 ml
            ['ingredient_id' => $salt->id,     'quantity' => 500],   // 500 g
        ];

        foreach ($pantryData as $item) {
            PantryItem::create([
                'user_id' => $user->id,
                'ingredient_id' => $item['ingredient_id'],
                'quantity' => $item['quantity'],
            ]);
        }
    }
}
