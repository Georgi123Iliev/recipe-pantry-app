import { defineStore } from 'pinia';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export const usePantryStore = defineStore('pantry', () => {
    const items = ref([]);
    const debounceTimers = {};

    function hydrate(pantryItems) {
        items.value = pantryItems.map(item => ({
            id: item.id,
            ingredient_id: item.ingredient_id,
            quantity: item.quantity,
            ingredient: item.ingredient,
        }));
    }

    function syncItem(pantryItemId, quantity) {
        if (debounceTimers[pantryItemId]) {
            clearTimeout(debounceTimers[pantryItemId]);
        }

        debounceTimers[pantryItemId] = setTimeout(() => {
            router.patch(route('pantry.update', pantryItemId), {
                quantity,
            }, {
                preserveState: true,
                preserveScroll: true,
            });
            delete debounceTimers[pantryItemId];
        }, 500);
    }

    function increment(pantryItemId) {
        const item = items.value.find(i => i.id === pantryItemId);
        if (item) {
            item.quantity += 1;
            syncItem(pantryItemId, item.quantity);
        }
    }

    function decrement(pantryItemId) {
        const item = items.value.find(i => i.id === pantryItemId);
        if (item && item.quantity > 0) {
            item.quantity -= 1;
            if (item.quantity <= 0) {
                items.value = items.value.filter(i => i.id !== pantryItemId);
            }
            syncItem(pantryItemId, item.quantity);
        }
    }

    function addItem(ingredientId, quantity) {
        router.post(route('pantry.store'), {
            ingredient_id: ingredientId,
            quantity,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function removeItem(pantryItemId) {
        items.value = items.value.filter(i => i.id !== pantryItemId);
        router.delete(route('pantry.destroy', pantryItemId), {
            preserveState: true,
            preserveScroll: true,
        });
    }

    return { items, hydrate, increment, decrement, addItem, removeItem };
});
