<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import StarRating from '@/Components/StarRating.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    recipe: Object,
    ratings: Object,
    userRating: Number,
    userReview: String,
});

const page = usePage();
const r = computed(() => props.recipe.data);

const canModify = computed(() => {
    if (!page.props.auth.user) return false;
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

// Rating modal
const showRatingModal = ref(false);

const ratingForm = useForm({
    value: props.userRating || 0,
    review: props.userReview || '',
});

const hasExistingRating = computed(() => props.userRating != null);

const submitRating = () => {
    ratingForm.post(route('recipes.rate', r.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showRatingModal.value = false;
        },
    });
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('bg-BG', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <Head :title="r.title" />

    <AuthenticatedLayout>
        <div class="notebook-page">
            <!-- Header -->
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <h1 class="font-lora text-text-dark bg-paper-white inline-block mt-0" style="font-size: 2.2rem; line-height: 1.2;">
                        {{ r.title }}
                    </h1>
                    <span v-if="r.ratings_count" class="font-caveat" style="font-size: 1.8rem; color: #DAA520; white-space: nowrap;">
                        ★ {{ r.ratings_avg }}
                    </span>
                </div>
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

            <!-- Meta info & Description -->
            <div class="notebook-section mt-4">
                <div class="font-caveat text-text-muted mt-2" style="font-size: 1.3rem;">
                    <span v-if="r.prep_time">Подготовка: {{ r.prep_time }} мин</span>
                    <span v-if="r.prep_time && r.cook_time"> · </span>
                    <span v-if="r.cook_time">Готвене: {{ r.cook_time }} мин</span>
                    <span v-if="r.servings"> · {{ r.servings }} порции</span>
                </div>
                <p class="text-sm text-text-muted font-montserrat mt-1">
                    от {{ r.user?.name }}
                </p>

                <p v-if="r.description" class="mt-4 font-lora" style="font-size: 1.1rem; text-align: justify;">
                    {{ r.description }}
                </p>
            </div>

            <!-- Images -->
            <div v-if="r.images && r.images.length > 0" class="mt-6">
                <div class="polaroid" style="width: 80%; margin: 2rem auto;">
                    <div class="tape"></div>
                    <img
                        :src="r.images[currentImageIndex].url"
                        :alt="r.title"
                        loading="lazy"
                        style="width: 100%; display: block; aspect-ratio: 4/3; object-fit: contain; background-color: #f9f9f9;"
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

            <!-- Ingredients + Ratings — side by side -->
            <div class="side-by-side mt-6">
                <!-- Ingredients (left) -->
                <div class="notebook-section">
                    <h2 class="font-lora bg-paper-white inline-block" style="font-style: italic; margin-top: 0;">
                        Съставки:
                    </h2>
                    <ul class="ingredient-list">
                        <li v-for="ingredient in r.ingredients" :key="ingredient.id" class="font-lora">
                            <span class="font-semibold">{{ ingredient.name }}</span>
                            — {{ ingredient.display_quantity }} {{ ingredient.display_unit }}
                        </li>
                    </ul>
                </div>

                <!-- Ratings (right) -->
                <div class="notebook-section">
                    <h2 class="font-lora bg-paper-white inline-block" style="font-style: italic; margin-top: 0;">
                        Оценки:
                    </h2>

                    <!-- Average summary -->
                    <div class="flex items-center gap-3 mt-2">
                        <StarRating :modelValue="Math.round(r.ratings_avg)" readonly />
                        <span class="font-caveat text-text-dark" style="font-size: 1.4rem;">
                            {{ r.ratings_avg }} / 5
                        </span>
                        <span class="font-montserrat text-text-muted text-sm">
                            ({{ r.ratings_count }} {{ r.ratings_count === 1 ? 'оценка' : 'оценки' }})
                        </span>
                    </div>

                    <!-- Rate button -->
                    <div class="mt-4">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="route('login')"
                            class="btn-add"
                        >
                            Оцени рецептата
                        </Link>
                        <button
                            v-else
                            @click="showRatingModal = true"
                            class="btn-add"
                        >
                            {{ hasExistingRating ? 'Промени оценката' : 'Оцени рецептата' }}
                        </button>
                    </div>

                    <!-- Reviews list -->
                    <div v-if="ratings.data && ratings.data.length > 0" class="mt-6">
                        <div
                            v-for="rating in ratings.data"
                            :key="rating.id"
                            class="rating-item"
                        >
                            <div class="flex items-center gap-3">
                                <span class="font-montserrat font-semibold text-sm text-text-dark">
                                    {{ rating.user.name }}
                                </span>
                                <span class="font-caveat" style="font-size: 1.3rem; color: #DAA520;">
                                    {{ '★'.repeat(rating.value) }}{{ '☆'.repeat(5 - rating.value) }}
                                </span>
                                <span class="font-montserrat text-text-muted text-xs ml-auto">
                                    {{ formatDate(rating.created_at) }}
                                </span>
                            </div>
                            <p v-if="rating.review" class="font-lora text-text-dark mt-1" style="font-size: 0.95rem; line-height: 1.6;">
                                {{ rating.review }}
                            </p>
                        </div>

                        <!-- Review Pagination -->
                        <div
                            v-if="ratings.meta && ratings.meta.links && ratings.meta.links.length > 3"
                            class="flex items-center justify-center gap-2 mt-6 font-montserrat text-sm"
                        >
                            <template v-for="link in ratings.meta.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1 rounded no-underline transition-colors duration-200"
                                    :class="link.active
                                        ? 'bg-cherry-red text-white'
                                        : 'text-text-dark hover:bg-bg-beige'"
                                    v-html="link.label"
                                    preserve-scroll
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
                    <p v-else class="font-caveat text-text-muted mt-4" style="font-size: 1.3rem;">
                        Все още няма оценки. Бъди първият!
                    </p>
                </div>
            </div>

            <!-- Instructions -->
            <div class="notebook-section mt-6">
                <h2 class="font-lora bg-paper-white inline-block" style="font-style: italic; margin-top: 0;">
                    Начин на приготвяне:
                </h2>
                <div class="font-lora mt-2 instructions-text" style="white-space: pre-line; text-align: justify;">
                    {{ r.instructions }}
                </div>
            </div>

            <!-- Rating Modal -->
            <Modal :show="showRatingModal" max-width="md" @close="showRatingModal = false">
                <div class="p-6" style="background-color: #FFFDF8;">
                    <h3 class="font-lora text-text-dark text-xl mb-4 border-b-2 border-dashed border-text-muted pb-2">
                        {{ hasExistingRating ? 'Промени оценката' : 'Оцени рецептата' }}
                    </h3>

                    <form @submit.prevent="submitRating">
                        <div class="flex flex-col items-center gap-2 mb-4">
                            <span class="font-montserrat text-text-muted text-sm uppercase tracking-wide">Твоята оценка</span>
                            <StarRating v-model="ratingForm.value" />
                        </div>

                        <div class="mb-4">
                            <label class="font-montserrat uppercase text-xs tracking-widest text-text-muted block mb-1">
                                Отзив (по желание)
                            </label>
                            <textarea
                                v-model="ratingForm.review"
                                rows="3"
                                class="rating-textarea"
                                placeholder="Сподели впечатленията си..."
                            ></textarea>
                        </div>

                        <p v-if="ratingForm.errors.value" class="text-cherry-red text-sm font-montserrat mb-2">
                            {{ ratingForm.errors.value }}
                        </p>
                        <p v-if="ratingForm.errors.review" class="text-cherry-red text-sm font-montserrat mb-2">
                            {{ ratingForm.errors.review }}
                        </p>

                        <div class="flex justify-end gap-3">
                            <button
                                type="button"
                                @click="showRatingModal = false"
                                class="btn-add"
                                style="background-color: #8D6E63; box-shadow: 2px 2px 0px #4E342E;"
                            >
                                Отказ
                            </button>
                            <button
                                type="submit"
                                class="btn-add"
                                style="background-color: #C24641; box-shadow: 2px 2px 0px #4E342E;"
                                :disabled="ratingForm.processing || ratingForm.value === 0"
                                :class="{ 'opacity-50 cursor-not-allowed': ratingForm.processing || ratingForm.value === 0 }"
                            >
                                {{ hasExistingRating ? 'Обнови' : 'Изпрати' }}
                            </button>
                        </div>
                    </form>
                </div>
            </Modal>

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
.side-by-side {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    align-items: start;
}

@media (max-width: 768px) {
    .side-by-side {
        grid-template-columns: 1fr;
    }
}

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

.rating-item {
    padding: 0.6rem 0;
    border-bottom: 1px solid #e1daca;
    line-height: 1.6;
}

.rating-item:last-child {
    border-bottom: none;
}

.rating-textarea {
    width: 100%;
    padding: 0.8rem;
    border: 1px solid #8D6E63;
    background-color: #FFFDF8;
    font-family: 'Caveat', cursive;
    font-size: 1.2rem;
    color: #4E342E;
    box-sizing: border-box;
    outline: none;
    resize: vertical;
    min-height: 80px;
}

.rating-textarea::placeholder {
    color: #bcaaa4;
}

.rating-textarea:focus {
    border-color: #C24641;
    box-shadow: 0 0 0 2px rgba(194, 70, 65, 0.15);
}
</style>
