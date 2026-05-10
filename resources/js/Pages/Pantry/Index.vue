<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePantryStore } from '@/stores/pantryStore';
import { ref, computed, watch, onMounted } from 'vue';

const props = defineProps({
    pantry_items: Array,
    ingredients: Object,
    suggestions: Object,
});

const pantryStore = usePantryStore();

// Hydrate store on mount
onMounted(() => {
    pantryStore.hydrate(props.pantry_items);
});

// Watch for server-side prop changes (after Inertia navigations)
watch(() => props.pantry_items, (newItems) => {
    const serverIds = newItems.map(i => i.id).sort().join(',');
    const storeIds = pantryStore.items.map(i => i.id).sort().join(',');
    if (serverIds !== storeIds) {
        pantryStore.hydrate(newItems);
    }
}, { deep: true });

// Add item form state
const selectedIngredientId = ref('');
const addQuantity = ref('');

const addToPantry = () => {
    if (!selectedIngredientId.value || !addQuantity.value) return;
    pantryStore.addItem(selectedIngredientId.value, Number(addQuantity.value));
    selectedIngredientId.value = '';
    addQuantity.value = '';
};

// Group items by ingredient category
const groupedItems = computed(() => {
    const groups = {};
    for (const item of pantryStore.items) {
        const catName = item.ingredient?.category?.name || 'Други';
        if (!groups[catName]) groups[catName] = [];
        groups[catName].push(item);
    }
    return groups;
});

const ingredientsList = computed(() => props.ingredients?.data || []);
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
                        v-for="ing in ingredientsList"
                        :key="ing.id"
                        :value="ing.id"
                    >
                        {{ ing.name }} ({{ ing.base_unit }})
                    </option>
                </select>
                <input
                    v-model.number="addQuantity"
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="add-qty"
                    placeholder="Кол."
                />
                <button @click="addToPantry" class="btn-add" style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                    Добави в килера
                </button>
            </div>

            <!-- Empty State -->
            <div v-if="pantryStore.items.length === 0" class="text-center py-16">
                <p class="font-caveat text-text-muted" style="font-size: 2rem;">
                    Празен килер. Добави продукти!
                </p>
            </div>

            <!-- Pantry Items grouped by category -->
            <div v-else class="mt-6">
                <div v-for="(items, categoryName) in groupedItems" :key="categoryName" class="pantry-category">
                    <h2 class="category-title">{{ categoryName }}</h2>
                    <div class="pantry-items-list">
                        <div v-for="item in items" :key="item.id" class="pantry-item">
                            <div class="item-info">
                                <span class="item-name">{{ item.ingredient?.name }}</span>
                                <span class="item-unit">{{ item.ingredient?.base_unit }}</span>
                            </div>
                            <div class="item-controls">
                                <button class="qty-btn" @click="pantryStore.decrement(item.id)">−</button>
                                <span class="item-qty">{{ item.quantity }}</span>
                                <button class="qty-btn" @click="pantryStore.increment(item.id)">+</button>
                                <button class="remove-btn" @click="pantryStore.removeItem(item.id)" title="Премахни">✕</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suggestions -->
            <div v-if="suggestions && (suggestions.perfect.length > 0 || suggestions.partial.length > 0)" class="mt-12">
                <h2 class="font-lora text-text-dark bg-paper-white inline-block" style="font-size: 1.6rem; font-style: italic;">
                    Какво можеш да сготвиш?
                </h2>

                <!-- Perfect Matches -->
                <div v-if="suggestions.perfect.length > 0" class="mt-6">
                    <h3 class="suggestion-heading perfect">🟢 Перфектно съвпадение</h3>
                    <div class="suggestion-grid">
                        <Link
                            v-for="recipe in suggestions.perfect"
                            :key="recipe.id"
                            :href="route('recipes.show', recipe.id)"
                            class="suggestion-card"
                        >
                            <img
                                v-if="recipe.images && recipe.images.length > 0"
                                :src="recipe.images[0].url"
                                :alt="recipe.title"
                                class="suggestion-img"
                            />
                            <div class="suggestion-title">{{ recipe.title }}</div>
                        </Link>
                    </div>
                </div>

                <!-- Partial Matches -->
                <div v-if="suggestions.partial.length > 0" class="mt-6">
                    <h3 class="suggestion-heading partial">🟡 Почти готово</h3>
                    <div class="suggestion-grid">
                        <Link
                            v-for="recipe in suggestions.partial"
                            :key="recipe.id"
                            :href="route('recipes.show', recipe.id)"
                            class="suggestion-card"
                        >
                            <img
                                v-if="recipe.images && recipe.images.length > 0"
                                :src="recipe.images[0].url"
                                :alt="recipe.title"
                                class="suggestion-img"
                            />
                            <div class="suggestion-title">{{ recipe.title }}</div>
                            <span class="missing-badge">
                                Липсват {{ recipe.missing_ingredients_count }} съставки
                            </span>
                        </Link>
                    </div>
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

.add-select,
.add-qty {
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

.add-select {
    flex: 1;
    min-width: 180px;
}

.add-qty {
    width: 100px;
}

.add-select:focus,
.add-qty:focus {
    border-color: #C24641;
    box-shadow: 0 0 0 2px rgba(194, 70, 65, 0.15);
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

.item-info {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.item-name {
    font-family: 'Lora', serif;
    font-size: 1rem;
    font-weight: 600;
    color: #4E342E;
}

.item-unit {
    font-family: 'Montserrat', sans-serif;
    font-size: 0.75rem;
    color: #8D6E63;
}

.item-controls {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.qty-btn {
    width: 28px;
    height: 28px;
    border: 1px solid #8D6E63;
    background: #FFFDF8;
    color: #4E342E;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.15s;
    line-height: 1;
}

.qty-btn:hover {
    background-color: #e1daca;
}

.item-qty {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    font-size: 0.95rem;
    color: #4E342E;
    min-width: 50px;
    text-align: center;
}

.remove-btn {
    width: 28px;
    height: 28px;
    border: 1px solid #C24641;
    background: none;
    color: #C24641;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 0.5rem;
    transition: background-color 0.15s;
}

.remove-btn:hover {
    background-color: #C24641;
    color: white;
}

.suggestion-heading {
    font-family: 'Montserrat', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.75rem;
}

.suggestion-heading.perfect { color: #2E7D32; }
.suggestion-heading.partial { color: #F57F17; }

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

.missing-badge {
    display: inline-block;
    margin: 0 0.75rem 0.6rem;
    padding: 0.15rem 0.5rem;
    background-color: #FFF3E0;
    color: #E65100;
    font-family: 'Montserrat', sans-serif;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 3px;
}

@media (max-width: 600px) {
    .add-form {
        flex-direction: column;
    }

    .add-select,
    .add-qty {
        width: 100%;
    }

    .pantry-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>
