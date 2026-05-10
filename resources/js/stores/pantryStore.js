import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const usePantryStore = defineStore('pantry', () => {
    const items = ref([]);

    // Computed list of ingredient IDs currently in the pantry.
    const ingredientIds = computed(() => items.value.map(i => i.ingredient_id));

    function hydrate(pantryItems) {
        items.value = pantryItems.map(item => ({
            id:              item.id,
            ingredient_id:   item.ingredient_id,
            ingredient_name: item.ingredient?.name ?? '',
            category_name:   item.ingredient?.category?.name ?? 'Други',
        }));
    }

    function addItem(pantryItem) {
        // Prevent client-side duplicates (server is the authoritative guard).
        if (ingredientIds.value.includes(pantryItem.ingredient_id)) return;
        items.value.push({
            id:              pantryItem.id,
            ingredient_id:   pantryItem.ingredient_id,
            ingredient_name: pantryItem.ingredient?.name ?? '',
            category_name:   pantryItem.ingredient?.category?.name ?? 'Други',
        });
    }

    function removeItem(pantryItemId) {
        items.value = items.value.filter(i => i.id !== pantryItemId);
    }

    return { items, ingredientIds, hydrate, addItem, removeItem };
});
