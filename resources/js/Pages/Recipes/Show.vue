<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    recipe: Object,
});

const page = usePage();

const canModify = computed(() => {
    return props.recipe.user_id === page.props.auth.user.id || page.props.auth.user.is_admin;
});

const deleteRecipe = () => {
    if (confirm('Сигурни ли сте, че искате да изтриете тази рецепта?')) {
        router.delete(route('recipes.destroy', props.recipe.id));
    }
};
</script>

<template>
    <Head :title="recipe.title" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <!-- Header -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                    {{ recipe.title }}
                </h1>
                <div v-if="canModify" class="flex gap-2 mt-1">
                    <Link :href="route('recipes.edit', recipe.id)" class="btn-add" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                        Редактирай
                    </Link>
                    <button
                        @click="deleteRecipe"
                        class="btn-add"
                        style="font-size: 0.8rem; padding: 0.4rem 0.8rem; background-color: #C24641; box-shadow: 2px 2px 0px #4E342E;"
                    >
                        Изтрий
                    </button>
                </div>
            </div>

            <!-- Meta info -->
            <div class="font-caveat text-text-muted mt-2" style="font-size: 1.3rem;">
                <span v-if="recipe.prep_time">Подготовка: {{ recipe.prep_time }} мин</span>
                <span v-if="recipe.prep_time && recipe.cook_time"> · </span>
                <span v-if="recipe.cook_time">Готвене: {{ recipe.cook_time }} мин</span>
                <span v-if="recipe.servings"> · {{ recipe.servings }} порции</span>
            </div>
            <p class="text-sm text-text-muted font-montserrat mt-1">
                от {{ recipe.user?.name }}
            </p>

            <!-- Description -->
            <p v-if="recipe.description" class="mt-4 font-lora" style="font-size: 1.1rem; text-align: justify;">
                {{ recipe.description }}
            </p>

            <!-- Images -->
            <div v-if="recipe.images && recipe.images.length > 0" class="mt-6">
                <div
                    v-for="image in recipe.images"
                    :key="image.id"
                    class="polaroid"
                    style="width: 80%; margin: 2rem auto;"
                >
                    <div class="tape"></div>
                    <img
                        :src="image.url"
                        :alt="recipe.title"
                        loading="lazy"
                        style="width: 100%; display: block;"
                    />
                </div>
            </div>

            <!-- Ingredients -->
            <h2 class="font-lora mt-8 bg-paper-white inline-block" style="font-style: italic;">
                Съставки:
            </h2>
            <ul class="ingredient-list">
                <li v-for="ingredient in recipe.ingredients" :key="ingredient.id" class="font-lora">
                    <span class="font-semibold">{{ ingredient.name }}</span>
                    — {{ ingredient.display_quantity }} {{ ingredient.display_unit }}
                </li>
            </ul>

            <!-- Instructions -->
            <h2 class="font-lora mt-8 bg-paper-white inline-block" style="font-style: italic;">
                Начин на приготвяне:
            </h2>
            <div class="font-lora mt-2 instructions-text" style="white-space: pre-line; text-align: justify;">
                {{ recipe.instructions }}
            </div>

            <!-- Back link -->
            <div class="mt-10">
                <Link :href="route('recipes.index')" class="font-montserrat text-sm text-text-muted hover:text-cherry-red no-underline">
                    ← Обратно към каталога
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.ingredient-list {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0;
}

.ingredient-list li {
    padding: 0.3rem 0;
    border-bottom: 1px solid #e1daca;
    line-height: 32px;
}

.ingredient-list li:last-child {
    border-bottom: none;
}

.instructions-text {
    line-height: 32px;
}
</style>
