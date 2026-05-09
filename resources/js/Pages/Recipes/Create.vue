<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecipeForm from '@/Components/RecipeForm.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    ingredients: Array,
});

const form = useForm({
    title: '',
    description: '',
    instructions: '',
    prep_time: '',
    cook_time: '',
    servings: '',
    ingredients: [{ ingredient_id: '', display_quantity: '', display_unit: '' }],
    images: [],
});

const submit = () => {
    form.post(route('recipes.store'), {
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Нова рецепта" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Нова рецепта
            </h1>

            <RecipeForm
                :form="form"
                :ingredients="ingredients"
                submit-label="Създай рецепта"
                @submit="submit"
            />
        </div>
    </AuthenticatedLayout>
</template>
