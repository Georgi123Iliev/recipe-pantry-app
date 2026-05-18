<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RecipePolaroid from '@/Components/RecipePolaroid.vue';
import RecipeRow from '@/Components/RecipeRow.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    recipes: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const performSearch = () => {
    router.get(route('recipes.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Каталог" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Каталог с рецепти
            </h1>

            <!-- Search Bar -->
            <div class="mt-4 mb-6">
                <input
                    v-model="search"
                    type="text"
                    class="search-input"
                    placeholder="Търси рецепта или съставка..."
                    @keyup.enter="performSearch"
                />
            </div>

            <!-- Empty State -->
            <div
                v-if="!recipes.data || recipes.data.length === 0"
                class="text-center py-16"
            >
                <p class="font-caveat text-text-muted" style="font-size: 2rem;">
                    Тетрадката е празна. Добави първата рецепта!
                </p>
                <Link v-if="$page.props.auth.user" :href="route('recipes.create')" class="btn-add mt-4 inline-block">
                    + Нова Рецепта
                </Link>
            </div>

            <!-- Recipe Grid -->
            <div v-else>
                <div class="recipe-grid">
                    <template v-for="recipe in recipes.data" :key="recipe.id">
                        <RecipePolaroid
                            v-if="recipe.images && recipe.images.length > 0"
                            :recipe="recipe"
                        />
                        <RecipeRow
                            v-else
                            :recipe="recipe"
                        />
                    </template>
                </div>

                <!-- Pagination -->
                <div
                    v-if="recipes.meta && recipes.meta.links && recipes.meta.links.length > 3"
                    class="flex items-center justify-center gap-2 mt-12 font-montserrat text-sm"
                >
                    <template v-for="link in recipes.meta.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-3 py-1 rounded no-underline transition-colors duration-200"
                            :class="link.active
                                ? 'bg-cherry-red text-white'
                                : 'text-text-dark hover:bg-bg-beige'"
                            v-html="link.label"
                            preserve-state
                        />
                        <span
                            v-else
                            class="px-3 py-1 text-text-muted"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.recipe-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2.5rem;
    margin-top: 2rem;
}

@media (max-width: 600px) {
    .recipe-grid {
        grid-template-columns: 1fr;
    }
}
</style>
