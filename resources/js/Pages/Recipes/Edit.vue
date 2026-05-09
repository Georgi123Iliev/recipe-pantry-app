<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecipeForm from '@/Components/RecipeForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    recipe: Object,
    ingredients: Array,
});

const form = useForm({
    _method: 'PUT',
    title: props.recipe.title,
    description: props.recipe.description || '',
    instructions: props.recipe.instructions,
    prep_time: props.recipe.prep_time,
    cook_time: props.recipe.cook_time,
    servings: props.recipe.servings,
    ingredients: props.recipe.ingredients.map(ing => ({
        ingredient_id: ing.id,
        display_quantity: ing.display_quantity,
        display_unit: ing.display_unit,
    })),
    keep_images: props.recipe.images?.map(img => img.id) || [],
    images: [],
});

const submit = () => {
    form.post(route('recipes.update', props.recipe.id), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="`Редакция: ${recipe.title}`" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Редакция на рецепта
            </h1>

            <RecipeForm
                :form="form"
                :ingredients="ingredients"
                :existing-images="recipe.images"
                :is-editing="true"
                submit-label="Обнови рецептата"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
