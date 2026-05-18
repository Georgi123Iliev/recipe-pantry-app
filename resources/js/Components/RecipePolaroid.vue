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
    <Link :href="route('recipes.show', recipe.id)" class="block no-underline">
        <div class="polaroid">
            <div class="tape"></div>
            <div class="overflow-hidden" style="aspect-ratio: 4/3;">
                <img
                    :src="recipe.images[0].url"
                    :alt="recipe.title"
                    loading="lazy"
                    class="w-full h-full object-contain bg-[#f9f9f9]"
                />
            </div>
            <div class="polaroid-caption">
                <span>{{ recipe.title }}</span>
                <span
                    v-if="totalTime"
                    class="text-cherry-red text-base ml-1"
                >
                    · {{ totalTime }} мин
                </span>
                <span
                    v-if="recipe.ratings_count"
                    class="text-base ml-1"
                    style="color: #DAA520;"
                >
                    · ★ {{ recipe.ratings_avg }}
                </span>
            </div>
        </div>
    </Link>
</template>
