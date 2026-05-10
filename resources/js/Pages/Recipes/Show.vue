<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    recipe: Object,
});

const page = usePage();
const r = computed(() => props.recipe.data);

const canModify = computed(() => {
    return r.value.user_id === page.props.auth.user.id || page.props.auth.user.is_admin;
});

const deleteRecipe = () => {
    if (confirm('Сигурни ли сте, че искате да изтриете тази рецепта?')) {
        router.delete(route('recipes.destroy', r.value.id));
    }
};

const currentImageIndex = ref(0);

const nextImage = () => {
    if (r.value.images && r.value.images.length > 0) {
        currentImageIndex.value = (currentImageIndex.value + 1) % r.value.images.length;
    }
};

const prevImage = () => {
    if (r.value.images && r.value.images.length > 0) {
        currentImageIndex.value = (currentImageIndex.value - 1 + r.value.images.length) % r.value.images.length;
    }
};
</script>

<template>
    <Head :title="r.title" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <!-- Header -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                    {{ r.title }}
                </h1>
                <div v-if="canModify" class="flex gap-2 mt-1">
                    <Link :href="route('recipes.edit', r.id)" class="btn-add" style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
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
                <span v-if="r.prep_time">Подготовка: {{ r.prep_time }} мин</span>
                <span v-if="r.prep_time && r.cook_time"> · </span>
                <span v-if="r.cook_time">Готвене: {{ r.cook_time }} мин</span>
                <span v-if="r.servings"> · {{ r.servings }} порции</span>
            </div>
            <p class="text-sm text-text-muted font-montserrat mt-1">
                от {{ r.user?.name }}
            </p>

            <!-- Description -->
            <p v-if="r.description" class="mt-4 font-lora" style="font-size: 1.1rem; text-align: justify;">
                {{ r.description }}
            </p>

            <!-- Images -->
            <div v-if="r.images && r.images.length > 0" class="mt-6">
                <div class="polaroid" style="width: 80%; margin: 2rem auto;">
                    <div class="tape"></div>
                    <img
                        :src="r.images[currentImageIndex].url"
                        :alt="r.title"
                        loading="lazy"
                        style="width: 100%; display: block; aspect-ratio: 4/3; object-fit: cover;"
                    />
                    
                    <div v-if="r.images.length > 1" class="flex justify-between items-center mt-6 px-2">
                        <button @click="prevImage" class="btn-add" style="padding: 0.3rem 0.8rem; font-size: 0.85rem;">
                            &larr; Предишна
                        </button>
                        <span class="font-caveat text-xl text-text-dark font-bold">
                            {{ currentImageIndex + 1 }} / {{ r.images.length }}
                        </span>
                        <button @click="nextImage" class="btn-add" style="padding: 0.3rem 0.8rem; font-size: 0.85rem;">
                            Следваща &rarr;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ingredients -->
            <h2 class="font-lora mt-8 bg-paper-white inline-block" style="font-style: italic;">
                Съставки:
            </h2>
            <ul class="ingredient-list">
                <li v-for="ingredient in r.ingredients" :key="ingredient.id" class="font-lora">
                    <span class="font-semibold">{{ ingredient.name }}</span>
                    — {{ ingredient.display_quantity }} {{ ingredient.display_unit }}
                </li>
            </ul>

            <!-- Instructions -->
            <h2 class="font-lora mt-8 bg-paper-white inline-block" style="font-style: italic;">
                Начин на приготвяне:
            </h2>
            <div class="font-lora mt-2 instructions-text" style="white-space: pre-line; text-align: justify;">
                {{ r.instructions }}
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
