<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePantryStore } from '@/stores/pantryStore';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    pantry_items: Array,
    ingredients:  Object,
    suggestions:  Array,
});

const pantryStore = usePantryStore();

// Simple hydration — no watch needed (no quantity write-backs to guard against).
onMounted(() => {
    pantryStore.hydrate(props.pantry_items);
});

// Add item form
const selectedIngredientId = ref('');
const addError = ref('');

const addToPantry = () => {
    if (!selectedIngredientId.value) return;
    addError.value = '';

    router.post(
        route('pantry.store'),
        { ingredient_id: selectedIngredientId.value },
        {
            preserveState:  true,
            preserveScroll: true,
            onSuccess: () => {
                // Re-hydrate from fresh server props after Inertia response.
                pantryStore.hydrate(props.pantry_items);
                selectedIngredientId.value = '';
            },
            onError: (errors) => {
                addError.value = errors.ingredient_id ?? 'Грешка при добавяне.';
            },
        }
    );
};

const removeFromPantry = (itemId) => {
    router.delete(
        route('pantry.destroy', itemId),
        {
            preserveState:  true,
            preserveScroll: true,
            onSuccess: () => {
                pantryStore.removeItem(itemId);
            },
        }
    );
};

// Group pantry items by category
const groupedItems = computed(() => {
    const groups = {};
    for (const item of pantryStore.items) {
        const cat = item.category_name || 'Други';
        if (!groups[cat]) groups[cat] = [];
        groups[cat].push(item);
    }
    return groups;
});

// Filter out already-added ingredients from the dropdown
const availableIngredients = computed(() => {
    const added = new Set(pantryStore.ingredientIds);
    return (props.ingredients?.data ?? []).filter(i => !added.has(i.id));
});

const isPerfectMatch = (recipe) => recipe.matched_count === recipe.total_ingredient_count;
</script>

<template>
    <Head title="Виртуален килер" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                Виртуален килер
            </h1>

            <!-- Add Item Form -->
            <div class="add-form">
                <select v-model="selectedIngredientId" class="add-select">
                    <option value="" disabled>Избери продукт</option>
                    <option
                        v-for="ing in availableIngredients"
                        :key="ing.id"
                        :value="ing.id"
                    >
                        {{ ing.name }}
                    </option>
                </select>
                <button @click="addToPantry" class="btn-add" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                    Добави в килера
                </button>
            </div>
            <p v-if="addError" class="add-error">{{ addError }}</p>

            <!-- Empty State -->
            <div v-if="pantryStore.items.length === 0" class="text-center py-16">
                <p class="font-caveat text-text-muted" style="font-size: 2rem;">
                    Празен килер — добави първата съставка!
                </p>
            </div>

            <!-- Pantry Items grouped by category -->
            <div v-else class="mt-6">
                <div v-for="(items, categoryName) in groupedItems" :key="categoryName" class="pantry-category">
                    <h2 class="category-title">{{ categoryName }}</h2>
                    <div class="pantry-items-list">
                        <div v-for="item in items" :key="item.id" class="pantry-item">
                            <span class="item-name">{{ item.ingredient_name }}</span>
                            <button
                                class="remove-btn"
                                @click="removeFromPantry(item.id)"
                                title="Премахни"
                            >✕ Премахни</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <div class="mt-12">
                <h2 class="font-lora text-text-dark bg-paper-white inline-block" style="font-size: 1.6rem; font-style: italic;">
                    Какво да сготвя?
                </h2>

                <div v-if="suggestions && suggestions.length > 0" class="suggestion-grid mt-4">
                    <Link
                        v-for="recipe in suggestions"
                        :key="recipe.id"
                        :href="route('recipes.show', recipe.id)"
                        class="suggestion-card"
                        :class="{ 'suggestion-card--perfect': isPerfectMatch(recipe) }"
                    >
                        <img
                            v-if="recipe.images && recipe.images.length > 0"
                            :src="recipe.images[0].url"
                            :alt="recipe.title"
                            class="suggestion-img"
                            loading="lazy"
                        />
                        <div class="suggestion-title">{{ recipe.title }}</div>
                        <div class="match-badge" :class="isPerfectMatch(recipe) ? 'match-badge--perfect' : 'match-badge--partial'">
                            <span v-if="isPerfectMatch(recipe)">✓ Готово за готвене!</span>
                            <span v-else>{{ recipe.matched_count }}/{{ recipe.total_ingredient_count }} съставки</span>
                        </div>
                    </Link>
                </div>

                <div v-else class="text-center py-8">
                    <p class="font-caveat text-text-muted" style="font-size: 1.5rem;">
                        Добави съставки в килера, за да видиш рецепти!
                    </p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.add-form {
    display: flex;
    gap: 0.75rem;
    align-items: end;
    flex-wrap: wrap;
    margin-top: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px dashed #e1daca;
}

