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
        $dairy = IngredientCategory::create(['name' => 'Млечни']);
        $meat = IngredientCategory::create(['name' => 'Месо']);
        $produce = IngredientCategory::create(['name' => 'Плодове и зеленчуци']);
        $grains = IngredientCategory::create(['name' => 'Зърнени и тестени']);
        $pantry = IngredientCategory::create(['name' => 'Подправки и други']);

        // --- Ingredients ---
        $flour = Ingredient::create(['name' => 'Брашно', 'base_unit' => 'g', 'ingredient_category_id' => $grains->id]);
        $eggs = Ingredient::create(['name' => 'Яйца', 'base_unit' => 'бр.', 'ingredient_category_id' => $dairy->id]);
        $milk = Ingredient::create(['name' => 'Мляко', 'base_unit' => 'ml', 'ingredient_category_id' => $dairy->id]);
        $cheese = Ingredient::create(['name' => 'Сирене', 'base_unit' => 'g', 'ingredient_category_id' => $dairy->id]);
        $butter = Ingredient::create(['name' => 'Масло', 'base_unit' => 'g', 'ingredient_category_id' => $dairy->id]);
        $yogurt = Ingredient::create(['name' => 'Кисело мляко', 'base_unit' => 'g', 'ingredient_category_id' => $dairy->id]);
        $chicken = Ingredient::create(['name' => 'Пилешко месо', 'base_unit' => 'g', 'ingredient_category_id' => $meat->id]);
        $mince = Ingredient::create(['name' => 'Кайма', 'base_unit' => 'g', 'ingredient_category_id' => $meat->id]);
        $onion = Ingredient::create(['name' => 'Лук', 'base_unit' => 'g', 'ingredient_category_id' => $produce->id]);
        $tomato = Ingredient::create(['name' => 'Домати', 'base_unit' => 'g', 'ingredient_category_id' => $produce->id]);
        $pepper = Ingredient::create(['name' => 'Чушки', 'base_unit' => 'g', 'ingredient_category_id' => $produce->id]);
        $potato = Ingredient::create(['name' => 'Картофи', 'base_unit' => 'g', 'ingredient_category_id' => $produce->id]);
        $rice = Ingredient::create(['name' => 'Ориз', 'base_unit' => 'g', 'ingredient_category_id' => $grains->id]);
        $oil = Ingredient::create(['name' => 'Олио', 'base_unit' => 'ml', 'ingredient_category_id' => $pantry->id]);
        $salt = Ingredient::create(['name' => 'Сол', 'base_unit' => 'g', 'ingredient_category_id' => $pantry->id]);
        $sugar = Ingredient::create(['name' => 'Захар', 'base_unit' => 'g', 'ingredient_category_id' => $pantry->id]);

        // --- Pantry Items for test user (Мария) ---
        $pantryData = [
            ['ingredient_id' => $flour->id, 'quantity' => 1500],
            ['ingredient_id' => $eggs->id, 'quantity' => 10],
            ['ingredient_id' => $milk->id, 'quantity' => 1000],
            ['ingredient_id' => $cheese->id, 'quantity' => 400],
            ['ingredient_id' => $butter->id, 'quantity' => 200],
            ['ingredient_id' => $onion->id, 'quantity' => 500],
            ['ingredient_id' => $potato->id, 'quantity' => 2000],
            ['ingredient_id' => $rice->id, 'quantity' => 800],
            ['ingredient_id' => $oil->id, 'quantity' => 750],
            ['ingredient_id' => $salt->id, 'quantity' => 500],
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
            'user_id' => $user->id,
            'title' => 'Домашна мусака',
            'description' => 'Класическа българска мусака с кайма и картофи, запечена с кисело мляко и яйца.',
            'instructions' => "1. Нарежете картофите на кубчета и ги запържете леко.\n2. Запържете каймата с нарязания лук и подправките.\n3. Наредете в тава слой картофи, слой кайма.\n4. Разбийте яйцата с киселото мляко и излейте отгоре.\n5. Печете на 180 градуса около 40 минути.",
            'prep_time' => 30,
            'cook_time' => 40,
            'servings' => 6,
        ]);

        $musaka->ingredients()->attach([
            $mince->id => ['display_quantity' => 0.5, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $potato->id => ['display_quantity' => 1, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'kg')],
            $onion->id => ['display_quantity' => 150, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(150, 'g')],
            $eggs->id => ['display_quantity' => 3, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'бр.')],
            $yogurt->id => ['display_quantity' => 400, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(400, 'g')],
            $oil->id => ['display_quantity' => 100, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'ml')],
            $salt->id => ['display_quantity' => 1, 'display_unit' => 'чаена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'чаена лъжица')],
        ]);

        $banica = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Баница със сирене',
            'description' => 'Традиционна баница с хрупкави кори, пълнени със сирене и яйца.',
            'instructions' => "1. Разбъркайте сиренето с яйцата и киселото мляко.\n2. Намажете всяка кора с олио и слой от плънката.\n3. Навийте на рулца и наредете в тава.\n4. Печете на 200 градуса около 30 минути до златисто.",
            'prep_time' => 20,
            'cook_time' => 30,
            'servings' => 8,
        ]);

        $banica->ingredients()->attach([
            $cheese->id => ['display_quantity' => 400, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(400, 'g')],
            $eggs->id => ['display_quantity' => 4, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(4, 'бр.')],
            $yogurt->id => ['display_quantity' => 200, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $butter->id => ['display_quantity' => 100, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $oil->id => ['display_quantity' => 50, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(50, 'ml')],
        ]);

        $pileshko = Recipe::create([
            'user_id' => $admin->id,
            'title' => 'Пилешко с ориз',
            'description' => 'Лесно и бързо пилешко с ориз на фурна.',
            'instructions' => "1. Нарежете пилешкото месо на хапки.\n2. Запържете с лука и доматите.\n3. Добавете ориза и водата.\n4. Печете на 180 градуса около 45 минути.",
            'prep_time' => 15,
            'cook_time' => 45,
            'servings' => 4,
        ]);

        $pileshko->ingredients()->attach([
            $chicken->id => ['display_quantity' => 0.5, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $rice->id => ['display_quantity' => 200, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $onion->id => ['display_quantity' => 100, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $tomato->id => ['display_quantity' => 200, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $oil->id => ['display_quantity' => 3, 'display_unit' => 'супена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'супена лъжица')],
            $salt->id => ['display_quantity' => 1, 'display_unit' => 'чаена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'чаена лъжица')],
        ]);

        // --- Additional Recipes ---

        $palachinki = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Палачинки',
            'description' => 'Тънки и вкусни палачинки за закуска.',
            'instructions' => "1. Разбъркайте яйцата, млякото и брашното до хомогенна смес.\n2. Разтопете малко масло в тигана.\n3. Изсипете черпак от сместа и разнесете.\n4. Обърнете, когато се появят мехурчета.",
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4,
        ]);

        $palachinki->ingredients()->attach([
            $flour->id => ['display_quantity' => 200, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
            $eggs->id => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(2, 'бр.')],
            $milk->id => ['display_quantity' => 500, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(500, 'ml')],
            $butter->id => ['display_quantity' => 50, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(50, 'g')],
        ]);

        $omlet = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Омлет със сирене',
            'description' => 'Бърз и хранителен омлет.',
            'instructions' => "1. Разбийте яйцата.\n2. Добавете натрошеното сирене.\n3. Разтопете маслото в тиган.\n4. Изсипете сместа и гответе до готовност.",
            'prep_time' => 5,
            'cook_time' => 5,
            'servings' => 1,
        ]);

        $omlet->ingredients()->attach([
            $eggs->id => ['display_quantity' => 3, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'бр.')],
            $cheese->id => ['display_quantity' => 50, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(50, 'g')],
            $butter->id => ['display_quantity' => 20, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(20, 'g')],
        ]);

        $chushki = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Пълнени чушки с кайма и ориз',
            'description' => 'Класически пълнени чушки на фурна.',
            'instructions' => "1. Запържете лука, каймата и ориза.\n2. Добавете нарязан домат и малко вода.\n3. Напълнете почистените чушки.\n4. Подредете в тава с вода и печете 45 мин.",
            'prep_time' => 30,
            'cook_time' => 45,
            'servings' => 4,
        ]);

        $chushki->ingredients()->attach([
            $pepper->id => ['display_quantity' => 8, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(8, 'бр.')],
            $mince->id => ['display_quantity' => 0.5, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $rice->id => ['display_quantity' => 100, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $onion->id => ['display_quantity' => 1, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'бр.')],
            $oil->id => ['display_quantity' => 50, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(50, 'ml')],
        ]);

        $mishmash = Recipe::create([
            'user_id' => $admin->id,
            'title' => 'Миш-маш',
            'description' => 'Лятно традиционно ястие с чушки и домати.',
            'instructions' => "1. Запържете нарязаните чушки и лук.\n2. Добавете настърганите домати и гответе докато изври водата.\n3. Добавете натрошеното сирене и разбитите яйца.\n4. Бъркайте докато яйцата стегнат.",
            'prep_time' => 15,
            'cook_time' => 20,
            'servings' => 2,
        ]);

        $mishmash->ingredients()->attach([
            $pepper->id => ['display_quantity' => 4, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(4, 'бр.')],
            $tomato->id => ['display_quantity' => 300, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(300, 'g')],
            $eggs->id => ['display_quantity' => 3, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(3, 'бр.')],
            $cheese->id => ['display_quantity' => 150, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(150, 'g')],
            $oil->id => ['display_quantity' => 30, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(30, 'ml')],
        ]);

        $kartofi = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Пържени картофи със сирене',
            'description' => 'Любима класика.',
            'instructions' => "1. Обелете и нарежете картофите.\n2. Изпържете ги в сгорещено олио.\n3. Извадете, посолете и настържете обилно сирене отгоре.",
            'prep_time' => 10,
            'cook_time' => 15,
            'servings' => 2,
        ]);

        $kartofi->ingredients()->attach([
            $potato->id => ['display_quantity' => 0.5, 'display_unit' => 'kg', 'normalized_quantity' => UnitConverter::toBaseUnit(0.5, 'kg')],
            $oil->id => ['display_quantity' => 200, 'display_unit' => 'ml', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'ml')],
            $cheese->id => ['display_quantity' => 100, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(100, 'g')],
            $salt->id => ['display_quantity' => 1, 'display_unit' => 'щипка', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'щипка')],
        ]);

        $supa = Recipe::create([
            'user_id' => $admin->id,
            'title' => 'Пилешка супа',
            'description' => 'Сгряваща и ароматна супа.',
            'instructions' => "1. Сварете месото и го накъсайте.\n2. В бульона добавете нарязаните картофи и лук.\n3. Върнете месото и гответе до готовност на зеленчуците.\n4. Направете застройка с кисело мляко и яйце по желание.",
            'prep_time' => 15,
            'cook_time' => 40,
            'servings' => 4,
        ]);

        $supa->ingredients()->attach([
            $chicken->id => ['display_quantity' => 300, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(300, 'g')],
            $potato->id => ['display_quantity' => 2, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(2, 'бр.')],
            $onion->id => ['display_quantity' => 1, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'бр.')],
            $oil->id => ['display_quantity' => 2, 'display_unit' => 'супена лъжица', 'normalized_quantity' => UnitConverter::toBaseUnit(2, 'супена лъжица')],
        ]);

        $krem = Recipe::create([
            'user_id' => $user->id,
            'title' => 'Крем карамел',
            'description' => 'Класически млечен десерт.',
            'instructions' => "1. Карамелизирайте част от захарта и разпределете в купички.\n2. Разбийте яйцата с останалата захар и млякото.\n3. Изсипете сместа в купичките.\n4. Печете на водна баня на 150 градуса.",
            'prep_time' => 15,
            'cook_time' => 60,
            'servings' => 6,
        ]);

        $krem->ingredients()->attach([
            $eggs->id => ['display_quantity' => 6, 'display_unit' => 'бр.', 'normalized_quantity' => UnitConverter::toBaseUnit(6, 'бр.')],
            $milk->id => ['display_quantity' => 1, 'display_unit' => 'l', 'normalized_quantity' => UnitConverter::toBaseUnit(1, 'l')],
            $sugar->id => ['display_quantity' => 200, 'display_unit' => 'g', 'normalized_quantity' => UnitConverter::toBaseUnit(200, 'g')],
        ]);
    }
}
