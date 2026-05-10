<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecipeForm from '@/Components/RecipeForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
    ingredients: Object,
});

const r = props.recipe.data;

const form = useForm({
    _method: 'PUT',
    title: r.title,
    description: r.description || '',
    instructions: r.instructions,
    prep_time: r.prep_time,
    cook_time: r.cook_time,
    servings: r.servings,
    ingredients: r.ingredients.map(ing => ({
        ingredient_id: ing.id,
        display_quantity: ing.display_quantity,
        display_unit: ing.display_unit,
    })),
    keep_images: r.images?.map(img => img.id) || [],
    images: [],
});

const submit = () => {
    form.post(route('recipes.update', r.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Редакция: ${recipe.data.title}`" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Редакция на рецепта
            </h1>

            <RecipeForm
                :form="form"
                :ingredients="ingredients.data"
                :existing-images="recipe.data.images"
                :is-editing="true"
                submit-label="Обнови рецептата"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