.add-select {
    flex: 1;
    min-width: 180px;
    padding: 0.6rem 0.8rem;
    border: 1px solid #8D6E63;
    background-color: #FFFDF8;
    font-family: 'Lora', serif;
    font-size: 0.95rem;
    color: #4E342E;
    border-radius: 4px;
    outline: none;
    box-sizing: border-box;
}

.add-select:focus {
    border-color: #C24641;
    box-shadow: 0 0 0 2px rgba(194, 70, 65, 0.15);
}

.add-error {
    margin-top: 0.4rem;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.8rem;
    color: #C24641;
}

.pantry-category {
    margin-bottom: 2rem;
}

.category-title {
    font-family: 'Caveat', cursive;
    font-size: 1.5rem;
    color: #8D6E63;
    border-bottom: 2px solid #e1daca;
    padding-bottom: 0.3rem;
    margin-bottom: 0.75rem;
}

.pantry-items-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.pantry-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.6rem 0.9rem;
    background-color: #FFFDF8;
    border: 1px solid #e1daca;
    border-radius: 4px;
    transition: box-shadow 0.2s;
}

.pantry-item:hover {
    box-shadow: 0 1px 4px rgba(78, 52, 46, 0.1);
}

.item-name {
    font-family: 'Lora', serif;
    font-size: 1rem;
    font-weight: 600;
    color: #4E342E;
}

.remove-btn {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.7rem;
    border: 1px solid #C24641;
    background: none;
    color: #C24641;
    border-radius: 4px;
    cursor: pointer;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.78rem;
    font-weight: 600;
    transition: background-color 0.15s, color 0.15s;
}

.remove-btn:hover {
    background-color: #C24641;
    color: white;
}

/* Suggestion grid */
.suggestion-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}

.suggestion-card {
    display: block;
    background: #FFFDF8;
    border: 1px solid #e1daca;
    border-radius: 4px;
    overflow: hidden;
    text-decoration: none;
    transition: box-shadow 0.2s, transform 0.2s;
}

.suggestion-card:hover {
    box-shadow: 0 3px 10px rgba(78, 52, 46, 0.15);
    transform: translateY(-2px);
}

.suggestion-card--perfect {
    border-color: #2E7D32;
    box-shadow: 0 0 0 1px #2E7D3240;
}

.suggestion-img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    display: block;
}

.suggestion-title {
    padding: 0.6rem 0.75rem 0.3rem;
    font-family: 'Lora', serif;
    font-size: 0.95rem;
    color: #4E342E;
    font-weight: 600;
}

.match-badge {
    display: inline-block;
    margin: 0 0.75rem 0.6rem;
    padding: 0.2rem 0.6rem;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.72rem;
    font-weight: 700;
    border-radius: 3px;
}

.match-badge--perfect {
    background-color: #E8F5E9;
    color: #2E7D32;
}

.match-badge--partial {
    background-color: #FFF3E0;
    color: #E65100;
}

@media (max-width: 600px) {
    .add-form {
        flex-direction: column;
    }

    .add-select {
        width: 100%;
    }

    .pantry-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
