<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    recipe: {
        type: Object,
        required: true,
    },
});

const totalTime = (props.recipe.prep_time || 0) + (props.recipe.cook_time || 0);
</script>

<template>
    <Link :href="route('recipes.show', recipe.id)" class="recipe-row">
        <span v-if="totalTime" class="font-caveat text-cherry-red" style="font-size: 1.3rem; min-width: 70px;">
            {{ totalTime }} мин
        </span>
        <span v-else class="font-caveat text-text-muted" style="font-size: 1.3rem; min-width: 70px;">
            —
        </span>
        <span class="font-semibold text-text-dark font-lora">
            {{ recipe.title }}
        </span>
    </Link>
</template>
